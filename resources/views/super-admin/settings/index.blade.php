@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">System Settings</h1>
            <p class="text-gray-600 mt-1">Configure system-wide settings and preferences</p>
        </div>
        <div class="flex space-x-3">
            <form action="{{ route('settings.clear-cache') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-broom mr-2"></i>
                    Clear Cache
                </button>
            </form>
            <form action="{{ route('settings.backup') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-database mr-2"></i>
                    Backup Database
                </button>
            </form>
        </div>
    </div>
    
    <!-- Settings Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8">
                <button onclick="showTab('general')" id="tab-general" class="tab-btn py-2 px-1 border-b-2 font-medium text-sm transition">
                    <i class="fas fa-globe mr-2"></i>
                    General Settings
                </button>
                <button onclick="showTab('email')" id="tab-email" class="tab-btn py-2 px-1 border-b-2 font-medium text-sm transition">
                    <i class="fas fa-envelope mr-2"></i>
                    Email Settings
                </button>
                <button onclick="showTab('security')" id="tab-security" class="tab-btn py-2 px-1 border-b-2 font-medium text-sm transition">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Security Settings
                </button>
            </nav>
        </div>
    </div>
    
    <!-- General Settings Tab -->
    <div id="general-settings" class="settings-tab">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">General Settings</h3>
            <form method="POST" action="{{ route('settings.update.general') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                        <input type="text" name="app_name" required 
                               value="{{ $settings['app_name']->setting_value ?? 'Reverence Worship' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application URL</label>
                        <input type="url" name="app_url" required 
                               value="{{ $settings['app_url']->setting_value ?? 'http://localhost:8000' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="app_debug" value="1" 
                                   {{ (isset($settings['app_debug']) && $settings['app_debug']->setting_value == '1') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Enable Debug Mode</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Only enable in development environment</p>
                    </div>
                    
                    <div>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="registration_enabled" value="1" 
                                   {{ (isset($settings['registration_enabled']) && $settings['registration_enabled']->setting_value == '1') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Enable User Registration</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Allow new users to register</p>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-save mr-2"></i>
                        Save General Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Email Settings Tab -->
    <div id="email-settings" class="settings-tab hidden">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Email Configuration</h3>
            <form method="POST" action="{{ route('settings.update.email') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mail Driver</label>
                        <select name="mail_mailer" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="smtp" {{ (isset($settings['mail_mailer']) && $settings['mail_mailer']->setting_value == 'smtp') ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ (isset($settings['mail_mailer']) && $settings['mail_mailer']->setting_value == 'sendmail') ? 'selected' : '' }}>Sendmail</option>
                            <option value="log" {{ (isset($settings['mail_mailer']) && $settings['mail_mailer']->setting_value == 'log') ? 'selected' : '' }}>Log (Local Only)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                        <input type="text" name="mail_host" 
                               value="{{ $settings['mail_host']->setting_value ?? 'smtp.mailtrap.io' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                        <input type="number" name="mail_port" 
                               value="{{ $settings['mail_port']->setting_value ?? '2525' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                        <select name="mail_encryption" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="tls" {{ (isset($settings['mail_encryption']) && $settings['mail_encryption']->setting_value == 'tls') ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ (isset($settings['mail_encryption']) && $settings['mail_encryption']->setting_value == 'ssl') ? 'selected' : '' }}>SSL</option>
                            <option value="" {{ (isset($settings['mail_encryption']) && $settings['mail_encryption']->setting_value == '') ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="mail_username" 
                               value="{{ $settings['mail_username']->setting_value ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="mail_password" 
                               value="{{ $settings['mail_password']->setting_value ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Address</label>
                        <input type="email" name="mail_from_address" 
                               value="{{ $settings['mail_from_address']->setting_value ?? 'hello@example.com' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                        <input type="text" name="mail_from_name" 
                               value="{{ $settings['mail_from_name']->setting_value ?? 'Reverence Worship' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-save mr-2"></i>
                        Save Email Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Security Settings Tab -->
    <div id="security-settings" class="settings-tab hidden">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Security Settings</h3>
            <form method="POST" action="{{ route('settings.update.security') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Session Lifetime (minutes)</label>
                        <input type="number" name="session_lifetime" 
                               value="{{ $settings['session_lifetime']->setting_value ?? '120' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Default: 120 minutes (2 hours)</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Password Length</label>
                        <input type="number" name="password_min_length" 
                               value="{{ $settings['password_min_length']->setting_value ?? '6' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Minimum: 6 characters</p>
                    </div>
                    
                    <div>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="require_password_confirm" value="1" 
                                   {{ (isset($settings['require_password_confirm']) && $settings['require_password_confirm']->setting_value == '1') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Require Password Confirmation for Sensitive Actions</span>
                        </label>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Login Attempts</label>
                        <input type="number" name="max_login_attempts" 
                               value="{{ $settings['max_login_attempts']->setting_value ?? '5' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Number of failed attempts before lockout</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lockout Duration (minutes)</label>
                        <input type="number" name="lockout_duration" 
                               value="{{ $settings['lockout_duration']->setting_value ?? '15' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">How long to lock out after max attempts</p>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-save mr-2"></i>
                        Save Security Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById(`${tabName}-settings`).classList.remove('hidden');
    
    // Active the clicked tab button
    const activeBtn = document.getElementById(`tab-${tabName}`);
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    activeBtn.classList.add('border-blue-600', 'text-blue-600');
}
</script>
@endsection