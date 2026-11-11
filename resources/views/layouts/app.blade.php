<!DOCTYPE html>
<html lang="en" class="h-full bg-[#FAF7F2]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PhysioCare') - Physiotherapy &amp; Rehab Center</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#F2F6F2',
                            100: '#E2ECE1',
                            200: '#C5D7C3',
                            300: '#A3BEA0',
                            400: '#7E9F7B',
                            500: '#62845E',
                            600: '#4D6D4A',
                            700: '#3C5539',
                            800: '#2E412C',
                            900: '#1F2C1E',
                        },
                        ivory: {
                            50: '#FDFCF9',
                            100: '#FAF7F2',
                            200: '#F2EDE4',
                            300: '#E6DFD3',
                            400: '#D5CBB9',
                            500: '#B5A893',
                            600: '#8A7D69',
                            700: '#63594A',
                            800: '#423A30',
                            900: '#25201A',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-[#FAF7F2] text-[#2B352A] antialiased flex flex-col md:flex-row min-h-screen">

    <!-- ============================================== -->
    <!-- LEFT-HAND SIDE NAVIGATION MENU (SIDEBAR)       -->
    <!-- ============================================== -->
    <aside class="w-full md:w-64 bg-[#FDFCF9] border-r border-[#E6DFD3] shrink-0 flex flex-col z-30">
        
        <!-- Sidebar Brand Header -->
        <div class="h-16 flex items-center px-6 border-b border-[#E6DFD3] justify-between">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-700 to-brand-500 flex items-center justify-center text-white shadow-md shadow-brand-700/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-black tracking-tight text-[#1F2C1E] flex items-center gap-1">
                        Physio<span class="text-brand-600">Care</span>
                    </span>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-brand-700 -mt-1">Rehab &amp; Home EMR</span>
                </div>
            </a>
        </div>

        <!-- Sidebar Navigation Links -->
        <div class="flex-1 py-5 px-3 space-y-1 overflow-y-auto">
            
            @php
                $role = auth()->user()->role ?? 'admin';
            @endphp

            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-brand-700/60">
                @if($role === 'therapist')
                    Therapist Portal
                @elseif($role === 'receptionist')
                    Front Desk Desk
                @else
                    Main Clinic Menu
                @endif
            </div>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-brand-100 text-brand-800 border border-brand-300 shadow-xs' : 'text-[#4A5749] hover:text-brand-800 hover:bg-brand-50' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-brand-700' : 'text-brand-500/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>{{ $role === 'therapist' ? 'Clinical Dashboard' : ($role === 'receptionist' ? 'Front Desk Dashboard' : 'Clinic Dashboard') }}</span>
            </a>

            <!-- Patients EMR (All authenticated staff) -->
            <a href="{{ route('patients.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('patients.*') ? 'bg-brand-100 text-brand-800 border border-brand-300 shadow-xs' : 'text-[#4A5749] hover:text-brand-800 hover:bg-brand-50' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('patients.*') ? 'text-brand-700' : 'text-brand-500/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Patients Directory</span>
            </a>

            <!-- Appointments & Home Visits (All authenticated staff) -->
            <a href="{{ route('appointments.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('appointments.*') ? 'bg-brand-100 text-brand-800 border border-brand-300 shadow-xs' : 'text-[#4A5749] hover:text-brand-800 hover:bg-brand-50' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('appointments.*') ? 'text-brand-700' : 'text-brand-500/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ $role === 'therapist' ? 'My Schedule & Visits' : 'Appointments & Home Visits' }}</span>
            </a>

            <!-- Treatment Sessions (Admin & Therapist) -->
            @if(in_array($role, ['admin', 'therapist'], true))
            <a href="{{ route('sessions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('sessions.*') ? 'bg-brand-100 text-brand-800 border border-brand-300 shadow-xs' : 'text-[#4A5749] hover:text-brand-800 hover:bg-brand-50' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('sessions.*') ? 'text-brand-700' : 'text-brand-500/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span>Treatment Sessions</span>
            </a>
            @endif

            <!-- Billing & Invoices (Admin & Receptionist only) -->
            @if(in_array($role, ['admin', 'receptionist'], true))
            <a href="{{ route('billing.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('billing.*') ? 'bg-brand-100 text-brand-800 border border-brand-300 shadow-xs' : 'text-[#4A5749] hover:text-brand-800 hover:bg-brand-50' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('billing.*') ? 'text-brand-700' : 'text-brand-500/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Billing &amp; Invoices</span>
            </a>
            @endif

            <!-- Exercise Library (Admin & Therapist) -->
            @if(in_array($role, ['admin', 'therapist'], true))
            <a href="{{ route('exercises.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('exercises.*') ? 'bg-brand-100 text-brand-800 border border-brand-300 shadow-xs' : 'text-[#4A5749] hover:text-brand-800 hover:bg-brand-50' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('exercises.*') ? 'text-brand-700' : 'text-brand-500/70' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span>Exercise Library</span>
            </a>
            @endif

            <!-- Quick Action Divider -->
            <div class="pt-4 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-brand-700/60">
                Quick Shortcuts
            </div>

            <div class="px-2 space-y-2">
                @if(in_array($role, ['admin', 'receptionist'], true))
                    <a href="{{ route('patients.create') }}" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Register Patient
                    </a>
                @endif

                @if($role === 'therapist')
                    <a href="{{ route('sessions.create') }}" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        + Log Treatment Session
                    </a>
                @endif

                <a href="{{ route('appointments.create') }}" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-ivory-200 hover:bg-ivory-300 text-[#3C4A3A] text-xs font-bold rounded-xl transition-colors border border-[#E6DFD3]">
                    <svg class="w-3.5 h-3.5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Schedule Visit
                </a>

                @if($role === 'receptionist')
                    <a href="{{ route('billing.index') }}" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-amber-100 hover:bg-amber-200 text-amber-900 text-xs font-bold rounded-xl transition-colors border border-amber-300">
                        <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Desk Collections
                    </a>
                @endif
            </div>

        </div>

        <!-- Sidebar Bottom: Authenticated User Profile & Logout -->
        <div class="p-3 border-t border-[#E6DFD3] bg-[#FAF7F2]">
            @auth
                <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-[#E6DFD3] shadow-xs">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="w-8 h-8 rounded-full bg-brand-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <span class="block text-xs font-bold text-[#1F2C1E] truncate">{{ auth()->user()->name }}</span>
                            <span class="block text-[10px] font-semibold text-brand-700 uppercase tracking-wider">{{ auth()->user()->role ?? 'Staff' }}</span>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Sign Out" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl transition-colors shadow-xs">
                    Sign In
                </a>
            @endauth
        </div>

    </aside>

    <!-- ============================================== -->
    <!-- RIGHT-HAND SIDE MAIN CONTENT AREA             -->
    <!-- ============================================== -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-[#FAF7F2]">

        <!-- Top Slim Utility Bar -->
        <header class="bg-[#FDFCF9] border-b border-[#E6DFD3] h-16 flex items-center justify-between px-6 sticky top-0 z-20 shadow-xs">
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-brand-800">
                    Clinic Management &bull; <span class="font-mono text-brand-700 bg-brand-50 px-2 py-0.5 rounded border border-brand-200 font-bold">physio (MySQL)</span>
                </span>
            </div>

            <div class="flex items-center gap-4 text-xs">
                <span class="text-[#5C6B5A] font-medium hidden sm:inline">
                    {{ date('l, d M Y') }}
                </span>
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-ivory-200 hover:bg-rose-50 hover:text-rose-700 text-[#3C4A3A] transition-colors flex items-center gap-1.5 border border-[#E6DFD3]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Log Out
                        </button>
                    </form>
                @endauth
            </div>
        </header>

        <!-- Flash Notifications -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            @if(session('success'))
                <div class="rounded-xl bg-brand-50 border border-brand-200 p-4 mb-4 flex items-center justify-between text-brand-900 shadow-xs">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 mb-4 flex items-center space-x-3 text-rose-800 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 mb-4 text-rose-800 shadow-xs">
                    <div class="font-semibold text-sm mb-1">Please fix the following validation errors:</div>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Main Page Content -->
        <main class="flex-1 px-4 sm:px-6 lg:px-8 pb-12">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-[#FDFCF9] border-t border-[#E6DFD3] py-4 px-6 text-xs text-[#7A8778] flex flex-col sm:flex-row justify-between items-center gap-2 mt-auto">
            <div>PhysioCare Management Software &bull; Domiciliary Home Care &amp; Pay-Per-Session EMR</div>
            <div>Sage Green &amp; Warm Ivory Clinical Palette</div>
        </footer>

    </div>

</body>
</html>
