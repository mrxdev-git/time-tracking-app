@extends('layouts.app')

@section('title', 'Report')

@section('content')
    <h1>Report</h1>
    <x-reports.period-form :from="$from" :to="$to" :from-invalid="$errors->has('from')" :to-invalid="$errors->has('to')" />
    @if ($report)
        <p class="report-meta">
            From {{ $report->period->from->toDateString() }} to {{ $report->period->to->toDateString() }} (inclusive), generated at <x-ui.time :at="$report->generatedAt" /> UTC.
        </p>
        <section class="report-section" aria-labelledby="totals-heading">
            <h2 id="totals-heading">Totals</h2>
            @if ($report->totals === [])
                <x-employees.empty-state />
            @else
                <x-reports.totals-table :totals="$report->totals" />
            @endif
        </section>
        <section class="report-section" aria-labelledby="entries-heading">
            <h2 id="entries-heading">Entries</h2>
            @if ($report->isEmpty())
                <x-reports.empty-state :period="$report->period" />
            @else
                <x-reports.entries-table :entries="$report->entries" :now="$report->generatedAt" />
            @endif
        </section>
        <p class="footnote">All times are UTC. An entry counts toward the day it started; a running entry counts up to the generation time.</p>
    @endif
@endsection
