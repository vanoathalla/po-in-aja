<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PO-in Aja — Official Jersey Teknik Informatika' }}</title>

    {{-- Apple / iOS System Fonts + Plus Jakarta Sans + Bebas Neue --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN dengan konfigurasi tema iOS Clean White & Royal Blue --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', '"SF Pro Display"', '"Plus Jakarta Sans"', 'sans-serif'],
                        athletic: ['"Bebas Neue"', 'Impact', 'sans-serif'],
                    },
                    colors: {
                        ios: {
                            bg: '#F8FAFC',
                            card: '#FFFFFF',
                            border: '#E2E8F0',
                            blue: '#2563EB',
                            'blue-hover': '#1D4ED8',
                            'blue-light': '#EFF6FF',
                        }
                    },
                    boxShadow: {
                        'ios': '0 4px 20px -2px rgba(15, 23, 42, 0.05)',
                        'ios-lg': '0 12px 32px -4px rgba(15, 23, 42, 0.08)',
                        'ios-pill': '0 2px 8px rgba(0, 0, 0, 0.04)',
                    }
                }
            }
        }
    </script>

    <style>
        /* Smooth iOS glass blur & crisp scrollbar */
        body {
            background-color: #F8FAFC;
            color: #0F172A;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .ios-blur {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
    </style>

    @livewireStyles
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
    {{ $slot }}

    @livewireScripts
</body>
</html>
