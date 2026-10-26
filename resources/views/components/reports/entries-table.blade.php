@props(['entries', 'now'])
<x-ui.table label="Time entries">
    <thead>
        <tr>
            <th scope="col">Employee</th>
            <th scope="col">Description</th>
            <th scope="col">Started (UTC)</th>
            <th scope="col">Stopped (UTC)</th>
            <th scope="col" class="numeric">Duration</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($entries as $entry)
            <tr>
                <td class="wrap">{{ $entry->employee->name }}</td>
                <td class="wrap">{{ $entry->description }}</td>
                <td><x-ui.time :at="$entry->started_at" /></td>
                <td>
                    @if ($entry->isRunning())
                        <span class="badge">running</span>
                    @else
                        <x-ui.time :at="$entry->stopped_at" />
                    @endif
                </td>
                <td class="numeric">{{ $entry->duration($now)->format() }}</td>
            </tr>
        @endforeach
    </tbody>
</x-ui.table>
