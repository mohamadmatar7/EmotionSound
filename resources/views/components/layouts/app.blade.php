<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Emotion Sound</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

<body class="bg-gray-100 text-gray-900">
    <main class="min-h-screen flex flex-col justify-center items-center p-6">
        {{ $slot }}
    </main>

</body>
</html>
