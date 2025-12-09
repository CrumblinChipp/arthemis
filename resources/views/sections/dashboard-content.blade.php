<div class="flex justify-between items-center mb-4">
    <h1 class="text-3xl font-bold mb-4">Dashboard</h1>
</div>
{{-- Filter function --}}
<div class= "flex justify-between items-center mb-4" >
    {{-- Campus Filter --}}
    <form id="campusFilterForm" class="flex justify-start items-center mb-4">
        <select name="campus" onchange="this.form.submit()"
        class="bg-white text-black p-2 rounded border border-gray-300">
            @foreach ($campuses as $c)
                <option value="{{ $c->id }}" {{ $selectedCampus == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
    </form>
    {{-- Time filter --}}
    <form method="GET" action="{{ route('dashboard') }}" class="flex justify-end items-right mb-4">
        <select
            name="days"
            onchange="this.form.submit()"
            class="bg-white text-black p-2 rounded border border-gray-300">
            <option value="7"  {{ $selectedRange == 7 ? 'selected' : '' }}>Last Week</option>
            <option value="30" {{ $selectedRange == 30 ? 'selected' : '' }}>Last 30 Days</option>
            <option value="90" {{ $selectedRange == 90 ? 'selected' : '' }}>Last 90 Days</option>
        </select>
    </form>
</div>

<div id="dashboard-graphs">
    {{-- Top stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card p-6 text-center">
            <div class="text-sm text-red-600 font-medium flex items-center justify-center gap-2">📉 Highest</div>
            <div class="text-4xl text-red-600 font-bold mt-3">{{ $highest['kg'] ?? 0 }} <span class="text-lg font-normal">kg</span></div>
            <div class="text-xs text-gray-400 mt-2">{{ $highest['date'] ?? '' }}</div>
        </div>

        <div class="card p-6 text-center">
            <div class="text-sm text-green-600 font-medium flex items-center justify-center gap-2">📈 Lowest</div>
            <div class="text-4xl text-green-600 font-bold mt-3">{{ $lowest['kg'] ?? 0 }} <span class="text-lg font-normal">kg</span></div>
            <div class="text-xs text-gray-400 mt-2">{{ $lowest['date'] ?? '' }}</div>
        </div>

        <div class="card p-6 text-center">
            <div class="text-sm text-gray-500">Average</div>
            <div class="text-3xl text-gray-700 font-bold mt-3">{{ $average }} <span class="text-lg font-normal">kg</span></div>
        </div>
    </div>

    <h2 class="text-xl font-semibold mb-4">Waste Generated</h2>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Line chart area --}}
        <div class="md:col-span-2 card p-4">
            <div class="mb-3 font-medium">Overall weight</div>
            <canvas id="lineChart" height="130"></canvas>
        </div>

        {{-- Pie/composition --}}
        <div class="card p-4 md:col-span-1">
            <div class="mb-3 font-medium">Waste composition</div>
            <div class="flex justify-center items-center h-64">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </div>

    {{-- PerBuilding Line Chart --}}
    <div class="mt-6 card p-4">
        <div class="mb-3 font-medium">Waste Generated per Building</div>
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <canvas id="buildingLineChart" height="300"></canvas>
            </div>
            <div id="perBuildingSummary" class="w-64 flex flex-col gap-2"></div>
        </div>
    </div>

</div>