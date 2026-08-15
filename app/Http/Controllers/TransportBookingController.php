<?php

namespace App\Http\Controllers;

use App\Models\TransportBooking;
use App\Models\TransportPickupEvent;
use App\Services\Messaging\TransportNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class TransportBookingController extends Controller
{
    public function index(): View
    {
        $events = TransportPickupEvent::query()
            ->openForBooking()
            ->withCount('bookings')
            ->orderBy('pickup_date')
            ->orderBy('pickup_start_time')
            ->get();

        return view('transport.index', compact('events'));
    }

    public function create(TransportPickupEvent $pickupEvent): View
    {
        $this->ensureBookable($pickupEvent);
        $pickupEvent->load('bookings');

        return view('transport.book', [
            'pickupEvent' => $pickupEvent,
            'slots' => $pickupEvent->availableSlots(),
            'maxPartySize' => max(1, (int) config('transport.booking.max_party_size', 10)),
        ]);
    }

    public function store(
        Request $request,
        TransportPickupEvent $pickupEvent,
        TransportNotificationService $notificationService,
    ): RedirectResponse {
        $this->ensureBookable($pickupEvent);
        $pickupEvent->load('bookings');

        $slotValues = $pickupEvent->availableSlots()->pluck('value')->all();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'phone' => ['required', 'string', 'max:191'],
            'address' => ['required', 'string', 'max:500'],
            'pickup_time' => ['required', Rule::in($slotValues)],
            'party_size' => [
                'required',
                'integer',
                'min:1',
                'max:' . max(1, (int) config('transport.booking.max_party_size', 10)),
            ],
        ]);

        $booking = DB::transaction(function () use ($pickupEvent, $validated): TransportBooking {
            $lockedEvent = TransportPickupEvent::query()
                ->lockForUpdate()
                ->findOrFail($pickupEvent->id);

            $this->ensureBookable($lockedEvent);
            $lockedEvent->load('bookings');

            $slot = $lockedEvent->availableSlots()
                ->firstWhere('value', $validated['pickup_time']);

            if (! $slot) {
                throw ValidationException::withMessages([
                    'pickup_time' => 'That pickup time is no longer available.',
                ]);
            }

            if ((int) $validated['party_size'] > (int) $slot['remaining']) {
                throw ValidationException::withMessages([
                    'party_size' => sprintf(
                        'Only %d seat(s) remain for %s. Please choose a smaller party or another time.',
                        (int) $slot['remaining'],
                        $slot['label'],
                    ),
                ]);
            }

            return TransportBooking::query()->create([
                'transport_pickup_event_id' => $lockedEvent->id,
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'pickup_time' => $validated['pickup_time'],
                'party_size' => $validated['party_size'],
                'status' => TransportBooking::STATUS_CONFIRMED,
            ]);
        });

        Log::info('Fresh Fountain transport booking created.', [
            'booking_id' => $booking->id,
            'pickup_event_id' => $booking->transport_pickup_event_id,
            'pickup_time' => $booking->pickup_time,
            'party_size' => $booking->party_size,
        ]);

        try {
            $notificationService->notifyNewBooking(
                $booking->fresh(['pickupEvent']) ?? $booking
            );
        } catch (Throwable $e) {
            Log::error('Transport booking saved, but messaging dispatch failed.', [
                'booking_id' => $booking->id,
                'message' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('transport.confirmation')
            ->with('transport_booking_id', $booking->id);
    }

    public function confirmation(Request $request): View|RedirectResponse
    {
        $bookingId = $request->session()->get('transport_booking_id');

        if (! $bookingId) {
            return redirect()->route('transport.index');
        }

        $booking = TransportBooking::query()
            ->with('pickupEvent')
            ->findOrFail($bookingId);

        return view('transport.confirmation', compact('booking'));
    }

    private function ensureBookable(TransportPickupEvent $pickupEvent): void
    {
        abort_unless(
            $pickupEvent->bookings_open
            && ! $pickupEvent->pickup_date->lt(today()),
            404
        );
    }
}
