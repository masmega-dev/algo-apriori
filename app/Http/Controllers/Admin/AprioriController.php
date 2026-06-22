<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RunAprioriRequest;
use App\Models\AprioriAnalysisRun;
use App\Services\AprioriService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AprioriController extends Controller
{
    public function index(): Response { return Inertia::render('admin/reports', ['runs' => AprioriAnalysisRun::query()->withCount('rules')->latest('executed_at')->paginate(20)]); }
    public function store(RunAprioriRequest $request, AprioriService $service): RedirectResponse { $run = $service->run($request->user()->id, now()->parse($request->validated('date_from')), now()->parse($request->validated('date_to')), (float) $request->validated('minimum_support'), (float) $request->validated('minimum_confidence'), (float) $request->validated('minimum_lift', 0)); return to_route('admin.apriori.show', $run)->with('success', 'Analisis Apriori selesai dijalankan.'); }
    public function show(AprioriAnalysisRun $aprioriAnalysisRun): Response { return Inertia::render('admin/apriori-detail', ['run' => $aprioriAnalysisRun->load('rules.items')]); }
}
