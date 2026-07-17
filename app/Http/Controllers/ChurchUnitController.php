<?php

namespace App\Http\Controllers;

use App\Models\ChurchUnit;

class ChurchUnitController extends Controller
{
    public function index()
    {
        $units = ChurchUnit::query()
            ->active()
            ->ordered()
            ->withCount([
                'leaders',
                'members',
            ])
            ->get();

        return view('church-units.index', compact('units'));
    }

    public function show(string $slug)
    {
        $unit = ChurchUnit::query()
            ->active()
            ->where('slug', $slug)
            ->with([
                'leaders',
            ])
            ->withCount('members')
            ->firstOrFail();

        return view('church-units.show', compact('unit'));
    }
}