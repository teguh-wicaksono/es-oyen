<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wana Cafe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght=300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }
        
        /* Loading Screen */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f8f3eb;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            transform: translateY(-2vh);
        }
        
        .coffee-image {
            width: clamp(390px, 28vw, 520px);
            height: auto;
            max-height: 42vh;
            object-fit: contain;
            animation: bounce 1.5s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-30px); }
        }
        
        .loading-bar {
            width: 250px;
            height: 6px;
            background: rgba(139, 69, 19, 0.2);
            border-radius: 3px;
            margin-top: 14px;
            overflow: hidden;
        }

        .loading-content h1 {
            margin: -46px 0 0 !important;
            line-height: 1;
        }

        .loading-content p {
            margin: 0 !important;
        }
        
        .loading-progress {
            width: 0%;
            height: 100%;
            background: #b8860b;
            border-radius: 3px;
            animation: loading 2.5s ease-in-out forwards;
        }
        
        @keyframes loading {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        @media (max-width: 760px) {
            .coffee-image {
                width: min(78vw, 380px);
                max-height: 42vh;
            }
        }
    </style>
</head>
<body class="bg-amber-50">
    <!-- Loading Screen -->
    <div class="loading-screen">
        <div class="loading-content">
            <img src="/images/loading.jpg" alt="Coffee" class="coffee-image">
            <h1 class="font-display text-5xl font-bold text-amber-900">Wana Cafe</h1>
            <p class="text-amber-800 text-lg">Sip the Perfect Brew</p>
            <div class="loading-bar">
                <div class="loading-progress"></div>
            </div>
        </div>
    </div>

    <script>
        // Auto redirect after loading completes
        setTimeout(function() {
            window.location.href = "{{ Auth::check() ? route(Auth::user()->role) : route('login') }}";
        }, 2800);
    </script>
</body>
</html>
