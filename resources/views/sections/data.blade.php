
{{-- TABLE --}}
<div class="overflow-x-auto bg-white shadow-md rounded-lg">
    <table class="table table-zebra w-full">
        <thead>
            <tr>
                <th><input type="checkbox" class="checkbox" /></th>
                <th>Date</th>
                <th>Building</th>

                <th class="text-center col-residual">Residual (kg)</th>
                <th class="text-center col-recyclable">Recyclable (kg)</th>
                <th class="text-center col-biodegradable">Biodegradable (kg)</th>
                <th class="text-center col-infectious">Infectious (kg)</th>

                <th class="text-center font-bold col-total">Total (kg)</th>
                <th class="text-right">Delete</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($wastes as $waste)
            @php
                $totalWeight =
                    $waste->residual +
                    $waste->recyclable +
                    $waste->biodegradable +
                    $waste->infectious;
            @endphp
                <tr>
                    <td><input type="checkbox" class="checkbox" /></td>
                    <td>{{ Carbon\Carbon::parse($waste->date)->format('M d, Y') }}</td>
                    <td>{{ $waste->building->name }}</td>

                    <td class="text-center col-residual">{{ number_format($waste->residual, 2) }}</td>
                    <td class="text-center col-recyclable">{{ number_format($waste->recyclable, 2) }}</td>
                    <td class="text-center col-biodegradable">{{ number_format($waste->biodegradable, 2) }}</td>
                    <td class="text-center col-infectious">{{ number_format($waste->infectious, 2) }}</td>
                    <td class="text-center font-bold col-total">{{ number_format($totalWeight, 2) }}</td>

                    <td class="text-right">
                        <form method="POST" action="{{ route('waste.destroy', $waste) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Delete this entry?')"
                                class="text-red-600 text-xl font-bold hover:text-red-800">
                                🗑
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


{{-- PAGINATION --}}
<div class="flex justify-between items-center mt-4 text-sm text-gray-600">
    <div>
        Rows per page:
        <form method="GET" action="{{ route('dashboard') }}" class="inline-block">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="date" value="{{ request('date') }}">
            <select name="per_page" onchange="this.form.submit()"
                class="select select-bordered select-sm w-20">
                <option value="20" @selected(request('per_page', 20) == 20)>20</option>
                <option value="50" @selected(request('per_page') == 50)>50</option>
                <option value="100" @selected(request('per_page') == 100)>100</option>
            </select>
        </form>
    </div>

    <div>
        {{ $wastes->links('pagination::tailwind') }}
    </div>
</div>
