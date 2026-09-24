<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StockIn;
use App\Models\SerialNumber;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\GudangBarang;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Stock", $menu)){
            $user = Auth::user();
            return view('master.stock', compact('user'));
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

    }


    public function getDataStock($id)
    {
        $user = Auth::user();
        $query = StockIn::where('barang_id', $id);
        if($user->status != 1){
            $query->where('toko_id', $user->toko_id);
        }
        $getDataStock = $query->get();
        $stockIn = array();
        foreach ($getDataStock as $key => $value) {
            $stockIn[] = $value->id;
        }
        $count = SerialNumber::whereIn('stock_in_id', $stockIn)->count();

        return $count;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $data = array();
        $barangs = Barang::with('kategori')->get();
        foreach($barangs as $key => $barang){
            if($barang->wajib_serial_number == 1){
                $barang->wajib_serial_number = 'Iya';
            }else{
                $barang->wajib_serial_number = 'Tidak';
            }
            //$barangs[$key]['nama_product'] = $barang['nama_product'].'-'.$barang['id'];
            $data[$key] = $barang;
            $data[$key]['total'] = $this->stock($barang->id);
        }

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
            $user = Auth::user();
            $btn_market = '';
            $btn_delete = '';
            // if($user->status == 1){
                    $btn_market = '<button class="btn btn-icon btn-success btn-warning view-stock" data-id="'.$row->id.'" data-barang="'.$row->nama_product.'" data-satuan="'.$row->satuan.'"><i class="fas fa-store"></i></button>';
                    $btn_delete = '<button data-id="'.$row->id.'" data-barang="'.$row->nama_product.'" data-satuan="'.$row->satuan.'" href="#" class="btn btn-icon btn-info btn-delete view-sn"><i class="fas fa-th"></i></button>';
            // }
            $btn = '<div class="button">'.$btn_market.' '.$btn_delete.'</div>';
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
        $user = Auth::user();
        $query = GudangBarang::with('toko')->where('barang_id', $id)->select(DB::raw('count(*) as total, toko_id'))->groupBy('toko_id');
        $data = $query->get();
        return $data;
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
        $data = array('serial_number' => $request->serial_number);
        $update = GudangBarang::where('id', $request->id)->update($data);
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
        $delete = GudangBarang::destroy($request->id);
        return response()->json($delete);
    }

    public function select(Request $request)
    {
        $search = $request->term;
        $data = Barang::select("id", "nama_product as text")
            ->where('nama_product', 'LIKE', "%$search%")
            ->get();
        $results = array(
            "results" => $data,
        );
        return response()->json($results);
    }

    public function toko(Request $request, $id)
    {
        $user = Auth::user();
        $search = $request->term;
        $query = DB::table('gudang_barangs')
                    ->join('serial_numbers', 'serial_numbers.id', '=', 'gudang_barangs.serial_number_id')
                    ->join('stock_ins', 'stock_ins.id', '=', 'serial_numbers.stock_in_id')
                    ->join('barangs', 'barangs.id', '=', 'stock_ins.barang_id')
                    ->where('barangs.nama_product', 'LIKE', "%$search%")
                    ->select('barangs.id as id', 'barangs.nama_product as text')
                    ->groupBy('id', 'text');
        if($user->status != 1){
            $query->where('gudang_barangs.toko_id', $id);
        }
        $data = $query->get();

        $results = array(
            "results" => $data,
        );
        return response()->json($results);
    }

    public function getTotalBarangByToko(Request $request)
    {
        $query = DB::table('gudang_barangs')
                    ->join('serial_numbers', 'serial_numbers.id', '=', 'gudang_barangs.serial_number_id')
                    ->join('stock_ins', 'stock_ins.id', '=', 'serial_numbers.stock_in_id')
                    ->where('gudang_barangs.status', 1)
                    ->where('gudang_barangs.toko_id', $request->id_toko)
                    ->where('stock_ins.barang_id', $request->id_barang)
                    ->select(DB::raw('count(gudang_barangs.id) as total'))->first();
        return response()->json($query);
    }

    public function get(Request $request)
    {

        $serial_number = GudangBarang::where('id', '!=', $request->id)->where('serial_number', $request->serial_number)->first();
        return response()->json($serial_number);
    }

    public function stock($id)
    {
        $user = Auth::user();
        $query = GudangBarang::where('barang_id', $id);   
        return $query->count();
    }

    public function serialNumber($id)
    {
        $query = GudangBarang::with(['toko', 'barang'])->where('barang_id', $id)->orderBy('toko_id');
        $data = $query->get();
        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($row) use ($id) {
            $btn = '';
            $user = Auth::user();
            if($user->status != 1){
                // if($row->toko_id == $user->toko_id){
                    $btn = '<div class="button"></div>';
                // }
            }else{
                $btn = '<div class="button"><button class="btn btn-icon btn-info btn-edit-sn" data-barang-id="'.$id.'" data-id="'.$row->id.'" data-sn="'.$row->serial_number.'"><i class="far fa-edit"></i></button> <button data-barang-id="'.$id.'" data-id="'.$row->id.'" href="#" class="btn btn-icon btn-danger btn-delete-sn"><i class="far fas fa-trash"></i></button></div>';
            }
            return $btn;
        })->rawColumns(['action'])->make(true);
    }
}
