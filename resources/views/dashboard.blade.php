<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Artemis — Dashboard</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .sidebar {
      background: #142d1b; /* deep green */
      color: #cfe4d5;
      min-height: 100vh;
    }
    .card {
      background: white;
      border-radius: 6px;
      box-shadow: 0 0 0 1px rgba(0,0,0,0.03);
    }
  </style>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="flex">
    <!-- Sidebar -->
    <aside class="
        fixed top-0 left-0
        bg-[#0f2a1a] text-white flex
        md:flex-col items-center md: items-center
        md:justify-start w-full md:w-72
        fixed md:static top-0 left-0
        h-14 md:h-screen
        px-0 pr-0 md:px-0 md:pr-0
        md:pt-6
        z-50
    ">
        <!-- Logo -->
        <div class="flex items-center gap-3 md:mb-8">
            <div class="h-8 w-8 rounded-full bg-green-700 flex items-center justify-center text-white font-bold">A</div>

            <!-- Hide text on mobile -->
            <div class="hidden md:block">
                <div class="text-white font-semibold">ARTHEMIS</div>
            </div>
        </div>

        <!-- Navigation -->
        <a href="#"
          data-nav="dashboard"
          class="nav-item w-full flex flex-col
          md:flex-row items-center gap-2
          px-3 py-2 rounded-md text-gray-200
          hover:bg-green-900/30 transition">
            <span>🏠︎</span>
            <span class="hidden md:inline text-2xl">Dashboard</span>
        </a>

        <!-- Maps -->
        <a href="#"
          data-nav="maps"
          class="nav-item w-full flex flex-col
          md:flex-row items-center gap-2
          px-3 py-2 rounded-md text-gray-200
          hover:bg-green-900/30 transition">
            <span>⚲</span>
            <span class="hidden md:inline text-2xl">Map</span>
        </a>

        <!-- Trash Bin -->
        <a href="#"
          data-nav="trash"
          class="nav-item w-full flex flex-col
          md:flex-row items-center gap-2
          px-3 py-2 rounded-md text-gray-200
          hover:bg-green-900/30 transition">
            <span>🗑</span>
            <span class="hidden md:inline text-2xl">Trashbin</span>
        </a>

        <!-- Data -->
        <a href="#"
          data-nav="data"
          class="nav-item w-full flex flex-col
          md:flex-row items-center gap-2
          px-3 py-2 rounded-md text-gray-200
          hover:bg-green-900/30 transition">
            <span>𝄜</span>
            <span class="hidden md:inline text-2xl">Data</span>
        </a>

        <!-- Account Setting -->
        <a href="#"
          data-nav="data"
          class="nav-item w-full flex flex-col
          md:flex-row items-center gap-2
          px-3 py-2 rounded-md text-gray-200
          hover:bg-green-900/30 transition">
            <span>𝄜</span>
            <span class="hidden md:inline text-2xl">Account Settings</span>
            
        </a>


        <!-- Desktop logout -->
        <div class="hidden md:block mt-auto mb-6">
            <a href="#" class="inline-block bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Sign out</a>
        </div>

        <!-- Mobile logout icon: aligned to right -->
        <div class="md:hidden absolute right-4">
            <span class="text-2xl">⏻</span>
        </div>
    </aside>

    <!--Main-->
    <main id="mainContent" class="flex-1 p-6">
      <!-- Dashboard Content -->
      <section data-section="dashboard" class="content-section">
        <h1 class="text-3xl font-bold mb-4">Dashboard</h1>
        <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
          <select
              name="days"
              onchange="this.form.submit()"
              class="bg-white text-black p-2 rounded border border-gray-300"
          >
              <option value="7"  {{ $selectedRange == 7 ? 'selected' : '' }}>Last Week</option>
              <option value="30" {{ $selectedRange == 30 ? 'selected' : '' }}>Last 30 Days</option>
              <option value="90" {{ $selectedRange == 90 ? 'selected' : '' }}>Last 90 Days</option>
          </select>
        </form>

        <div id="dashboard-graphs">
          <!-- Top stats -->
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

          <!-- Charts row -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Line chart area -->
            <div class="md:col-span-2 card p-4">
              <div class="mb-3 font-medium">Overall weight</div>
              <canvas id="lineChart" height="90"></canvas>
            </div>

            <!-- Pie/composition -->
            <div class="card p-4 md:col-span-1">
              <div class="mb-3 font-medium">Waste composition</div>
              <div class="flex gap-4">
                <div style="width:140px; height:140px;">
                  <canvas id="donutChart"></canvas>
                </div>
                <div class="flex-1">
                  <div class="space-y-3">
                    <div class="p-3 rounded bg-gray-100">
                      <div class="text-sm font-medium">Biodegradable</div>
                      <div class="text-lg font-bold">{{ $composition['biodegradable'] ?? 0 }} kg</div>
                    </div>
                    <div class="p-3 rounded bg-gray-100">
                      <div class="text-sm font-medium">Residual</div>
                      <div class="text-lg font-bold">{{ $composition['residual'] ?? 0 }} kg</div>
                    </div>
                    <div class="p-3 rounded bg-gray-100">
                      <div class="text-sm font-medium">Recyclable</div>
                      <div class="text-lg font-bold">{{ $composition['recyclable'] ?? 0 }} kg</div>
                    </div>
                    <div class="p-3 rounded bg-gray-100">
                      <div class="text-sm font-medium">Infectious</div>
                      <div class="text-lg font-bold">{{ $composition['infectious'] ?? 0 }} kg</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- PerBuilding Chart -->
          <div class="mt-6 card p-4">
            <div class="md:col-span-2 card p-4">
              <div class="mb-3 font-medium">Waste Generated per Building</div>
              <canvas id="buildingLineChart" height="90"></canvas>
            </div>
          </div>

        </div>
      </section>

      <!-- Maps Content -->
      <section data-section="maps" class="content-section hidden">
          <h1 class="text-3xl font-bold mb-4">Maps</h1>
          <div id="maps-area">
              <!-- Map embed/code goes here -->
          </div>
      </section>

      <!-- Trash Content -->
      <section data-section="trash" class="content-section hidden">
          <h1 class="text-3xl font-bold mb-4">Trash Bin Data</h1>
          <div id="trash-info">
              <!-- Trash bin info -->
          </div>
      </section>

      <!-- Data Content -->
      <section data-section="data" class="content-section hidden">
          <h1 class="text-3xl font-bold mb-4">Data Analytics</h1>
          <div id="data-stats">
              <!-- Data tables, charts, etc -->
          </div>
      </section>

  </main>

</div>

</body>
</html>

<script>
      window.dashboardData = {
        labels: @json($labels),
        totals: @json($totals),
        buildings: @json($buildings)
        composition: @json(array_values($composition))
        wastePerBuilding: @json($wastePerBuilding)
    };
</script>
<script src="{{ asset('js/dashboardHandler.js') }}"></script>