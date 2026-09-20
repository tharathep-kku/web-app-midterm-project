<?php

namespace App\Http\Controllers;

use App\Models\ReturnUnit;
use Illuminate\View\View;

class ReturnUnitController extends Controller
{
    public function dashboard(): View
    {
        $returnUnits = ReturnUnit::withCount('items')->get();

        $returnUnitsForMap = $returnUnits->map(fn (ReturnUnit $unit) => [
            'id' => $unit->id,
            'name' => $unit->name,
            'description' => $unit->description,
            'lat' => (float) $unit->latitude,
            'lng' => (float) $unit->longitude,
            'items_count' => $unit->items_count,
            'show_url' => route('return-units.show', $unit->id),
        ])->values();

        return view('dashboard', compact('returnUnits', 'returnUnitsForMap'));
    }

    public function show(ReturnUnit $returnUnit): View
    {
        $returnUnit->load(['items' => function ($query) {
            $query->with('category')->orderBy('event_date', 'desc');
        }]);

        $itemsByStatus = $returnUnit->items->groupBy('status');

        return view('return-unit', compact('returnUnit', 'itemsByStatus'));
    }
}
