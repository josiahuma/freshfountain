<?php

namespace App\Http\Controllers;

use App\Models\ChurchUnit;
use App\Services\UnitRequestService;
use Illuminate\Http\Request;

class UnitRequestController extends Controller
{
    public function create(string $slug)
    {
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
    ) {
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
                'email',
                'max:255',
            ],

            'mobile_number' => [
                'required',
                'string',
                'max:30',
            ],

            'message' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $service->submit(
            $churchUnit,
            $validated
        );

        return redirect()->route(
            'church-units.success',
            $churchUnit->slug
            )
            ->with(
                'success',
                'Your request has been submitted successfully.'
            );
    }

    public function success(string $slug)
    {
        $churchUnit = ChurchUnit::query()
            ->where('slug', $slug)
            ->firstOrFail();

        return view(
            'church-units.success',
            compact('churchUnit')
        );
    }
}