<?php

namespace App\Http\Controllers\admin;

use App\Enum\status;
use App\Http\Controllers\Controller;
use App\Models\unit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $units = Unit::select(['id', 'name', 'shortname', 'no_of_product', 'status', 'created_at'])->get();
        return view('admin.unit.list',compact('units')); // Return view for normal page load
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.unit.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name'          =>'required|max:255',
            'shortname'     =>'required|max:255',
            'no_of_product' =>'required|numeric|max:15',
            'status'        =>[new Enum(status::class)],
        ]);

        try{

            DB::beginTransaction();
            // If validation passes, save data
            Unit::create([
                'name'          => $request->name,
                'shortname'     => $request->shortname,
                'status'        => $request->status,
                'no_of_product' => $request->no_of_product,
            ]);

            DB::commit();

            return redirect()->back()->with('success','Unit Added Successfully');
            
        }catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error','Sorry Unit Colud Not Add Issue:'.$e->getMessage());
        }
       
    }

    /**
     * Display the specified resource.
     */
    public function show(unit $unit)
    {
        //
        return view('admin.unit.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(unit $unit)
    {
        //
        return view('admin.unit.edit',compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, unit $unit)
    {
        $request->validate([
            'name'          =>'required|max:255',
            'shortname'     =>'required|max:255',
            'no_of_product' =>'required|numeric|max:15',
            'status'        =>[new Enum(status::class)],
        ]);

        try{
            DB::beginTransaction();

            $unit->name             = $request->name;
            $unit->shortname        = $request->shortname;
            $unit->no_of_product    = $request->no_of_product;
            $unit->status           = $request->status;
            $unit->save();
            DB::commit(); // Commit transaction if delete is successful
            return redirect()->back()->with('success','Unit Updated Successfully');
        }catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error','Unit could not Update'.$e->getMessage());
        }
        
    }
    
    public function destroy(Unit $unit)
    {
        try {
            DB::beginTransaction();
    
            // Check if the unit exists before deletion
            if (!$unit) {
                return redirect()->back()->with('error', 'Unit not found');
            }
    
            $unit->delete();
            
            DB::commit(); // Commit transaction if delete is successful
    
            return redirect()->back()->with('success', 'Unit Deleted Successfully');
        } catch (Exception $e) {
            DB::rollBack(); // Rollback if an error occurs
            return redirect()->back()->with('error', 'Unit could not be deleted: ' . $e->getMessage());
        }
    }
    

    public function destroyAll(Request $requests){

        $requests->validate([
            'unit_ids'  => 'required|array'
        ]);
        
        try{

            DB::beginTransaction();
            unit::whereIn('id',$requests->unit_ids)->delete();

        }catch(Exception $e){
            
            DB::rollBack();
            return redirect()->back()->with('fail',$e->getMessage());
        }

    }
}
