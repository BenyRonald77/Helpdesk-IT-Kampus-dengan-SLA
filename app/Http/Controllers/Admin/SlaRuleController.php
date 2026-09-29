<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SlaRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlaRuleController extends Controller
{
    public function index(): View
    {
        $rules = SlaRule::query()->get()->sortBy(fn ($rule) => match ($rule->priority->value) {
            'critical' => 0,
            'high' => 1,
            'medium' => 2,
            'low' => 3,
            default => 4,
        })->values();

        return view('admin.sla-rules.index', compact('rules'));
    }

    public function update(Request $request, SlaRule $slaRule): RedirectResponse
    {
        $data = $request->validate([
            'response_minutes' => ['required', 'integer', 'min:1', 'max:100000'],
            'resolution_minutes' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        $slaRule->update($data);

        return redirect()->route('admin.sla-rules.index')
            ->with('status', "Aturan SLA prioritas {$slaRule->priority->label()} diperbarui. Tiket baru atau yang berganti prioritas akan memakai target baru; tiket lama tidak berubah.");
    }
}
