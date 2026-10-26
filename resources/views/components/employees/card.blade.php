@props(['employee', 'now'])
<li @class(['panel', 'employee', 'employee-running' => $employee->runningEntry !== null])>
    <h2 class="employee-name">{{ $employee->name }}</h2>
    @if ($employee->runningEntry)
        <x-timers.running :employee="$employee" :entry="$employee->runningEntry" :now="$now" />
    @else
        <x-timers.start-form :employee="$employee" />
    @endif
</li>
