<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Toko;
use App\Models\StockIn;
use App\Models\SerialNumber;
use App\Models\GudangBarang;
use App\Models\QuotationDetail;
use App\Models\DetailPenjualan;
use App\Models\DetailBarangKeluar;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Barang", $menu)){
            return view('master.barang');
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
        $request->validate([
            'kategori_barang' => 'required|exists:kategoris,id',
            'nama_barang' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'satuan' => 'nullable|string|max:100',
            'warna' => 'nullable|string|max:100',
            'berat' => 'nullable|string|max:100',
            'ukuran' => 'nullable|string|max:100',
            'harga' => 'required|string',
            'wajib_serial_number' => 'required|boolean',
            'keterangan' => 'nullable|string',
        ]);

        $price_list = $request->harga;
        if(preg_match("/^[0-9,]+$/", $request->harga)){
            $price_list = str_replace(',', '',$request->harga);
        }
        $create = Barang::create([
            "kategori_id" => $request->kategori_barang,
            "nama_product" => $request->nama_barang,
            "merk" => $request->merk,
            "satuan" => $request->satuan,
            "warna" => $request->warna,
            "berat" => $request->berat,
            "ukuran" => $request->ukuran,
            "harga" => $price_list,
            "wajib_serial_number" => $request->wajib_serial_number,
            "keterangan" => $request->keterangan,
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
        $data = array();

        $barangs = Barang::with('kategori');
        if($request->merk != null){
            $barangs = $barangs->where('merk', $request->merk);
        }
        if($request->kategori != null){
            $barangs = $barangs->where('kategori_id', $request->kategori);
        }
        $barangs = $barangs->get();
        foreach($barangs as $key => $barang){
            if($barang->wajib_serial_number == 1){
                $barang->wajib_serial_number = 'Iya';
            }else{
                $barang->wajib_serial_number = 'Tidak';
            }
            $barang->harga = number_format($barang->harga);
            $data[$key] = $barang;
        }

        if ($request->print) {
            return view('master.export_barang' , compact('data'));
        }

        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
            $btn = '<div class="button"><button class="btn btn-icon btn-info btn-edit" data-id="'.$row->id.'"><i class="far fa-edit"></i></button> <button data-id="'.$row->id.'" href="#" class="btn btn-icon btn-danger btn-delete"><i class="far fas fa-trash"></i></button></div>';
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
        $edit = Barang::with('kategori')->find($id);
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
        $request->validate([
            'id' => 'required|exists:barangs,id',
            'kategori_barang_edit' => 'required|exists:kategoris,id',
            'nama_barang_edit' => 'required|string|max:255',
            'merk_edit' => 'nullable|string|max:255',
            'satuan_edit' => 'nullable|string|max:100',
            'warna_edit' => 'nullable|string|max:100',
            'berat_edit' => 'nullable|string|max:100',
            'ukuran_edit' => 'nullable|string|max:100',
            'harga_edit' => 'required|string',
            'wajib_serial_number_edit' => 'required|boolean',
            'keterangan_edit' => 'nullable|string',
        ]);

        $price_list = $request->harga_edit;
        if(preg_match("/^[0-9,]+$/", $request->harga_edit)){
            $price_list = str_replace(',', '',$request->harga_edit);
        }
        $update = Barang::where('id',$request->id)->update([
            "kategori_id" => $request->kategori_barang_edit,
            "nama_product" => $request->nama_barang_edit,
            "merk" => $request->merk_edit,
            "satuan" => $request->satuan_edit,
            "warna" => $request->warna_edit,
            "berat" => $request->berat_edit,
            "ukuran" => $request->ukuran_edit,
            "harga" => $price_list,
            "wajib_serial_number" => $request->wajib_serial_number_edit,
            "keterangan" => $request->keterangan_edit,
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
        $delete = Barang::destroy($request->id);
        StockIn::where('barang_id', $request->id)->delete();
        QuotationDetail::where('barang_id', $request->id)->delete();
        GudangBarang::where('barang_id', $request->id)->delete();
        DetailPenjualan::where('barang_id', $request->id)->delete();
        DetailBarangKeluar::where('barang_id', $request->id)->delete();
        return response()->json($delete);
    }

    public function select(Request $request)
    {
        $search = $request->term;
        $data = Barang::select("id", DB::raw('CONCAT(nama_product," ",warna) as text'))
            ->where('nama_product', 'LIKE', "%$search%")
            ->get();
        if (!empty($data)) {
            $results = array(
                "results" => $data,
            );
        }else{
            $results = array(
                "results" => '',
            );
        }
     
        return response()->json($results);
    }

    public function selectmerk(Request $request)
    {   
        $search = $request->term;
        $data = Barang::select("merk","merk as text")
            ->where('merk', 'LIKE', "%$search%")
            ->groupBy("merk")
            ->get();
        $data = $data->map(function($data){
            return [
               'id' => $data->merk,
               'text' => $data->text,
            ];
        });
        $results = array(
            "results" => $data,
        );
        return response()->json($results);
    }
    
    
    public function getTotalBarangByToko(Request $request)
    {
        $user = Auth::user();
        $query = GudangBarang::where('barang_id', $request->id_barang)->where('status',1);
        $query->where('toko_id', $request->id_toko);
        $data = $query->count();
        return response()->json($data);
    }

    public function getTotalBarangByTokoNew(Request $request)
    {
        $user = Auth::user();
        $query = GudangBarang::where('barang_id', $request->id_barang)->where('toko_id', $request->id_toko);
        $data = $query->count();
        return response()->json($data);
    }

    public function get($id)
    {
        $barangs = Barang::where('id', $id)->first();
        return response()->json($barangs);
    }



}
