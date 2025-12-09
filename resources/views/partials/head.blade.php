<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Artemis — Dashboard</title>

<meta name="csrf-token" content="{{ csrf_token() }}">


<script src="https://cdn.tailwindcss.com"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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