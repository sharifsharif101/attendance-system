<?php

namespace App\Http\Controllers;

use App\Models\OfficialHoliday;
use Illuminate\Http\Request;

class OfficialHolidayController extends Controller
{
    /**
     * Display a listing of the holidays.
     */
    public function index()
    {
        // Get current year holidays by default, or all
        $holidays = OfficialHoliday::orderBy('date', 'desc')->paginate(20);
        return view('settings.holidays.index', compact('holidays'));
    }

    /**
     * Store a newly created holiday in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:official_holidays,date',
            'description' => 'required|string|max:255',
        ], [
            'date.unique' => 'هذا التاريخ مسجل مسبقاً كعطلة.',
        ]);

        OfficialHoliday::create($request->only(['date', 'description']));

        return redirect()->route('holidays.index')
            ->with('success', 'تم إضافة العطلة بنجاح');
    }

    /**
     * Remove the specified holiday from storage.
     */
    public function destroy(OfficialHoliday $holiday)
    {
        $holiday->delete();

        return redirect()->route('holidays.index')
            ->with('success', 'تم حذف العطلة بنجاح');
    }
}
