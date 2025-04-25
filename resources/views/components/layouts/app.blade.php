<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Emotion Sound</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Favicon + manifest --}}
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/site.webmanifest">

    {{-- Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>
<body class="bg-gradient-to-br from-gray-900 via-indigo-900 to-gray-800  min-h-screen font-sans flex flex-col justify-center items-center p-6"
>

    {{-- Logo --}}
    <style>
        @keyframes heartbeat {
          0%, 100% {
            transform: scale(1);
          }
          14% {
            transform: scale(1.3);
          }
          28% {
            transform: scale(1);
          }
          42% {
            transform: scale(1.2);
          }
          70% {
            transform: scale(1);
          }
        }
        
        .heartbeat {
          animation: heartbeat 1.5s infinite;
        }
    </style>
        
    <img src="/images/emotion-logo.png" alt="EmotionSound logo"
             class="h-36 w-36 drop-shadow-lg heartbeat">

    {{-- Main content container --}}
    <div class="w-full max-w-4xl flex flex-col items-center">
        {{-- Step Title --}}
        {{ $slot }}
    </div>

</body>
</html>
