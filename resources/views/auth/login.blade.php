<!DOCTYPE html>
<html lang="en" class="h-full bg-[#FAF7F2]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PhysioCare Clinic Management</title>
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
<body class="h-full bg-[#FAF7F2] text-[#2B352A] flex items-center justify-center p-4">

    <div class="w-full max-w-md space-y-6">

        <!-- Clinic Branding Header -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-brand-700 to-brand-500 flex items-center justify-center text-white shadow-lg shadow-brand-700/25">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-[#1F2C1E]">
                Physio<span class="text-brand-600">Care</span>
            </h1>
            <p class="text-xs font-semibold text-[#5C6B5A] uppercase tracking-wider">
                Rehabilitation &amp; Domiciliary Home Care
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-[#FDFCF9] rounded-3xl border border-[#E6DFD3] shadow-xl shadow-[#D5CBB9]/30 p-8 space-y-6">

            <div>
                <h2 class="text-base font-bold text-[#1F2C1E]">Sign In to Your Account</h2>
                <p class="text-xs text-[#5C6B5A] mt-0.5">Please authenticate to access the clinic EMR &amp; billing.</p>
            </div>

            <!-- Error Notifications -->
            @if(session('error'))
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="p-3 rounded-xl bg-brand-50 border border-brand-200 text-xs text-brand-800 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                    @foreach($errors->all() as $err)
                        <div>&bull; {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-[#3C4A3A] mb-1">Email Address</label>
                    <input type="email" name="email" id="emailInput" required value="{{ old('email', 'admin@physio.local') }}" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3.5 py-2.5 bg-ivory-100 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-hidden transition-colors" placeholder="doctor@physiocare.com">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-[#3C4A3A]">Password</label>
                    </div>
                    <input type="password" name="password" id="passwordInput" required value="admin123" class="w-full text-xs rounded-xl border border-[#E6DFD3] px-3.5 py-2.5 bg-ivory-100 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-hidden transition-colors" placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-[#4A5749] font-medium">
                        <input type="checkbox" name="remember" value="1" class="rounded text-brand-600 focus:ring-brand-500 border-[#D5CBB9]">
                        Remember this device
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold shadow-md shadow-brand-700/25 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Secure Sign In
                </button>
            </form>

            <!-- 1-Click Demo Accounts Quick Fill -->
            <div class="pt-4 border-t border-[#E6DFD3]">
                <span class="block text-[11px] font-bold uppercase tracking-wider text-[#7A8778] text-center mb-2.5">
                    Demo Accounts (1-Click Fill)
                </span>
                <div class="grid grid-cols-3 gap-2 text-[11px]">
                    <button type="button" onclick="fillDemo('admin@physio.local', 'admin123')" class="p-2 rounded-xl bg-ivory-100 hover:bg-brand-100 hover:text-brand-800 border border-[#E6DFD3] text-[#3C4A3A] font-semibold text-center transition-colors">
                        👑 Admin
                    </button>
                    <button type="button" onclick="fillDemo('rajesh.physio@physio.local', 'password')" class="p-2 rounded-xl bg-ivory-100 hover:bg-brand-100 hover:text-brand-800 border border-[#E6DFD3] text-[#3C4A3A] font-semibold text-center transition-colors">
                        🩺 Doctor
                    </button>
                    <button type="button" onclick="fillDemo('reception@physio.local', 'password')" class="p-2 rounded-xl bg-ivory-100 hover:bg-brand-100 hover:text-brand-800 border border-[#E6DFD3] text-[#3C4A3A] font-semibold text-center transition-colors">
                        📋 Front Desk
                    </button>
                </div>
            </div>

        </div>

        <div class="text-center text-[11px] text-[#7A8778]">
            Database: <span class="font-mono text-[#3C4A3A] font-bold">physio</span> &bull; Sage Green &amp; Ivory Theme
        </div>

    </div>

    <script>
        function fillDemo(email, pass) {
            document.getElementById('emailInput').value = email;
            document.getElementById('passwordInput').value = pass;
        }
    </script>
</body>
</html>
