<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = CmsPage::orderBy('id', 'desc')->paginate(15);
        return view('admin.cms_pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cms_pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['status'] = $request->has('status');
        
        // Ensure slug uniqueness
        $count = CmsPage::where('slug', $validated['slug'])->count();
        if ($count > 0) {
            $validated['slug'] = $validated['slug'] . '-' . time();
        }

        CmsPage::create($validated);

        return redirect()->route('admin.cms-pages.index')
            ->with('success', 'CMS Page created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $page = CmsPage::findOrFail($id);
        return view('admin.cms_pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $page = CmsPage::findOrFail($id);

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $validated['status'] = $request->has('status');

        // Only update slug if title changed, to avoid breaking links unnecessarily
        if ($page->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
            
            $count = CmsPage::where('slug', $validated['slug'])->where('id', '!=', $page->id)->count();
            if ($count > 0) {
                $validated['slug'] = $validated['slug'] . '-' . time();
            }
        }

        $page->update($validated);

        return redirect()->route('admin.cms-pages.index')
            ->with('success', 'CMS Page updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $page = CmsPage::findOrFail($id);
        
        // Prevent deleting core pages like Privacy Policy and Terms and Conditions if needed, 
        // but for now we'll allow deleting any page.
        $page->delete();

        return redirect()->route('admin.cms-pages.index')
            ->with('success', 'CMS Page deleted successfully.');
    }
}
