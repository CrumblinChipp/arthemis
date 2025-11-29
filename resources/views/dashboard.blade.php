<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Artemis — Dashboard</title>

  <meta name="csrf-token" content="{{ csrf_token() }}">


  <script src="https://cdn.tailwindcss.com"></script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .sidebar {
      background: #142d1b;
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

<div class="flex w-full">
    <!-- Sidebar -->
    <aside class="
        bg-[#0f2a1a] text-white flex items-center
        fixed top-0 left-0 z-50
        w-full h-14
        md:flex-col md:items-center md:justify-start
        md:w-72 md:h-screen md:static md:fixed
        md:pt-6
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

        <!-- Admin Setting -->
        @if(session('admin_verified'))
            <a href="#"
          data-nav="admin"
          class="nav-item w-full flex flex-col
          md:flex-row items-center gap-2
          px-3 py-2 rounded-md text-gray-200
          hover:bg-green-900/30 transition">
                <span>⚙</span>
                <span class="hidden md:inline text-2xl">Admin Settings</span>
            </a>
        @endif


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
    <main id="mainContent" class="pt-16 md:pt-[15px] ml-0 md:ml-[300px] w-full">
      <!-- Dashboard Content -->
      <section data-section="dashboard" class="content-section">
        <!-- Filter function -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-bold mb-4">Dashboard</h1>
            <!-- ADMIN Button -->
            <button id="openAdminModal" 
                    class="px-4 py-2 bg-green-700 text-white rounded-md absolute top-4 right-4">
                Admin
            </button>
        </div>
        <div class= "flex justify-between items-center mb-4" >
          <!-- Campus Filter -->
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
          <!--Time filter-->
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

      <!-- Admin Content -->
      <section data-section="admin" class="content-section hidden">
        <!-- Admin Settings Wrapper -->
        <div id="admin-wrapper" class="flex w-full min-h-screen">
            <!-- Middle Sidebar: Admin Navigation -->
            <div id="admin-nav"
                class="w-full md:w-64 bg-white min-h-screen border-r flex-shrink-0 flex flex-col
                        transition-transform duration-300 md:translate-x-0
                        fixed md:static inset-0 z-40">

                <h2 class="text-xl font-bold p-4 border-b">Admin Setting</h2>

                <button data-admin-page="add-campus"
                        class="admin-nav-item p-4 text-left hover:bg-green-900/20 border-b">
                    + Add Campus
                </button>

                <button data-admin-page="edit-campus"
                        class="admin-nav-item p-4 text-left hover:bg-green-900/20 border-b">
                    Edit Campus
                </button>

                <button data-admin-page="edit-building"
                        class="admin-nav-item p-4 text-left hover:bg-green-900/20 border-b">
                    Edit Building
                </button>

            </div>


            <!-- Right Panel: Admin Page Content -->
            <div id="admin-content" class="hidden md:block flex-1 p-6">
                <button id="admin-back"
                        class="md:hidden mb-4 px-4 py-2 bg-green-800 text-white rounded">
                    ← Back
                </button>

                <div id="admin-content-inner">
                  <div id="add-campus-page" class="hidden">
                    <form id="add-campus-form" enctype="multipart/form-data">
                        <h2 class="text-2xl font-bold mb-6">Add New Campus</h2>

                        <div class="mb-4">
                            <label for="campus-name" class="block font-medium mb-1">Campus Name</label>
                            <input type="text" id="campus-name" name="campus_name"
                                  class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600" 
                                  placeholder="Enter campus name" required>
                        </div>

                        <div id="buildings-wrapper" class="mb-4">
                            <label class="block font-medium mb-1">Buildings</label>
                            <div class="flex items-center mb-2">
                                <input type="text" name="buildings[]" 
                                      class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600"
                                      placeholder="Enter building name" required>
                            </div>
                        </div>
                        <button type="button" id="add-building-btn" 
                                class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            + Add Another Building
                        </button>

                        <div class="mb-4">
                            <label for="campus-map" class="block font-medium mb-1">Campus Map (Image)</label>
                            <input type="file" id="campus-map" name="campus_map" accept="image/*"
                                  class="w-full border rounded px-3 py-2">
                        </div>

                        <button type="submit" 
                                class="px-6 py-2 bg-green-800 text-white rounded hover:bg-green-900">
                            Add Campus
                        </button>
                    </form>
                  </div>
                  <div id="edit-campus-page" class="hidden">
                      <h2 class="text-2xl font-bold">Edit Campus</h2>
                  </div>
                  <div id="edit-building-page" class="hidden">
                      <h2 class="text-2xl font-bold">Edit Building</h2>
                  </div>


                </div>
            </div>
        </div>
      </section>

  </main>

</div>

<!--admin modal-->
<div id="adminModal" class="fixed inset-0 hidden flex items-center justify-center bg-black bg-opacity-50">
  <div class="bg-white p-6 rounded-md shadow-lg w-80">
      <h2 class="text-xl font-bold mb-4">Admin Verification</h2>

      <input type="password" id="adminPassword"
          class="border w-full px-3 py-2 mb-3 rounded-md"
          placeholder="Enter admin password">

      <button onclick="verifyAdmin()"
          class="bg-blue-600 w-full text-white py-2 rounded-md">
          Verify
      </button>

      <button onclick="closeAdminModal()"
          class="mt-2 w-full py-2 border rounded-md">
          Cancel
      </button>

      <p id="adminError" class="text-red-600 mt-2 hidden">
          Incorrect password.
      </p>
  </div>
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