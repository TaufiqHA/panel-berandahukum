<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use DataTables;
use DB;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Kategori Barang", $menu)){
            return view('master.kategori_barang');
        }else{
            return abort(403);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $create = Kategori::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        return response()->json($create);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return Datatables::of(Kategori::all())->addIndexColumn()->addColumn('action', function ($row) {
            $btn = '<div class="button"><button class="btn btn-icon btn-info btn-edit" data-id="' . $row->id . '" data-name="' . $row->nama_kategori . '"><i class="far fa-edit"></i></button> <button data-id="' . $row->id . '" href="#" class="btn btn-icon btn-danger btn-delete"><i class="far fas fa-trash"></i></button></div>';
            return $btn;
        })->rawColumns(['action'])->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $update = Kategori::where('id', $request->id)->update([
            'nama_kategori' => $request->edit_nama_kategori,
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
        $delete = Kategori::destroy($request->id);
        return response()->json($delete);
    }

    public function select(Request $request)
    {
        $search = '';
        $data = Kategori::select("id", "nama_kategori as text")
            ->where('nama_kategori', 'LIKE', "%$search%")
            ->get();
        $results = array(
            "results" => $data,
        );
        return response()->json($results);
    }

    public function check()
    {
        $checks = DB::table('penjualans')
                ->join('detail_penjualans', 'penjualans.id', '=', 'detail_penjualans.penjualan_id')
                ->join('gudang_barangs', 'gudang_barangs.id', '=', 'detail_penjualans.gudang_barang_id')
                ->select('penjualans.toko_id as penjualan_toko_id', 'detail_penjualans.id', 'gudang_barangs.id', 'gudang_barangs.toko_id')
                ->get();
        $test = [];
        foreach ($checks as $key => $check) {
            if($check->penjualan_toko_id != $check->toko_id){
                $test[] = $check;
            }
        }
        dd($test);
    }
}
