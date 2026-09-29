<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Services\TeamPerformanceReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request, TeamPerformanceReport $report): View
    {
        $user = $request->user();

        $year = (int) $request->integer('year', now()->year);
        $month = (int) $request->integer('month', now()->month);

        $teamId = null;

        if ($user->isSupervisor()) {
            $teamId = Team::query()->where('supervisor_id', $user->id)->value('id');
        }

        $teams = $report->generate($year, $month, $teamId);

        return view('reports.index', [
            'teams' => $teams,
            'year' => $year,
            'month' => $month,
        ]);
    }
}
