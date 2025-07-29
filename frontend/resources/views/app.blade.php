<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Scheduler App</title>
    <script>
        window.API_BASE = "{{ env('MIX_API_BASE', 'http://localhost:8000/api') }}";
    </script>
    @vite('resources/js/app.js')
</head>
<body class="antialiased">
    <div id="app"></div>
</body>
</html>
