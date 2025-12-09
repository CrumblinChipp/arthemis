<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARTHEMIS - Dashboard</title>
    <style>
        /* Your existing styles */
    </style>
</head>
<body>
    <!-- ADD THIS HEADER WITH LOGOUT BUTTON -->
    <header style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); padding: 20px 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1400px; margin: 0 auto;">
            <h1 style="color: white; margin: 0; font-size: 28px; letter-spacing: 2px;">ARTHEMIS</h1>
            
            <div style="display: flex; align-items: center; gap: 20px;">
                <span style="color: white; font-weight: 500;">
                    Welcome, {{ auth()->user()->name }}
                </span>
                
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="
                        padding: 10px 20px;
                        background: rgba(255, 255, 255, 0.2);
                        color: white;
                        border: 2px solid white;
                        border-radius: 8px;
                        cursor: pointer;
                        font-weight: 600;
                        font-size: 14px;
                        transition: all 0.3s;
                    ">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- YOUR EXISTING DASHBOARD CONTENT BELOW -->
    <main style="padding: 40px;">
        <!-- Your existing dashboard content stays here -->
    </main>
</body>
</html>

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
        bg-[#0f2a1a] text-white flex
        md:flex-col items-center md: items-center
        md:justify-start w-full md:w-72
        fixed md:static top-0 left-0
        h-14 md:h-screen
        px-0 pr-0 md:px-0 md:pr-0
        md:pt-6
        z-50">

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
          data-nav="setting"
          class="nav-item w-full flex flex-col
          md:flex-row items-center gap-2
          px-3 py-2 rounded-md text-gray-200
          hover:bg-green-900/30 transition">
            <span>𝄜</span>
            <span class="hidden md:inline text-2xl">Account Settings</span>
            
        </a>
    </aside>

    <!--Main-->
    <main id="mainContent" class="flex-1 p-6">
      <!-- Dashboard Content -->
      <section data-section="dashboard" class="content-section">
        <!-- Filter function -->
        <div class="flex">
            <h1 class="text-3xl font-bold mb-4">Dashboard</h1>
            <form method="GET" action="{{ route('dashboard') }}" class="flex justify-center items-center mb-4">
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
        </div>

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
              <canvas id="lineChart" height="130"></canvas>
            </div>

            <!-- Pie/composition -->
            <div class="card p-4 md:col-span-1">
              <div class="mb-3 font-medium">Waste composition</div>
              <div class="flex justify-center items-center h-64">
                <canvas id="donutChart"></canvas>
              </div>
            </div>
          </div>

          <!-- PerBuilding Line Chart -->
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

      <!-- Account Setting -->
      <section data-section="setting" class="content-section hidden">
          <h1 class="text-3xl font-bold mb-4">Account Setting</h1>
          <div id="account-setting">
              <!-- change password, change name -->
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
        buildingDatasets: @json($buildingDatasets),
        composition: @json(array_values($composition))
    };
</script>
<script src="{{ asset('js/dashboardHandler.js') }}"></script>