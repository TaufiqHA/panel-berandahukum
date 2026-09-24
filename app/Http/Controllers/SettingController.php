<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Toko;
use Auth;
use Yajra\DataTables\Facades\DataTables;
use DB;


class SettingController extends Controller
{
     public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        $setting = Setting::first();
       //s echo auth()->user()->status;exit;
        //echo $setting->cara_pembayaran.'<br /><br /><br />';
        //print_r($setting);exit;
        if(auth()->user()->status == 1 || in_array("Setting", $menu)){
            $user = Auth::user();
            return view('master.setting', compact('user','setting'));
        }else{
            return abort(403);
        }
    }
    
    
    
    public function store(Request $request)
    {
        $create = Setting::create([
            "toko_id" => $request->nama_toko,
            "cara_pembayaran" => $request->cara_pembayaran,
        ]);

        return response()->json($create);
    }
    
    public function show(Request $request)
    {
        $data = array();
        $settings = Setting::with('toko')->get();
        foreach($settings as $key => $row){
            
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
        $edit = Setting::with('toko')->find($id);
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
        $update = Setting::where('id',$request->id)->update([
            "toko_id" => $request->nama_toko_edit,
            "cara_pembayaran" => $request->cara_pembayaran_edit,
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
        $delete = Setting::destroy($request->id);
        return response()->json($delete);
    }
}
