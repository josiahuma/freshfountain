<?php

namespace App\Http\Controllers;

use App\Models\ChurchUnit;
use App\Services\UnitRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitRequestController extends Controller
{
    public function create(
        string $slug
    ): View {
        $churchUnit = ChurchUnit::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        return view(
            'church-units.request',
            compact('churchUnit')
        );
    }

    public function store(
        Request $request,
        string $slug,
        UnitRequestService $service
    ): RedirectResponse {
        $churchUnit = ChurchUnit::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
            ],

            'mobile_number' => [
                'required',
                'string',
                'min:7',
                'max:30',
                'regex:/^[0-9+\s().-]+$/',
            ],

            'message' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'mobile_number.regex' =>
                'Please enter a valid telephone number.',
        ]);

        $service->submit(
            $churchUnit,
            $validated
        );

        return redirect()
            ->route(
                'church-units.success',
                $churchUnit->slug
            )
            ->with(
                'success',
                'Your request has been submitted successfully.'
            );
    }

    public function success(
        string $slug
    ): View {
        $churchUnit = ChurchUnit::query()
            ->where('slug', $slug)
            ->firstOrFail();

        return view(
            'church-units.success',
            compact('churchUnit')
        );
    }
}