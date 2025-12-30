<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $activities = Activity::with(['causer', 'subject'])
            ->latest()
            ->paginate(20);

        return view('audit.index', compact('activities'));
    }
}