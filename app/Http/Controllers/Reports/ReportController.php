<?php

declare(strict_types=1);

namespace App\Http\Controllers\Reports;

use App\Domain\Reports\PeriodReportQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\ShowReportRequest;
use Illuminate\Contracts\View\View;

final class ReportController extends Controller
{
    public function show(ShowReportRequest $request, PeriodReportQuery $reports): View
    {
        $period = $request->period();

        return view('reports.show', [
            'from' => $request->dateInput('from'),
            'to' => $request->dateInput('to'),
            'report' => $period === null ? null : $reports->run($period),
        ])->withErrors($request->validationErrors());
    }
}
