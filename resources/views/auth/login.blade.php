<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reverence Worship - Login</title>
    
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
        
        .login-container {
            min-height: 100vh;
        }
        
        .login-card {
            background: white;
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }
        
        .input-field {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            font-size: 15px;
            transition: all 0.2s ease;
            background: #ffffff;
        }
        
        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        .btn-login {
            background: #1e40af;
            transition: all 0.2s ease;
            font-weight: 600;
            border-radius: 16px;
        }
        
        .btn-login:hover {
            background: #1e3a8a;
            transform: translateY(-2px);
        }
        
        .btn-google {
            border: 1px solid #e2e8f0;
            background: white;
            transition: all 0.2s ease;
            border-radius: 16px;
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
            font-size: 13px;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .divider::before {
            margin-right: 1rem;
        }
        
        .divider::after {
            margin-left: 1rem;
        }
        
        .verse-card {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            border-radius: 24px;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        
        .alert {
            border-radius: 16px;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="login-container flex items-center justify-center p-4">
        <div class="w-full max-w-5xl">
            <div class="login-card overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    
                    <!-- Left Side - Inspirational Content -->
                    <div class="verse-card w-full md:w-2/5 p-8 text-white">
                        <div class="h-full flex flex-col justify-between">
                            <div>
                                <div class="mb-6">
                                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                                        <i class="fas fa-church text-xl"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold">REVERENCE</h2>
                                    <p class="text-blue-100 text-sm mt-1">Worship Team</p>
                                </div>
                                
                                <div class="mt-8">
                                    <h1 class="text-4xl font-bold leading-tight mb-3">
                                        Worship
                                    </h1>
                                    <p class="text-2xl font-semibold text-yellow-300 leading-tight">
                                        All to the<br>
                                        Glory of Christ.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mt-10">
                                <div class="border-l-2 border-white/30 pl-4 mb-6">
                                    <i class="fas fa-quote-left text-white/40 text-lg mb-2 block"></i>
                                    <p class="text-white/80 text-sm leading-relaxed">
                                        "Worship the Lord with gladness; come before him with joyful songs."
                                    </p>
                                    <p class="text-xs text-white/40 mt-2">— Psalm 100:2</p>
                                </div>
                                
                                <div class="pt-5 border-t border-white/20">
                                    <p class="text-xs text-white/50 leading-relaxed">
                                        Join REVERENCE Worship Team in our mission to elevate hearts and honor God through excellence in worship.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side - Login Form -->
                    <div class="w-full md:w-3/5 p-8 md:p-10">
                        <!-- Logo for mobile -->
                        <div class="md:hidden flex items-center gap-3 mb-6">
                            <img src="{{ asset('images/logo.png') }}" alt="Reverence Worship" class="h-10 w-auto">
                            <div>
                                <h1 class="text-xl font-bold text-gray-800">Reverence</h1>
                                <p class="text-xs text-gray-400">Worship Team</p>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome back</h2>
                            <p class="text-gray-500 text-sm">Sign in to your account</p>
                        </div>
                        
                        <!-- Success Message (from registration redirect) -->
                        @if(session('success'))
                            <div class="alert bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 text-sm flex items-start gap-2">
                                <i class="fas fa-check-circle mt-0.5"></i>
                                <div class="flex-1">{{ session('success') }}</div>
                                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                        
                        <!-- Warning Message (pending approval) -->
                        @if(session('warning'))
                            <div class="alert bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl mb-5 text-sm flex items-start gap-2">
                                <i class="fas fa-clock mt-0.5"></i>
                                <div class="flex-1">{{ session('warning') }}</div>
                                <button onclick="this.parentElement.remove()" class="text-yellow-500 hover:text-yellow-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                        
                        <!-- Error Message -->
                        @if(session('error'))
                            <div class="alert bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-5 text-sm flex items-start gap-2">
                                <i class="fas fa-exclamation-circle mt-0.5"></i>
                                <div class="flex-1">{{ session('error') }}</div>
                                <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                        
                        <!-- Validation Errors -->
                        @if($errors->any())
                            <div class="alert bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-5 text-sm">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                @foreach($errors->all() as $error)
                                    <p class="mb-1 last:mb-0">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2">Email Address</label>
                                <div class="relative">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" required 
                                           class="input-field pl-11 @error('email') border-red-500 @enderror"
                                           placeholder="name@reverence.com"
                                           value="{{ old('email') }}">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                                <div class="relative">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="password" required 
                                           class="input-field pl-11 @error('password') border-red-500 @enderror"
                                           placeholder="••••••••">
                                </div>
                            </div>
                            
                            <div class="flex justify-end mb-6">
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-700 transition">Forgot password?</a>
                            </div>
                            
                            <button type="submit" class="btn-login w-full text-white py-3.5 transition">
                                Sign In
                            </button>
                        </form>
                        
                        <div class="divider my-6">
                            <span>or continue with</span>
                        </div>
                        
                        <a href="{{ route('google.login') }}" class="btn-google w-full flex items-center justify-center gap-2 py-3 rounded-xl text-gray-600 text-sm transition hover:bg-gray-50">
                            <i class="fab fa-google text-red-500"></i>
                            <span>Continue with Google</span>
                        </a>
                        
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-500">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium transition">Create an account</a>
                            </p>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
    <!-- Auto-dismiss alerts after 5 seconds -->
    <script>
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.3s ease';
                setTimeout(function() {
                    if (alert.parentElement) alert.remove();
                }, 300);
            });
        }, 5000);
    </script>
</body>
</html>