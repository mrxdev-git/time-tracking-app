@props(['totals'])
<x-ui.table label="Totals per employee">
    <thead>
        <tr>
            <th scope="col">Employee</th>
            <th scope="col" class="numeric">Entries</th>
            <th scope="col" class="numeric">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($totals as $employeeTotal)
            <tr>
                <th scope="row" class="wrap">{{ $employeeTotal->employee->name }}</th>
                <td class="numeric">{{ $employeeTotal->entryCount }}</td>
                <td class="numeric">{{ $employeeTotal->total->format() }}</td>
            </tr>
        @endforeach
    </tbody>
</x-ui.table>
