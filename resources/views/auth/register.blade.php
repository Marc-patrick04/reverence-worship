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
            padding: 2rem;
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
        
        .input-field.valid {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
        
        .input-field.invalid {
            border-color: #ef4444;
            background-color: #fef2f2;
        }
        
        select.input-field {
            cursor: pointer;
            background-color: white;
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
        
        .btn-register:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            transform: none;
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
        
        .eye-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            font-size: 14px;
            transition: color 0.2s ease;
            z-index: 10;
        }
        
        .eye-icon:hover {
            color: #3b82f6;
        }
        
        .password-strength {
            height: 3px;
            transition: all 0.3s ease;
            border-radius: 3px;
        }
        
        .form-group {
            margin-bottom: 14px;
        }
        
        label {
            font-size: 13px;
            margin-bottom: 4px;
            font-weight: 500;
        }
        
        .match-indicator {
            font-size: 11px;
            margin-top: 4px;
            transition: all 0.2s ease;
        }
        
        .password-requirements {
            font-size: 10px;
            margin-top: 4px;
        }
        
        .requirement {
            color: #94a3b8;
            transition: color 0.2s ease;
        }
        
        .requirement.met {
            color: #10b981;
        }
        
        .alert {
            border-radius: 12px;
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
        
        .scrollable-form {
            max-height: 70vh;
            overflow-y: auto;
            padding-right: 5px;
        }
        
        .scrollable-form::-webkit-scrollbar {
            width: 5px;
        }
        
        .scrollable-form::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 5px;
        }
        
        .scrollable-form::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 5px;
        }
        
        .required-star {
            color: #ef4444;
            margin-left: 2px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #1e40af;
            margin: 12px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="register-container flex items-center justify-center">
        <div class="w-full max-w-5xl">
            <div class="register-card overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    
                    <!-- Left Side - Inspirational Content -->
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
                    
                    <!-- Right Side - Register Form -->
                    <div class="w-full md:w-3/5 p-6 md:p-7">
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
                        
                        @if(session('warning'))
                            <div class="alert bg-yellow-50 border border-yellow-200 text-yellow-700 px-3 py-2 rounded-xl mb-4 text-xs flex items-start gap-2">
                                <i class="fas fa-clock mt-0.5"></i>
                                <div class="flex-1">{{ session('warning') }}</div>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl mb-4 text-xs">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl mb-4 text-xs">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Please fix the errors below.
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('register') }}" id="registerForm">
                            @csrf
                            
                            <div class="scrollable-form">
                                <!-- Basic Information Section -->
                                <div class="section-title">Basic Information</div>
                                
                                <!-- Full Name -->
                                <div class="form-group">
                                    <label class="block text-gray-700">Full Name <span class="required-star">*</span></label>
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
                                
                                <!-- Email -->
                                <div class="form-group">
                                    <label class="block text-gray-700">Email Address <span class="required-star">*</span></label>
                                    <div class="relative">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" name="email" id="email" required 
                                               class="input-field pl-10"
                                               placeholder="name@reverence.com"
                                               value="{{ old('email') }}">
                                    </div>
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Phone Number -->
                                <div class="form-group">
                                    <label class="block text-gray-700">Phone Number <span class="required-star">*</span></label>
                                    <div class="relative">
                                        <i class="fas fa-phone input-icon"></i>
                                        <input type="tel" name="phone" required
                                               class="input-field pl-10"
                                               placeholder="+250 78X XXX XXX"
                                               value="{{ old('phone') }}">
                                    </div>
                                    @error('phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="form-row">
                                    <!-- Gender -->
                                    <div class="form-group">
                                        <label class="block text-gray-700">Gender <span class="required-star">*</span></label>
                                        <div class="relative">
                                            <i class="fas fa-venus-mars input-icon"></i>
                                            <select name="gender" class="input-field pl-10" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>
                                        @error('gender')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <!-- Date of Birth -->
                                    <div class="form-group">
                                        <label class="block text-gray-700">Date of Birth <span class="required-star">*</span></label>
                                        <div class="relative">
                                            <i class="fas fa-calendar-alt input-icon"></i>
                                            <input type="date" name="date_of_birth" required
                                                   class="input-field pl-10"
                                                   value="{{ old('date_of_birth') }}">
                                        </div>
                                        @error('date_of_birth')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <!-- Marital Status -->
                                    <div class="form-group">
                                        <label class="block text-gray-700">Marital Status <span class="required-star">*</span></label>
                                        <div class="relative">
                                            <i class="fas fa-heart input-icon"></i>
                                            <select name="marital_status" class="input-field pl-10" required>
                                                <option value="">Select Status</option>
                                                <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                                <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                                <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                                <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                            </select>
                                        </div>
                                        @error('marital_status')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Address Information Section -->
                                <div class="section-title">Address Information</div>
                                
                                <!-- Province -->
                                <div class="form-group">
                                    <label class="block text-gray-700">Province <span class="required-star">*</span></label>
                                    <div class="relative">
                                        <i class="fas fa-map-marker-alt input-icon"></i>
                                        <input type="text" name="province" required
                                               class="input-field pl-10"
                                               placeholder="e.g., Kigali, Northern, Southern, Eastern, Western"
                                               value="{{ old('province') }}">
                                    </div>
                                    @error('province')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- District -->
                                <div class="form-group">
                                    <label class="block text-gray-700">District <span class="required-star">*</span></label>
                                    <div class="relative">
                                        <i class="fas fa-city input-icon"></i>
                                        <input type="text" name="district" required
                                               class="input-field pl-10"
                                               placeholder="e.g., Gasabo, Kicukiro, Nyarugenge"
                                               value="{{ old('district') }}">
                                    </div>
                                    @error('district')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Sector -->
                                <div class="form-group">
                                    <label class="block text-gray-700">Sector <span class="required-star">*</span></label>
                                    <div class="relative">
                                        <i class="fas fa-building input-icon"></i>
                                        <input type="text" name="sector" required
                                               class="input-field pl-10"
                                               placeholder="e.g., Kimihurura, Kacyiru, Remera"
                                               value="{{ old('sector') }}">
                                    </div>
                                    @error('sector')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Cell -->
                                <div class="form-group">
                                    <label class="block text-gray-700">Cell <span class="required-star">*</span></label>
                                    <div class="relative">
                                        <i class="fas fa-layer-group input-icon"></i>
                                        <input type="text" name="cell" required
                                               class="input-field pl-10"
                                               placeholder="e.g., Kimihurura Center, Kacyiru Center"
                                               value="{{ old('cell') }}">
                                    </div>
                                    @error('cell')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Village -->
                                <div class="form-group">
                                    <label class="block text-gray-700">Village / Umudugudu <span class="required-star">*</span></label>
                                    <div class="relative">
                                        <i class="fas fa-home input-icon"></i>
                                        <input type="text" name="village" required
                                               class="input-field pl-10"
                                               placeholder="e.g., Jali Center, Kinyaga"
                                               value="{{ old('village') }}">
                                    </div>
                                    @error('village')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Password Section -->
                                <div class="section-title">Security</div>
                                
                                <!-- Password -->
                                <div class="form-group">
                                    <label class="block text-gray-700">Password <span class="required-star">*</span></label>
                                    <div class="relative">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" name="password" id="password" required 
                                               class="input-field pl-10 pr-10"
                                               placeholder="Create a password (min 4 characters)">
                                        <i class="fas fa-eye eye-icon" onclick="togglePassword('password', this)"></i>
                                    </div>
                                    
                                    <div class="password-strength w-full bg-gray-200 rounded-full mt-2 overflow-hidden">
                                        <div id="strengthBar" class="password-strength" style="width: 0%;"></div>
                                    </div>
                                    
                                    <div class="password-requirements mt-2">
                                        <p class="requirement text-xs" id="lengthReq">
                                            <i class="fas fa-circle text-[6px] mr-1"></i> At least 4 characters
                                        </p>
                                    </div>
                                    
                                    @error('password')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Confirm Password -->
                                <div class="form-group">
                                    <label class="block text-gray-700">Confirm Password <span class="required-star">*</span></label>
                                    <div class="relative">
                                        <i class="fas fa-check-circle input-icon"></i>
                                        <input type="password" name="password_confirmation" id="password_confirmation" required 
                                               class="input-field pl-10 pr-10"
                                               placeholder="Confirm your password">
                                        <i class="fas fa-eye eye-icon" onclick="togglePassword('password_confirmation', this)"></i>
                                    </div>
                                    <div id="matchMessage" class="match-indicator"></div>
                                </div>
                            </div>
                            
                            <button type="submit" id="submitBtn" class="btn-register w-full text-white py-2.5 transition mt-4" disabled>
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
    
    <script>
        // Password toggle function
        function togglePassword(fieldId, iconElement) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                iconElement.classList.remove('fa-eye');
                iconElement.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                iconElement.classList.remove('fa-eye-slash');
                iconElement.classList.add('fa-eye');
            }
        }
        
        // Check password strength (only length check - min 4 characters)
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const lengthReq = document.getElementById('lengthReq');
            const strengthBar = document.getElementById('strengthBar');
            
            const isValidLength = password.length >= 4;
            
            if (isValidLength) {
                lengthReq.classList.add('met');
                lengthReq.innerHTML = '<i class="fas fa-check-circle text-[10px] mr-1"></i> At least 4 characters';
                strengthBar.style.width = '100%';
                strengthBar.style.backgroundColor = '#10b981';
                return true;
            } else {
                lengthReq.classList.remove('met');
                lengthReq.innerHTML = '<i class="fas fa-circle text-[6px] mr-1"></i> At least 4 characters';
                strengthBar.style.width = '0%';
                strengthBar.style.backgroundColor = '#e2e8f0';
                return false;
            }
        }
        
        // Check password match
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            const matchMessage = document.getElementById('matchMessage');
            const confirmField = document.getElementById('password_confirmation');
            
            if (confirm.length === 0) {
                matchMessage.innerHTML = '';
                confirmField.classList.remove('valid', 'invalid');
                return false;
            }
            
            if (password === confirm) {
                matchMessage.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1"></i> <span class="text-green-600">Passwords match!</span>';
                confirmField.classList.add('valid');
                confirmField.classList.remove('invalid');
                return true;
            } else {
                matchMessage.innerHTML = '<i class="fas fa-exclamation-circle text-red-500 mr-1"></i> <span class="text-red-600">Passwords do not match</span>';
                confirmField.classList.add('invalid');
                confirmField.classList.remove('valid');
                return false;
            }
        }
        
        // Validate email
        function validateEmail() {
            const email = document.getElementById('email').value;
            const emailField = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email.length === 0) {
                emailField.classList.remove('valid', 'invalid');
                return false;
            }
            
            if (emailRegex.test(email)) {
                emailField.classList.add('valid');
                emailField.classList.remove('invalid');
                return true;
            } else {
                emailField.classList.add('invalid');
                emailField.classList.remove('valid');
                return false;
            }
        }
        
        // Validate name (at least 2 characters)
        function validateName() {
            const name = document.querySelector('input[name="name"]').value;
            const nameField = document.querySelector('input[name="name"]');
            
            if (name.length === 0) {
                nameField.classList.remove('valid', 'invalid');
                return false;
            }
            
            if (name.length >= 2) {
                nameField.classList.add('valid');
                nameField.classList.remove('invalid');
                return true;
            } else {
                nameField.classList.add('invalid');
                nameField.classList.remove('valid');
                return false;
            }
        }
        
        // Validate phone (at least 10 digits)
        function validatePhone() {
            const phone = document.querySelector('input[name="phone"]').value;
            const phoneField = document.querySelector('input[name="phone"]');
            const phoneRegex = /^[\+]?[0-9\s]{10,}$/;
            
            if (phone.length === 0) {
                phoneField.classList.remove('valid', 'invalid');
                return false;
            }
            
            if (phoneRegex.test(phone)) {
                phoneField.classList.add('valid');
                phoneField.classList.remove('invalid');
                return true;
            } else {
                phoneField.classList.add('invalid');
                phoneField.classList.remove('valid');
                return false;
            }
        }
        
        // Validate required text fields
        function validateTextField(fieldName, minLength = 1) {
            const field = document.querySelector(`input[name="${fieldName}"]`);
            const value = field?.value || '';
            
            if (value.length >= minLength) {
                field?.classList.add('valid');
                field?.classList.remove('invalid');
                return true;
            } else {
                field?.classList.add('invalid');
                field?.classList.remove('valid');
                return false;
            }
        }
        
        // Validate select fields
        function validateSelectField(fieldName) {
            const field = document.querySelector(`select[name="${fieldName}"]`);
            const value = field?.value || '';
            
            if (value !== '') {
                field?.classList.add('valid');
                field?.classList.remove('invalid');
                return true;
            } else {
                field?.classList.add('invalid');
                field?.classList.remove('valid');
                return false;
            }
        }
        
        // Validate date field
        function validateDateField() {
            const field = document.querySelector('input[name="date_of_birth"]');
            const value = field?.value || '';
            
            if (value !== '') {
                field?.classList.add('valid');
                field?.classList.remove('invalid');
                return true;
            } else {
                field?.classList.add('invalid');
                field?.classList.remove('valid');
                return false;
            }
        }
        
        // Update submit button
        function updateSubmitButton() {
            const nameValid = document.querySelector('input[name="name"]').value.length >= 2;
            const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(document.getElementById('email').value);
            const phoneValid = /^[\+]?[0-9\s]{10,}$/.test(document.querySelector('input[name="phone"]').value);
            const genderValid = document.querySelector('select[name="gender"]').value !== '';
            const dobValid = document.querySelector('input[name="date_of_birth"]').value !== '';
            const maritalValid = document.querySelector('select[name="marital_status"]').value !== '';
            const provinceValid = document.querySelector('input[name="province"]').value !== '';
            const districtValid = document.querySelector('input[name="district"]').value !== '';
            const sectorValid = document.querySelector('input[name="sector"]').value !== '';
            const cellValid = document.querySelector('input[name="cell"]').value !== '';
            const villageValid = document.querySelector('input[name="village"]').value !== '';
            const passwordValid = document.getElementById('password').value.length >= 4;
            const passwordsMatch = document.getElementById('password').value === document.getElementById('password_confirmation').value;
            const confirmHasValue = document.getElementById('password_confirmation').value.length > 0;
            
            const allValid = nameValid && emailValid && phoneValid && genderValid && dobValid && maritalValid && 
                            provinceValid && districtValid && sectorValid && cellValid && villageValid && 
                            passwordValid && passwordsMatch && confirmHasValue;
            
            const submitBtn = document.getElementById('submitBtn');
            if (allValid) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
        
        // Initialize all validations
        function initializeValidations() {
            const nameInput = document.querySelector('input[name="name"]');
            const emailInput = document.getElementById('email');
            const phoneInput = document.querySelector('input[name="phone"]');
            const genderSelect = document.querySelector('select[name="gender"]');
            const dobInput = document.querySelector('input[name="date_of_birth"]');
            const maritalSelect = document.querySelector('select[name="marital_status"]');
            const provinceInput = document.querySelector('input[name="province"]');
            const districtInput = document.querySelector('input[name="district"]');
            const sectorInput = document.querySelector('input[name="sector"]');
            const cellInput = document.querySelector('input[name="cell"]');
            const villageInput = document.querySelector('input[name="village"]');
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            
            // Add event listeners
            if (nameInput) nameInput.addEventListener('input', function() { validateName(); updateSubmitButton(); });
            if (emailInput) emailInput.addEventListener('input', function() { validateEmail(); updateSubmitButton(); });
            if (phoneInput) phoneInput.addEventListener('input', function() { validatePhone(); updateSubmitButton(); });
            if (genderSelect) genderSelect.addEventListener('change', function() { validateSelectField('gender'); updateSubmitButton(); });
            if (dobInput) dobInput.addEventListener('change', function() { validateDateField(); updateSubmitButton(); });
            if (maritalSelect) maritalSelect.addEventListener('change', function() { validateSelectField('marital_status'); updateSubmitButton(); });
            if (provinceInput) provinceInput.addEventListener('input', function() { validateTextField('province'); updateSubmitButton(); });
            if (districtInput) districtInput.addEventListener('input', function() { validateTextField('district'); updateSubmitButton(); });
            if (sectorInput) sectorInput.addEventListener('input', function() { validateTextField('sector'); updateSubmitButton(); });
            if (cellInput) cellInput.addEventListener('input', function() { validateTextField('cell'); updateSubmitButton(); });
            if (villageInput) villageInput.addEventListener('input', function() { validateTextField('village'); updateSubmitButton(); });
            if (passwordInput) passwordInput.addEventListener('input', function() { checkPasswordStrength(); checkPasswordMatch(); updateSubmitButton(); });
            if (confirmInput) confirmInput.addEventListener('input', function() { checkPasswordMatch(); updateSubmitButton(); });
            
            // Initial validations
            validateName();
            validateEmail();
            validatePhone();
            validateSelectField('gender');
            validateDateField();
            validateSelectField('marital_status');
            validateTextField('province');
            validateTextField('district');
            validateTextField('sector');
            validateTextField('cell');
            validateTextField('village');
            checkPasswordStrength();
            updateSubmitButton();
        }
        
        // Start everything
        document.addEventListener('DOMContentLoaded', function() {
            initializeValidations();
        });
    </script>
</body>
</html>