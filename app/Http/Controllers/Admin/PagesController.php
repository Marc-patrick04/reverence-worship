<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\Page;
use App\Models\System\Feature;
use App\Models\User\ActivityLog;

class PagesController extends Controller
{
    // Display all pages
    public function index()
    {
        $pages = Page::orderBy('sort_order')->get();
        return view('super-admin.pages.index', compact('pages'));
    }
    
    // Show create page form
    public function create()
    {
        return view('super-admin.pages.create');
    }
    
    // Store new page
    public function store(Request $request)
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
        
        return redirect()->route('pages.index')->with('success', 'Page created successfully!');
    }
    
    // Show edit page form
    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('super-admin.pages.edit', compact('page'));
    }
    
    // Update page
    public function update(Request $request, $id)
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
        
        $page->name = strtolower(str_replace(' ', '-', $request->name));
        $page->display_name = $request->display_name;
        $page->icon = $request->icon;
        $page->route = $request->route;
        $page->sort_order = $request->sort_order ?? 999;
        $page->is_active = $request->is_active ?? true;
        $page->save();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'page_updated',
            'description' => 'Updated page: ' . $page->display_name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('pages.index')->with('success', 'Page updated successfully!');
    }
    
    // Delete page
    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        
        // Check if page has features
        if ($page->features()->count() > 0) {
            return back()->with('error', 'Cannot delete page. Please delete its features first.');
        }
        
        $pageName = $page->display_name;
        $page->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'page_deleted',
            'description' => 'Deleted page: ' . $pageName,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        return redirect()->route('pages.index')->with('success', 'Page deleted successfully!');
    }
    
    // Features Management for a page
    public function features($pageId)
    {
        $page = Page::findOrFail($pageId);
        $features = Feature::where('page_id', $pageId)->get();
        return view('super-admin.pages.features', compact('page', 'features'));
    }
    
    // Store feature
    public function storeFeature(Request $request, $pageId)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:features,name,NULL,id,page_id,' . $pageId,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        $feature = Feature::create([
            'page_id' => $pageId,
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
        
        return redirect()->route('pages.features', $pageId)->with('success', 'Feature created successfully!');
    }
    
    // Delete feature
    public function destroyFeature($pageId, $featureId)
    {
        $feature = Feature::findOrFail($featureId);
        $featureName = $feature->display_name;
        $feature->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'feature_deleted',
            'description' => 'Deleted feature: ' . $featureName,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        
        return redirect()->route('pages.features', $pageId)->with('success', 'Feature deleted successfully!');
    }
    
    // Edit feature
    public function editFeature($pageId, $featureId)
    {
        $page = Page::findOrFail($pageId);
        $feature = Feature::findOrFail($featureId);
        return view('super-admin.pages.edit-feature', compact('page', 'feature'));
    }
    
    // Update feature
    public function updateFeature(Request $request, $pageId, $featureId)
    {
        $feature = Feature::findOrFail($featureId);
        
        $request->validate([
            'name' => 'required|string|max:100|unique:features,name,' . $featureId . ',id,page_id,' . $pageId,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        $feature->name = strtolower(str_replace(' ', '-', $request->name));
        $feature->display_name = $request->display_name;
        $feature->description = $request->description;
        $feature->save();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'feature_updated',
            'description' => 'Updated feature: ' . $feature->display_name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        return redirect()->route('pages.features', $pageId)->with('success', 'Feature updated successfully!');
    }
}