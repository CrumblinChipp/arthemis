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