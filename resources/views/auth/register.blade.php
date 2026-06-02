<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reverence Worship - Register</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #eef2f7 100%);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }
        
        .register-container {
            min-height: 100vh;
        }
        
        .register-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.12);
        }
        
        .input-field {
            width: 100%;
            padding: 11px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.2s ease;
            background: #ffffff;
        }
        
        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        .btn-register {
            background: #1e40af;
            transition: all 0.2s ease;
            font-weight: 600;
            border-radius: 12px;
        }
        
        .btn-register:hover {
            background: #1e3a8a;
            transform: translateY(-1px);
        }
        
        .btn-google {
            border: 1px solid #e2e8f0;
            background: white;
            transition: all 0.2s ease;
            border-radius: 12px;
        }
        
        .btn-google:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .divider::before {
            margin-right: 0.8rem;
        }
        
        .divider::after {
            margin-left: 0.8rem;
        }
        
        .verse-card {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            border-radius: 20px;
        }
        
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
        }
        
        /* Reduced padding and spacing */
        .form-group {
            margin-bottom: 14px;
        }
        
        label {
            font-size: 13px;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="register-container flex items-center justify-center p-4">
        <div class="w-full max-w-4xl">
            <div class="register-card overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    
                    <!-- Left Side - Inspirational Content (Smaller) -->
                    <div class="verse-card w-full md:w-2/5 p-6 text-white">
                        <div class="h-full flex flex-col justify-between">
                            <div>
                                <div class="mb-4">
                                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                                        <i class="fas fa-church text-base"></i>
                                    </div>
                                    <h2 class="text-xl font-bold">REVERENCE</h2>
                                    <p class="text-blue-100 text-xs mt-0.5">Worship Team</p>
                                </div>
                                
                                <div class="mt-6">
                                    <h1 class="text-2xl font-bold leading-tight mb-2">
                                        Join the<br>
                                        Worship Team
                                    </h1>
                                    <p class="text-base font-semibold text-yellow-300 leading-tight">
                                        Become part of<br>
                                        our mission.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <div class="border-l-2 border-white/30 pl-3 mb-4">
                                    <i class="fas fa-quote-left text-white/40 text-sm mb-1 block"></i>
                                    <p class="text-white/70 text-xs leading-relaxed">
                                        "Worship the Lord with gladness; come before him with joyful songs."
                                    </p>
                                    <p class="text-xs text-white/30 mt-1">— Psalm 100:2</p>
                                </div>
                                
                                <div class="pt-3 border-t border-white/20">
                                    <p class="text-xs text-white/40 leading-relaxed">
                                        Join REVERENCE Worship Team in our mission to elevate hearts and honor God through excellence in worship.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side - Register Form (Smaller) -->
                    <div class="w-full md:w-3/5 p-6 md:p-7">
                        <!-- Logo for mobile -->
                        <div class="md:hidden flex items-center gap-2 mb-4">
                            <img src="{{ asset('images/logo.png') }}" alt="Reverence Worship" class="h-8 w-auto">
                            <div>
                                <h1 class="text-lg font-bold text-gray-800">Reverence</h1>
                                <p class="text-xs text-gray-400">Worship Team</p>
                            </div>
                        </div>
                        
                        <div class="mb-5">
                            <h2 class="text-xl font-bold text-gray-800 mb-1">Create account</h2>
                            <p class="text-gray-500 text-xs">Join the Reverence Worship Team</p>
                        </div>
                        
                        @if(session('error'))
                            <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl mb-4 text-xs">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl mb-4 text-xs">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Please fix the errors below.
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <div class="form-group">
                                <label class="block text-gray-700 font-medium">Full Name</label>
                                <div class="relative">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" name="name" required 
                                           class="input-field pl-10"
                                           placeholder="John Doe"
                                           value="{{ old('name') }}">
                                </div>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label class="block text-gray-700 font-medium">Email Address</label>
                                <div class="relative">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" required 
                                           class="input-field pl-10"
                                           placeholder="name@reverence.com"
                                           value="{{ old('email') }}">
                                </div>
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label class="block text-gray-700 font-medium">Password</label>
                                <div class="relative">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="password" required 
                                           class="input-field pl-10"
                                           placeholder="Create a password">
                                </div>
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label class="block text-gray-700 font-medium">Confirm Password</label>
                                <div class="relative">
                                    <i class="fas fa-check-circle input-icon"></i>
                                    <input type="password" name="password_confirmation" required 
                                           class="input-field pl-10"
                                           placeholder="Confirm your password">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-register w-full text-white py-2.5 transition mt-2">
                                Create Account
                            </button>
                        </form>
                        
                        <div class="divider my-5">
                            <span>or sign up with</span>
                        </div>
                        
                        <a href="{{ route('google.login') }}" class="btn-google w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-gray-600 text-sm transition">
                            <i class="fab fa-google text-red-500"></i>
                            <span>Google</span>
                        </a>
                        
                        <div class="mt-5 text-center">
                            <p class="text-xs text-gray-500">
                                Already have an account? 
                                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">Sign in</a>
                            </p>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>