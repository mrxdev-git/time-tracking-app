@extends('layouts.app')

@section('title', 'Employees')

@section('content')
    <h1>Employees</h1>
    <x-employees.create-form />
    @if ($employees->isEmpty())
        <x-employees.empty-state />
    @else
        <ul class="employee-list" role="list">
            @foreach ($employees as $employee)
                <x-employees.card :employee="$employee" :now="$now" />
            @endforeach
        </ul>
    @endif
@endsection
