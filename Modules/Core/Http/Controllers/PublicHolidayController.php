<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\PublicHoliday;

class PublicHolidayController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) ($request->get('year') ?? now()->year);
        $yearStart = $year . '-01-01';
        $yearEnd = $year . '-12-31';
        $holidays = PublicHoliday::where('date', '<=', $yearEnd)
            ->where(function ($q) use ($yearStart) {
                $q->whereNotNull('end_date')->where('end_date', '>=', $yearStart)
                    ->orWhereNull('end_date')->where('date', '>=', $yearStart);
            })
            ->orderBy('date')
            ->get();
        return view('core::holidays.index', compact('holidays', 'year'));
    }

    public function create()
    {
        return view('core::holidays.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'country_code' => 'nullable|string|max:10',
        ]);
        PublicHoliday::create($request->only('name', 'date', 'end_date', 'country_code'));
        return redirect()->route('holidays.index')->with('success', 'Public holiday added.');
    }

    public function edit(PublicHoliday $holiday)
    {
        return view('core::holidays.edit', compact('holiday'));
    }

    public function update(Request $request, PublicHoliday $holiday)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'country_code' => 'nullable|string|max:10',
        ]);
        $holiday->update($request->only('name', 'date', 'end_date', 'country_code'));
        return redirect()->route('holidays.index')->with('success', 'Public holiday updated.');
    }

    public function destroy(PublicHoliday $holiday)
    {
        $holiday->delete();
        return redirect()->route('holidays.index')->with('success', 'Public holiday deleted.');
    }
}
