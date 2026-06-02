<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\Page;
use App\Models\System\Feature;
use App\Models\System\ActivityLog;

class PermissionManagerController extends Controller
{
    // Main permission manager page
    public function index()
    {
        $pages = Page::orderBy('sort_order')->get();
        $features = Feature::with('page')->orderBy('page_id')->get();
        
        return view('super-admin.permission-manager', compact('pages', 'features'));
    }
    
    // Create new page
    public function storePage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:pages',
            'display_name' => 'required|string|max:255',
            'icon' => 'required|string|max:50',
            'route' => 'nullable|string|max:255',
            'sort_order' => 'integer'
        ]);
        
        $page = Page::create([
            'name' => strtolower(str_replace(' ', '-', $request->name)),
            'display_name' => $request->display_name,
            'icon' => $request->icon,
            'route' => $request->route,
            'sort_order' => $request->sort_order ?? 999,
            'is_active' => true
        ]);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'page_created',
            'description' => 'Created page: ' . $page->display_name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('permission-manager.index')->with('success', 'Page created successfully!');
    }
    
    // Update page
    public function updatePage(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100|unique:pages,name,' . $id,
            'display_name' => 'required|string|max:255',
            'icon' => 'required|string|max:50',
            'route' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'is_active' => 'boolean'
        ]);
        
        $page->update([
            'name' => strtolower(str_replace(' ', '-', $request->name)),
            'display_name' => $request->display_name,
            'icon' => $request->icon,
            'route' => $request->route,
            'sort_order' => $request->sort_order ?? 999,
            'is_active' => $request->has('is_active')
        ]);
        
        return redirect()->route('permission-manager.index')->with('success', 'Page updated successfully!');
    }
    
    // Delete page
    public function deletePage($id)
    {
        $page = Page::findOrFail($id);
        
        // Check if page has features
        if ($page->features()->count() > 0) {
            return back()->with('error', 'Cannot delete page. Please delete its features first.');
        }
        
        $page->delete();
        
        return redirect()->route('permission-manager.index')->with('success', 'Page deleted successfully!');
    }
    
    // Create new feature for a page
    public function storeFeature(Request $request)
    {
        $request->validate([
            'page_id' => 'required|exists:pages,id',
            'name' => 'required|string|max:100',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        // Check for duplicate within same page
        $exists = Feature::where('page_id', $request->page_id)
            ->where('name', strtolower(str_replace(' ', '-', $request->name)))
            ->exists();
            
        if ($exists) {
            return back()->with('error', 'Feature with this name already exists for this page.');
        }
        
        $feature = Feature::create([
            'page_id' => $request->page_id,
            'name' => strtolower(str_replace(' ', '-', $request->name)),
            'display_name' => $request->display_name,
            'description' => $request->description
        ]);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'feature_created',
            'description' => 'Created feature: ' . $feature->display_name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('permission-manager.index')->with('success', 'Feature created successfully!');
    }
    
    // Update feature
    public function updateFeature(Request $request, $id)
    {
        $feature = Feature::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        $feature->update([
            'name' => strtolower(str_replace(' ', '-', $request->name)),
            'display_name' => $request->display_name,
            'description' => $request->description
        ]);
        
        return redirect()->route('permission-manager.index')->with('success', 'Feature updated successfully!');
    }
    
    // Delete feature
    public function deleteFeature($id)
    {
        $feature = Feature::findOrFail($id);
        $feature->delete();
        
        return redirect()->route('permission-manager.index')->with('success', 'Feature deleted successfully!');
    }
}