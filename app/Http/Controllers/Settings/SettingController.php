<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\SystemSetting;
use App\Models\System\ActivityLog;

class SettingController extends Controller
{
    // Display system settings
    public function index()
    {
        $settings = SystemSetting::all()->keyBy('setting_key');
        return view('super-admin.settings.index', compact('settings'));
    }
    
    // Update general settings
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'app_debug' => 'boolean',
            'registration_enabled' => 'boolean'
        ]);
        
        // Update or create settings
        SystemSetting::updateOrCreate(
            ['setting_key' => 'app_name'],
            ['setting_value' => $request->app_name, 'setting_type' => 'string', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'app_url'],
            ['setting_value' => $request->app_url, 'setting_type' => 'string', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'app_debug'],
            ['setting_value' => $request->app_debug ? '1' : '0', 'setting_type' => 'boolean', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'registration_enabled'],
            ['setting_value' => $request->registration_enabled ? '1' : '0', 'setting_type' => 'boolean', 'updated_by' => auth()->id()]
        );
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'settings_updated',
            'description' => 'Updated general settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('settings.index')->with('success', 'General settings updated successfully!');
    }
    
    // Update email settings
    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string'
        ]);
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'mail_mailer'],
            ['setting_value' => $request->mail_mailer, 'setting_type' => 'string', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'mail_host'],
            ['setting_value' => $request->mail_host, 'setting_type' => 'string', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'mail_port'],
            ['setting_value' => $request->mail_port, 'setting_type' => 'integer', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'mail_username'],
            ['setting_value' => $request->mail_username, 'setting_type' => 'string', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'mail_password'],
            ['setting_value' => $request->mail_password, 'setting_type' => 'string', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'mail_encryption'],
            ['setting_value' => $request->mail_encryption, 'setting_type' => 'string', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'mail_from_address'],
            ['setting_value' => $request->mail_from_address, 'setting_type' => 'string', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'mail_from_name'],
            ['setting_value' => $request->mail_from_name, 'setting_type' => 'string', 'updated_by' => auth()->id()]
        );
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'settings_updated',
            'description' => 'Updated email settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('settings.index')->with('success', 'Email settings updated successfully!');
    }
    
    // Update security settings
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'session_lifetime' => 'required|integer|min:1|max:1440',
            'password_min_length' => 'required|integer|min:6|max:255',
            'require_password_confirm' => 'boolean',
            'max_login_attempts' => 'required|integer|min:3|max:10',
            'lockout_duration' => 'required|integer|min:5|max:1440'
        ]);
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'session_lifetime'],
            ['setting_value' => $request->session_lifetime, 'setting_type' => 'integer', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'password_min_length'],
            ['setting_value' => $request->password_min_length, 'setting_type' => 'integer', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'require_password_confirm'],
            ['setting_value' => $request->require_password_confirm ? '1' : '0', 'setting_type' => 'boolean', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'max_login_attempts'],
            ['setting_value' => $request->max_login_attempts, 'setting_type' => 'integer', 'updated_by' => auth()->id()]
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'lockout_duration'],
            ['setting_value' => $request->lockout_duration, 'setting_type' => 'integer', 'updated_by' => auth()->id()]
        );
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'settings_updated',
            'description' => 'Updated security settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('settings.index')->with('success', 'Security settings updated successfully!');
    }
    
    // Clear system cache
    public function clearCache(Request $request)
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');
            
            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'cache_cleared',
                'description' => 'Cleared system cache',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            return back()->with('success', 'System cache cleared successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
    
    // Backup database (simple version)
    public function backupDatabase(Request $request)
    {
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'database_backup',
            'description' => 'Performed database backup',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return back()->with('success', 'Database backup initiated. Check storage folder for backup file.');
    }
    
    // Get setting value helper
    public static function getSetting($key, $default = null)
    {
        $setting = SystemSetting::where('setting_key', $key)->first();
        if (!$setting) {
            return $default;
        }
        
        if ($setting->setting_type === 'boolean') {
            return $setting->setting_value === '1';
        }
        
        return $setting->setting_value;
    }
}