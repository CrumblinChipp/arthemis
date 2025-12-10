<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">

        {{-- Date Quick Picker Button --}}
        <button class="btn btn-primary btn-md" onclick="document.getElementById('date-filter').focus()">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar">
                <rect width="18" height="18" x="3" y="4" rx="2" />
                <line x1="16" x2="16" y1="2" y2="6" />
                <line x1="8" x2="8" y1="2" y2="6" />
                <line x1="3" x2="21" y1="10" y2="10" />
            </svg>
        </button>

        {{-- Toggle Column Visibility --}}
        <div class="dropdown">
            <label tabindex="0" class="btn">Toggle Columns</label>
            <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">

                {{-- JS toggles column by class name --}}
                <li><a onclick="toggleColumn('col-residual')">Residual</a></li>
                <li><a onclick="toggleColumn('col-recyclable')">Recyclable</a></li>
                <li><a onclick="toggleColumn('col-biodegradable')">Biodegradable</a></li>
                <li><a onclick="toggleColumn('col-infectious')">Infectious</a></li>

                <div class="divider my-1"></div>
                <li><a onclick="showAllColumns()">Show All</a></li>
            </ul>
        </div>

        {{-- wastes Type Filter --}}
        <div class="dropdown">
            <label tabindex="0" class="btn">wastes Type Filters</label>
            <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                <li><a href="?wastes_type=residual">Residual</a></li>
                <li><a href="?wastes_type=recyclable">Recyclable</a></li>
                <li><a href="?wastes_type=biodegradable">Biodegradable</a></li>
                <li><a href="?wastes_type=infectious">Infectious</a></li>
                <div class="divider my-0"></div>
                <li><a href="{{ route('dashboard') }}">Show All</a></li>
            </ul>
        </div>

        {{-- Search + Date + Submit --}}
        <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2">
            <input type="date" id="date-filter" name="date"
                value="{{ request('date') }}"
                class="input input-bordered w-40" />

            <input type="text" placeholder="Search Building" name="search"
                value="{{ request('search') }}"
                class="input input-bordered w-60" />

            <button type="submit" class="btn btn-ghost">Apply</button>

            @if (request()->hasAny(['search', 'date', 'wastes_type']))
                <a href="{{ route('dashboard') }}" class="btn btn-ghost">Clear</a>
            @endif
        </form>
    </div>
</div>


{{-- TABLE --}}
<div class="overflow-x-auto bg-white shadow-md rounded-lg">
    <table class="table table-zebra w-full">
        <thead>
            <tr>
                <td><input type="checkbox" class="checkbox" /></td>
                <td>{{ Carbon\Carbon::parse($wastes->date)->format('M d, Y') }}</td>
                <td>{{ $wastes->building->name }}</td>

                <td class="text-center col-residual">{{ number_format($wastes->residual, 2) }}</td>
                <td class="text-center col-recyclable">{{ number_format($wastes->recyclable, 2) }}</td>
                <td class="text-center col-biodegradable">{{ number_format($wastes->biodegradable, 2) }}</td>
                <td class="text-center col-infectious">{{ number_format($wastes->infectious, 2) }}</td>
                <td class="text-center font-bold col-total">{{ number_format($totalWeight, 2) }}</td>

                {{-- BIG X DELETE BUTTON --}}
                <td class="text-right">
                                        </td>
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
                    <td class="text-center col-residual">{{ number_format($waste->residual, 2) }}</td>
                    <td class="text-center col-recyclable">{{ number_format($waste->recyclable, 2) }}</td>
                    <td class="text-center col-biodegradable">{{ number_format($waste->biodegradable, 2) }}</td>
                    <td class="text-center col-infectious">{{ number_format($waste->infectious, 2) }}</td>
                    <td class="text-center font-bold col-total">{{ number_format($totalWeight, 2) }}</td>

                    {{-- BIG X DELETE BUTTON --}}
                    <td class="text-right">
                        <form method="POST" action="{{ route('waste.destroy', $waste) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Delete this entry?')"
                                class="text-red-600 text-xl font-bold hover:text-red-800">
                                ✕
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


{{-- COLUMN TOGGLING JS --}}
<script>
    function toggleColumn(className) {
        document.querySelectorAll('.' + className)
            .forEach(col => col.classList.toggle('hidden'));
    }

    function showAllColumns() {
        document.querySelectorAll('[class^="col-"]').forEach(col => col.classList.remove('hidden'));
    }
</script>
