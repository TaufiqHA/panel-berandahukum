<?php

namespace App\Http\Controllers;

use App\Models\DataBarangKeluar;
use App\Models\GudangBarang;
use App\Models\Toko;
use App\Models\BarangKeluar;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\SerialNumber;
use Illuminate\Http\Request;
use App\Models\Barang;
use Yajra\DataTables\Facades\DataTables;
use DB;
use PDF;
use Auth;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Barang Keluar", $menu)){
            $user = Auth::user();
            return view('barang-keluar.list', compact('user'));
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
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Barang Keluar", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            $barang_keluar = BarangKeluar::orderBy('id', 'DESC')->first();
            return view('barang-keluar.create', compact('barang_keluar', 'nama_toko'));
        }else{
            return abort(403);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $barang_keluar = array(
            'date' => date('Y-m-d', strtotime($request->date)),
            'kode_barang_keluar' => $request->ref_number,
            'nama_penerima' => $request->nama_penerima,
            'alamat_penerima' => $request->alamat_penerima,
            'telepon_penerima' => $request->telepon_penerima,
            'toko_id' => $request->nama_toko,
            'nama_sales' => $request->nama_sales,
            'keterangan' => $request->keterangan_pembayaran,
        );
        if($request->button == 'draft'){
            $barang_keluar['status'] = 2;
        }else{
            $barang_keluar['status'] = 1;
        }
        $create = BarangKeluar::create($barang_keluar);
        $data_barang = array_chunk($request->databarang,4);
        foreach ($data_barang as $key => $barangs) {
            if(count($barangs[3]) == 0){
                $query = GudangBarang::where('barang_id', $barangs[0])->where('toko_id', $request->nama_toko)->limit($barangs[2])->get();
                $dataBarang = array();
                foreach ($query as $key => $value) {
                    DataBarangKeluar::create([
                        'barang_keluar_id' => $create->id,
                        'gudang_barang_id' => $value->id,
                        'barang_id' => $barangs[0]
                    ]);
                    $dataBarang[] = $value->id;
                }
                GudangBarang::whereIn('id', $dataBarang)->delete();
            }else{
                foreach ($barangs[3] as $k => $barang) {
                    DataBarangKeluar::create([
                        'barang_keluar_id' => $create->id,
                        'gudang_barang_id' => $barang,
                        'barang_id' => $barangs[0]
                    ]);    
                    GudangBarang::where('id', $barang)->delete();
                }
            }
        }

        return response()->json($create);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $query = BarangKeluar::with('toko');
        if($request->date_from != null){
            $from = date('Y-m-d', strtotime($request->date_from));
            $to = date('Y-m-d', strtotime($request->date_to));;
            $query->whereBetween('date', [$from, $to]);
        }
        if($request->toko != null){
            $query->whereIn('toko_id', explode(",", $request->toko));
        }
        $query->orderBy('date', 'desc');
        $barang_keluars = $query->get();
        $data = [];
        foreach ($barang_keluars as $key => $barang_keluar) {
            $data[$key] = $barang_keluar;
            $data[$key]['tanggal'] = date('d F Y', strtotime($barang_keluar->date));
            if($barang_keluar->status == 2){
                $data[$key]['status_name'] = 'Draft';
            }else{
                $data[$key]['status_name'] = 'Done';
            }
        }
        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
            $btn = '<div class="button"><a href="/barang-keluar/edit/'.$row->id.'" class="btn btn-icon btn-info btn-edit" data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Edit"><i class="far fa-edit"></i></a> <a class="btn btn-icon btn-success btn-detail" href="/barang-keluar/detail/'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fas fa-th"></i></a> <a class="btn btn-icon btn-dark btn-print" target="_blank" href="/barang-keluar/print/'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Print"><i class="fas fa-print"></i></a> <button data-id="'.$row->id.'" href="#" class="btn btn-icon btn-danger btn-delete" data-toggle="tooltip" data-placement="top" title="Delete"><i class="far fas fa-trash"></i></button> <a class="btn btn-icon btn-warning btn-surat-jalan" target="_blank" href="/barang-keluar/surat-jalan/'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Surat Jalan"><i class="fas fa-file"></i></a></div>';
            return $btn;
        })->rawColumns(['action'])->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Barang Keluar", $menu)){
            $penjualan = BarangKeluar::with(['toko', 'data_barang_keluar.barang', 'barang'])->where('id', $id)->first();
            $barang = array();
            foreach ($penjualan->barang as $key => $value) {
                $barang[] = Barang::where('id',$value->barang_id)->first()->toArray();
            }
            $penjualan['detail_barang'] = $barang;
            return view('barang-keluar.edit', compact('penjualan'));
        }else{
            return abort(403);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $penjualan = array(
            'date' => date('Y-m-d', strtotime($request->date)),
            'kode_barang_keluar' => $request->ref_number,
            'nama_penerima' => $request->nama_penerima,
            'alamat_penerima' => $request->alamat_penerima,
            'telepon_penerima' => $request->telepon_penerima,
            'toko_id' => $request->nama_toko,
            'nama_sales' => $request->nama_sales,
            'keterangan' => $request->keterangan,
        );
        if($request->button == 'draft'){
            $penjualan['status'] = 2;
        }else{
            $penjualan['status'] = 1;
        }
        $update = BarangKeluar::where('id', $id)->update($penjualan);   
        if(count($request->databarangnew) > 0){
            $data_barang_new = array_chunk($request->databarangnew,4);
            foreach ($data_barang_new as $key => $barang_new) {
                if(count($barang_new[3]) == 0){
                    $query = GudangBarang::where('barang_id', $barang_new[0])->limit($barang_new[2])->get();
                    $dataBarang = array();
                    foreach ($query as $key => $value) {
                        DataBarangKeluar::create([
                            'barang_keluar_id' => $id,
                            'barang_id' => $barang_new[0],
                            'gudang_barang_id' => $value->id,
                        ]);
                        $dataBarang[] = $value->id;
                    }
                    GudangBarang::whereIn('id', $dataBarang)->delete();
                }else{
                    foreach ($barang_new[3] as $k => $brg) {
                        DataBarangKeluar::create([
                            'barang_keluar_id' => $id,
                            'barang_id' => $barang_new[0],
                            'gudang_barang_id' => $brg,
                        ]);    
                        GudangBarang::where('id', $brg)->delete();
                    }
                }
            }
        }

        return response()->json($update);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $details = DataBarangKeluar::where('barang_keluar_id',$request->id)->get();
        foreach ($details as $key => $detail) {
            GudangBarang::where('id', $detail->gudang_barang_id)->restore();
        }
        $delete = BarangKeluar::destroy($request->id);
        $detailPenjualan = DataBarangKeluar::where('barang_keluar_id',$request->id)->delete();
        return response()->json($delete);
    }

    public function barang(Request $request, $id)
    {
        $data_barang = array_chunk($request->all(),7);
        foreach ($data_barang as $key => $barangs) {
            if(count($barangs[3]) == 0){
                $create = DataBarangKeluar::create([
                    'barang_keluar_id' => $id,
                    'barang_id' => $barangs[0],
                    'discount' => $barangs[5],
                    'price' => intval(preg_replace('/[^\d.]/', '', $barangs[4])),
                ]);
            }else{
                foreach ($barangs[3] as $k => $barang) {
                    $create[] = DataBarangKeluar::create([
                        'barang_keluar_id' => $id,
                        'barang_id' => $barangs[0],
                        'discount' => $barangs[5],
                        'serial_number_id' => $barang,
                        'price' => intval(preg_replace('/[^\d.]/', '', $barangs[4])),
                    ]);    
                    $update[] =  GudangBarang::where('serial_number_id', $barang)->update([
                        'status' => 2
                    ]);
                }
            }
        }
        return response()->json($data_barang);
    }

    public function print($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Barang Keluar", $menu)){
            $penjualan = BarangKeluar::with(['toko', 'barang', 'detail_penjualan', 'serial_number', 'barang_pembelian'])->where('id', $id)->first();
            return view('barang-keluar.print', compact('penjualan'));
        }else{
            return abort(403);
        }
    }

    public function destroyBarang(Request $request)
    {
        $getBarang = DataBarangKeluar::find($request->id);
        $getPenjualan = BarangKeluar::where('id', (int)$request->id_penjualan)->first();
        $deleteBarang =  DataBarangKeluar::find($request->id); 
        $deleteBarang->forceDelete();
        if($getBarang->discount != ''){
            $subtotal = $getPenjualan->subtotal - (($getBarang->price - ($getBarang->price * $getBarang->discount / 100)));
        }else{
            $subtotal = $getPenjualan->subtotal - $getBarang->price;
        }
        $ppn = 0;
        if($getPenjualan->ppn != 0){
            $ppn = $subtotal * 11 / 100;
        }
        $sisa = 0;
        if($getPenjualan->payment_status == 'DP'){
            $sisa = $subtotal - $getPenjualan->dp_payment;
        }
        $updatePenjualan = BarangKeluar::where('id', (int)$request->id_penjualan)->update([
            'subtotal' => $subtotal,
            'total_pembayaran' => $subtotal,
            'sisa' => $sisa,
        ]);
        $getPenjualanNew = BarangKeluar::where('id', (int)$request->id_penjualan)->first();
        $updateGudangBarang = GudangBarang::withTrashed()->where('id', $getBarang->gudang_barang_id)->restore();
        return response()->json($getPenjualanNew);
    }

    public function get($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Barang Keluar", $menu)){
            $penjualan = BarangKeluar::with(['toko', 'data_barang_keluar.barang', 'barang'])->where('id', $id)->first();
            $barang = array();
            foreach ($penjualan->barang as $key => $value) {
                $barang[] = Barang::where('id',$value->barang_id)->first()->toArray();
            }
            $penjualan['detail_barang'] = $barang;
            return view('barang-keluar.show', compact('penjualan'));
        }else{
            return abort(403);
        }
    }

    public function convert($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Barang Keluar", $menu)){
            $penjualan = BarangKeluar::orderBy('id', 'DESC')->first();
            $quotation = Quotation::with(['toko', 'barang', 'detail_quotation', 'barang_pembelian'])->where('id', $id)->first();
            return view('barang-keluar.convert', compact('penjualan', 'quotation'));
        }else{
            return abort(403);
        }
    }

    public function convert_invoice($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Barang Keluar", $menu)){
            $penjualan = BarangKeluar::orderBy('id', 'DESC')->first();
            $quotation = Invoice::with(['toko', 'detail_invoice.barang'])->where('id', $id)->first();
            return view('barang-keluar.convert-invoice', compact('penjualan', 'quotation'));
        }else{
            return abort(403);
        }
    }

    public function download($id)
    {
        $penjualan = BarangKeluar::with(['toko', 'data_barang_keluar.barang', 'barang_pembelian'])->where('id', $id)->first();
        for ($i=0; $i <sizeof($penjualan->barang_pembelian); $i++) { 
            $data_serial_number[$i] = DataBarangKeluar::with('gudang_barang')->select('gudang_barang_id')->where('barang_id', $penjualan->barang_pembelian[$i]->pivot->barang_id)->where('barang_keluar_id', $penjualan->barang_pembelian[$i]->pivot->barang_keluar_id)->get()->toArray();
            for ($j=0; $j <sizeof($data_serial_number[$i]) ; $j++) { 
                $data_sn[$i][$j] = $data_serial_number[$i][$j]['gudang_barang']['serial_number'];
            }
            $penjualan->barang_pembelian[$i]->pivot['serial_number'] = implode(", ", array_filter($data_sn[$i]));
        }
        $pdf = PDF::loadView('barang-keluar.print', $penjualan);
        return $pdf->stream();
    }

    public function surat_jalan($id)
    {
        $penjualan = BarangKeluar::with(['toko', 'data_barang_keluar.barang', 'barang_pembelian'])->where('id', $id)->first();
        for ($i=0; $i <sizeof($penjualan->barang_pembelian); $i++) { 
            $data_serial_number[$i] = DataBarangKeluar::with('gudang_barang')->select('gudang_barang_id')->where('barang_id', $penjualan->barang_pembelian[$i]->pivot->barang_id)->where('barang_keluar_id', $penjualan->barang_pembelian[$i]->pivot->barang_keluar_id)->get()->toArray();
            for ($j=0; $j <sizeof($data_serial_number[$i]) ; $j++) { 
                $data_sn[$i][$j] = $data_serial_number[$i][$j]['gudang_barang']['serial_number'];
            }
            $penjualan->barang_pembelian[$i]->pivot['serial_number'] = implode(", ", array_filter($data_sn[$i]));
        }
        $pdf = PDF::loadView('barang-keluar.surat-jalan', $penjualan);
        return $pdf->stream();
    }
}
