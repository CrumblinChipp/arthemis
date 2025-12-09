@extends('layouts.app') {{-- Extends the new main layout --}}

@section('content')

    {{-- Dashboard Content --}}
    <section data-section="dashboard" class="content-section">
        @include('sections.dashboard-content')
    </section>

    {{-- Maps Content --}}
    <section data-section="maps" class="content-section hidden">
        <h1 class="text-3xl font-bold mb-4">Maps</h1>
        <div id="maps-area">
             {{-- Map embed/code goes here --}}
        </div>
    </section>

    {{-- Data Content --}}
    <section data-section="data" class="content-section hidden">
        <h1 class="text-3xl font-bold mb-4">Data Analytics</h1>
        <div id="data-stats">
            {{-- Data tables, charts, etc --}}
        </div>
    </section>

    {{-- Admin Content --}}
    <section data-section="admin" class="content-section hidden">
        {{-- Include the admin settings content here --}}
        @include('sections.admin-settings')
    </section>

    {{-- The JS data block for the charts must be kept on the page that extends the layout, before the main JS file. --}}
    <script>
        window.dashboardData = {
            labels: @json($labels),
            totals: @json($totals),
            buildingDatasets: @json($buildingDatasets),
            composition: @json(array_values($composition))
        };
    <!-- In the sidebar, find the navigation links and update them: -->

<a href="{{ route('dashboard') }}"
  data-nav="dashboard"
  class="nav-item w-full flex flex-col
  md:flex-row items-center gap-2
  px-3 py-2 rounded-md text-gray-200
  hover:bg-green-900/30 transition">
    <span>🏠︎</span>
    <span class="hidden md:inline text-2xl">Dashboard</span>
</a>

<a href="#maps"
  data-nav="maps"
  class="nav-item w-full flex flex-col
  md:flex-row items-center gap-2
  px-3 py-2 rounded-md text-gray-200
  hover:bg-green-900/30 transition">
    <span>⚲</span>
    <span class="hidden md:inline text-2xl">Map</span>
</a>

<!-- Add more sections as needed -->

<!-- Add a "Back to Home" link at the bottom of sidebar -->
<a href="{{ route('home') }}"
  class="nav-item w-full flex flex-col
  md:flex-row items-center gap-2
  px-3 py-2 rounded-md text-gray-200
  hover:bg-green-900/30 transition mt-auto">
    <span>🏠</span>
    <span class="hidden md:inline text-2xl">Home</span>
</a>
    </script>

@endsection