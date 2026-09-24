<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Auth;
use Yajra\DataTables\Facades\DataTables;
use DB;

class SupplierController extends Controller
{
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
       
       
        if(auth()->user()->status == 1 || in_array("Supplier", $menu)){
            $user = Auth::user();
            return view('master.supplier', compact('user'));
        }else{
            return abort(403);
        }
    }
    
    
    
    public function store(Request $request)
    {
		
        $create = Supplier::create([
            "nama_supplier" => $request->nama_supplier,
            "sales" => $request->nama_sales,
            "alamat" => $request->alamat,
        ]);

        return response()->json($create);
    }
    
    public function show(Request $request)
    {
        $data = array();
        $suppliers = Supplier::get();
        foreach($suppliers as $key => $row){
            
            $data[$key] = $row;
        }

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
            $btn = '<div class="button"><button class="btn btn-icon btn-info btn-edit" data-id="'.$row->id.'"><i class="far fa-edit"></i></button> <button data-id="'.$row->id.'" href="#" class="btn btn-icon btn-danger btn-delete"><i class="far fas fa-trash"></i></button></div>';
            return $btn;
        })->rawColumns(['action'])->make(true);
    }
    
     /* Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = Supplier::find($id);
        return response()->json($edit);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $update = Supplier::where('id',$request->id)->update([
            "nama_supplier" => $request->nama_supplier_edit,
            "sales" => $request->nama_sales_edit,
            "alamat" => $request->alamat_edit,
        ]);
        return response()->json($update);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $delete = Supplier::destroy($request->id);
        return response()->json($delete);
    }
    
    public function select(Request $request)
    {
        $search = $request->term;
        $data = Supplier::select("id", DB::raw("CONCAT(nama_supplier,' / ',sales) as text"))
            ->where('nama_supplier', 'LIKE', "%$search%")
            ->get();
        $results = array(
            "results" => $data,
        );
        return response()->json($results);
    }
}
