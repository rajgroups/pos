<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\category;
use Illuminate\Http\Request;
use app\Enum\status;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use PHPUnit\Framework\Constraint\FileExists;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // List Route View
        $categorys = category::all();
        return view('admin.category.list',compact('categorys'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // dd($request->all());
        $request->validate([
            'name'      => 'required|unique:tbl_category,name',
            'slug'      => 'required|unique:tbl_category,slug',
            'status'    => 'required|in:active,inactive', // Added 'required'
        ]);

        try{
            DB::beginTransaction();

            $category  = new category(); 
            $category->name     = $request->name;
            $category->slug     = $request->slug;
            $category->status   = $request->status;

            // Check if the image is uploaded
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                // Generate a unique name using timestamp and original extension
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                // Move image to the storage/public/uploads/category folder
                $image->move(public_path('uploads/category'), $imageName);

                // Save only the image name in the database
                $category->image = $imageName;
            }
            $category->save();

            DB::commit();

            return redirect()->back()->with('success','Category Added Successfully');
        }catch (Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error','Category Added Failure,Issue:'.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(category $category)
    {
        //
        try {
            DB::beginTransaction();
    
            // Check if the unit exists before deletion
            if (!$category) {
                return redirect()->back()->with('error', 'Category not found');
            }
    
            $category->delete();
            
            DB::commit(); // Commit transaction if delete is successful
    
            return redirect()->back()->with('success', 'Category Deleted Successfully');
        } catch (Exception $e) {
            DB::rollBack(); // Rollback if an error occurs
            return redirect()->back()->with('error', 'Category could not be deleted: ' . $e->getMessage());
        }
    }
}
