@extends('layouts.app') {{-- Extends the new main layout --}}

@section('content')

    {{-- Dashboard Content --}}
    <section data-section="dashboard" class="content-section">
        @include('sections.dashboard-content')
    </section>

    {{-- Maps Content --}}
    <section data-section="maps" class="content-section hidden">
        <div id="maps-area">
            @include('sections.maps-content')
        </div>
    </section>

    {{-- Data Content --}}
    <section data-section="data" class="content-section hidden">
        <h1 class="text-3xl font-bold mb-4">Data Analytics</h1>
        <div id="data-stats">
            @include('sections.data')
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
    </script>

@endsection