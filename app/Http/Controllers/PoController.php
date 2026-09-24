<?php

namespace App\Http\Controllers;

use App\Models\Po;
use Illuminate\Http\Request;
use App\Models\GudangBarang;
use App\Models\Toko;
use App\Models\PoDetail;
use App\Models\Barang;
use App\Models\Supplier;
use Yajra\DataTables\Facades\DataTables;
use DB;
use PDF;
use Auth;

class PoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Purchase Order", $menu)){
            $user = Auth::user();
            return view('po.list', compact('user'));
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
        if(auth()->user()->status == 1 || in_array("Purchase Order", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            $po = Po::orderBy('id', 'DESC')->first();
            return view('po.create', compact('po', 'nama_toko'));
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
        $total = $request->sub_total;
        if($request->input_ppn != 0){
            $total = $request->input_total;
        }
        $total_dp = 0;
        if($request->input_dp > 0){
            $total_dp = $request->input_dp;
        }
        $supplier_id = 0;
        if($request->nama_supplier != 0){
            $supplier_id = $request->nama_supplier;
        }
        $supplier = Supplier::find($supplier_id);
        if(!empty($supplier)):
			$nama_supplier = $supplier['nama_supplier'];
			$alamat_supplier = $supplier['alamat'];
			$nama_sales = $supplier['sales'];
		else:
			$nama_supplier = '';
			$alamat_supplier = '';
			$nama_sales = '';
        endif;
        $status_bayar = 0;
        if($request->status_bayar > 0){
            $status_bayar = $request->status_bayar;
        }
         $status_terima = 0;
        if($request->status_terima > 0){
            $status_terima = $request->status_terima;
        }
        $status = 1;
        if($request->status > 0){
            $status = $request->status;
        }
        if($request->jatuh_tempo == null):
			$jatuh_tempo = null;
        else:
			$jatuh_tempo = date('Y-m-d', strtotime($request->jatuh_tempo));
        endif;
        $show_tempo = 0;
        if($request->show_tempo > 0){
            $show_tempo = $request->show_tempo;
        }
        $create = Po::create([
            'date' => date('Y-m-d', strtotime($request->date)),
            'kode_po' => $request->ref_number,
            'supplier_id' => $supplier_id,
            'nama_purchase' => $request->nama_purchase,
            'nama_supplier' => $nama_supplier,
            'alamat_supplier' => $alamat_supplier,
            'telepon' => $request->telepon_supplier,
            'toko_id' => $request->nama_toko,
            'nama_sales' => $nama_sales,
            'subtotal' => intval(preg_replace('/[^\d.]/', '', $total)),
            'po_dp' =>  intval(preg_replace('/[^\d.]/', '', $total_dp)),
            'ppn' => intval(preg_replace('/[^\d.]/', '', $request->input_ppn)),
            'status' => $status,
            'status_terima' => $status_terima,
            'status_bayar' => $status_bayar,
            'show_tempo' => $show_tempo,
			'jatuh_tempo' => $jatuh_tempo,
            'keterangan' => $request->keterangan_pembayaran,
            'alamat_kirim' => $request->alamat_kirim,
            'jenis_barang' => $request->jenis_barang,
        ]);

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
        $query = Po::with('toko');
        if($request->date_from != null){
            $from = date('Y-m-d', strtotime($request->date_from));
            $to = date('Y-m-d', strtotime($request->date_to));;
            $query->whereBetween('date', [$from, $to]);
        }
        if($request->toko != null){
            $query->whereIn('toko_id', explode(",", $request->toko));
        }
        if($request->supplier != null){
            $query->whereIn('supplier_id', explode(",", $request->supplier));
        }
        if(auth()->user()->status != 1){
            $query->where('toko_id', auth()->user()->toko_id);
        }
        
        if($request->status_po != 'semua' AND $request->status_po != null){
            $query->where('status', $request->status_po);
        }
        
        if($request->status_terima != 2 AND $request->status_terima != null){
            $query->where('status_terima', $request->status_terima);
        }
        if($request->status_bayar != 3 AND $request->status_bayar != null){
            if ($request->status_bayar == 0 OR $request->status_bayar == 1) {
                $query->where('status_bayar', $request->status_bayar);
                $query->where('po_dp', '0');
            }else{
                $query->where('po_dp', '>' , 0);
            }
            
        }
        $query->orderBy('date', 'desc');
        $pos = $query->get();
        $data = [];
        foreach ($pos as $key => $po) {
            $data[$key] = $po;
            $data[$key]['tanggal'] = date('d-F-Y', strtotime($po->date));
            if($po->jatuh_tempo != null):
            $data[$key]['jatuh_tempo'] = date('d-F-Y', strtotime($po->jatuh_tempo));
            else:
            $data[$key]['jatuh_tempo'] = '';
            endif;
            $data[$key]['total_pembayaran'] = number_format($po->subtotal);
            if ($po->status == 2) {
                $data[$key]['status_name'] = 'Draft';
            } else if($po->status == 3){
                $data[$key]['status_name'] = 'Canceled';
            }else{
                $data[$key]['status_name'] = 'Dikirim';
            }
            if ($po->status_terima == 1) {
                $data[$key]['status_terima'] = 'Sudah DiTerima';
            } else {
                $data[$key]['status_terima'] = 'Belum DiTerima';
            }
            if ($po->status_bayar == 1) {
                $data[$key]['status_bayar'] = 'Lunas';
            } else {
                if ( $data[$key]['po_dp'] > 0) {
                    $data[$key]['status_bayar'] = 'DP';
                }else{
                    $data[$key]['status_bayar'] = 'Hutang';
                }
            }
            if ($po->ppn != 0) {
                $data[$key]['sub_total'] = number_format($po->subtotal + ($po->subtotal * 11 /100));
            } else {
                $data[$key]['sub_total'] = number_format($po->subtotal);
            }
        }
        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
            $btn = '<div class="button"><a href="'. url('po/edit/' . $row->id ). '" class="btn btn-icon btn-info btn-edit" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Edit"><i class="far fa-edit"></i></a> <a class="btn btn-icon btn-success btn-detail" href="'. url('po/detail/' . $row->id ). '" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fas fa-th"></i></a> <a target="_blank" class="btn btn-icon btn-dark btn-print" href="/po/print/' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Print"><i class="fas fa-print"></i></a> <button data-id="' . $row->id . '" href="#" class="btn btn-icon btn-danger btn-delete" data-toggle="tooltip" data-placement="top" title="Delete"><i class="far fas fa-trash"></i></button> <button data-id="' . $row->id . '" href="#" class="btn btn-icon btn-warning btn-convert" data-toggle="tooltip" data-placement="top" title="Terima Barang"><i class="far fas fa-download"></i></button></div>';
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
		
        $menu = explode(',', auth()->user()->user_menu ?? '');
        if(auth()->user()->status == 1 || in_array("Purchase Order", $menu)){
            $penjualan = Po::with(['toko', 'barang', 'supplier', 'detail_po.barang', 'barang_pembelian'])->where('id', $id)->first();
            return view('po.edit', compact('penjualan'));
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
        $total = $request->sub_total;
        if($request->input_ppn != 0){
            $total = $request->input_total;
        }
        $total_dp = 0;
        if($request->input_dp > 0){
            $total_dp = $request->input_dp;
        }
        $supplier_id = 0;
        if($request->nama_supplier != 0){
            $supplier_id = $request->nama_supplier;
        }
        $supplier = Supplier::find($supplier_id);
        if(!empty($supplier)):
			$nama_supplier = $supplier['nama_supplier'];
			$alamat_supplier = $supplier['alamat'];
			$nama_sales = $supplier['sales'];
		else:
			$nama_supplier = '';
			$alamat_supplier = '';
			$nama_sales = '';
        endif;
         $status_bayar = 0;
        if($request->status_bayar > 0){
            $status_bayar = $request->status_bayar;
        }
         $status_terima = 0;
        if($request->status_terima > 0){
            $status_terima = $request->status_terima;
        }
        $status = 1;
        if($request->status > 0){
            $status = $request->status;
        }
        $show_tempo = 0;
        if($request->jatuh_tempo == null):
			$jatuh_tempo = null;
        else:
			$jatuh_tempo = date('Y-m-d', strtotime($request->jatuh_tempo));
        endif;
        if($request->show_tempo > 0){
            $show_tempo = $request->show_tempo;
        }
        $update = Po::where('id', $id)->update([
            'date' => date('Y-m-d', strtotime($request->date)),
            'kode_po' => $request->ref_number,
            'nama_purchase' => $request->nama_purchase,
            'supplier_id' => $supplier_id,
            'nama_supplier' => $nama_supplier,
            'alamat_supplier' => $alamat_supplier,
            'telepon' => $request->telepon,
            'toko_id' => $request->nama_toko,
            'nama_sales' => $nama_sales,
            'subtotal' => intval(preg_replace('/[^\d.]/', '', $total)),
            'po_dp' =>  intval(preg_replace('/[^\d.]/', '', $total_dp)),
            'ppn' => intval(preg_replace('/[^\d.]/', '', $request->input_ppn)),
            'status' => $status,
            'status_terima' => $status_terima,
            'status_bayar' => $status_bayar,
            'show_tempo' => $show_tempo,
            'jatuh_tempo' => $jatuh_tempo,
            'alamat_kirim' => $request->alamat_kirim,
            'jenis_barang' => $request->jenis_barang,
            'keterangan' => $request->keterangan_pembayaran,

        ]);

        // Delete existing details
        PoDetail::where('po_id', $id)->delete();

        // Re-create details in order
        if (count($request->databarang) > 0) {
            foreach ($request->databarang as $barang) {
                if (count($barang) == 6) {
                    // Existing barang
                    $barang_id = $barang[1];
                    if (!is_numeric($barang_id)) {
                         $dataBarang = Barang::create([
                            'nama_product' => $barang_id
                        ]);
                        $barang_id = $dataBarang->id;
                    }
                    PoDetail::create([
                        'po_id' => $id,
                        'barang_id' => $barang_id,
                        'jumlah' => $barang[2],
                        'price' => intval(preg_replace('/[^\d.]/', '', $barang[3])),
                        'discount' => $barang[4],
                        'subtotal' => intval(preg_replace('/[^\d.]/', '', $barang[5])),
                    ]);
                } else {
                    // New barang
                    $barang_id = $barang[0];
                    if (!is_numeric($barang_id)) {
                         $dataBarang = Barang::create([
                            'nama_product' => $barang_id
                        ]);
                        $barang_id = $dataBarang->id;
                    }
                    PoDetail::create([
                        'po_id' => $id,
                        'barang_id' => $barang_id,
                        'jumlah' => $barang[1],
                        'price' => intval(preg_replace('/[^\d.]/', '', $barang[2])),
                        'discount' => $barang[3],
                        'subtotal' => intval(preg_replace('/[^\d.]/', '', $barang[4])),
                    ]);
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
        Po::destroy($request->id);
        PoDetail::where('po_id', $request->id)->delete();
        return response()->json('success');
    }

    public function barang(Request $request, $id)
    {
        $data_barang = array_chunk($request->all(), 5);
        foreach ($data_barang as $barang) {
            if (is_numeric($barang[0])) {
                $addBarang = PoDetail::create([
                    'po_id' => $id,
                    'barang_id' => $barang[0],
                    'jumlah' => $barang[1],
                    'price' => intval(preg_replace('/[^\d.]/', '', $barang[2])),
                    'discount' => $barang[3],
                    'subtotal' => intval(preg_replace('/[^\d.]/', '', $barang[4])),
                ]);
            } else {
                //insert barang
                $dataBarang = Barang::create([
                    'nama_product' => $barang[0]
                ]);
                $addBarang = PoDetail::create([
                    'po_id' => $id,
                    'barang_id' => $dataBarang->id,
                    'jumlah' => $barang[1],
                    'price' => intval(preg_replace('/[^\d.]/', '', $barang[2])),
                    'discount' => $barang[3],
                    'subtotal' => intval(preg_replace('/[^\d.]/', '', $barang[4])),
                ]);
            }
        }
        return response()->json($addBarang);
    }

    public function print($id)
    {
        $po = Po::with(['toko', 'detail_po.barang', 'barang_pembelian', 'user'])->where('id', $id)->first();
        $pdf = PDF::loadView('po.print', $po);
        return $pdf->stream();
    }

    public function destroyBarang(Request $request)
    {
        $getBarang = PoDetail::find($request->id);
        $getPenjualan = Po::where('id', (int)$request->id_penjualan)->first();
        $subtotal = $getPenjualan->subtotal - $getBarang->subtotal;
        PoDetail::destroy($request->id);
        Po::where('id', (int)$request->id_penjualan)->update([
            'subtotal' => $subtotal,
        ]);
        return response()->json($getPenjualan);
    }

    public function get($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Purchase Order", $menu)){
            $po = Po::with(['toko', 'barang', 'detail_po', 'barang_pembelian'])->where('id', $id)->first();
            return view('po.show', compact('po'));
        }else{
            return abort(403);
        }
    }

    public function terima($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Purchase Order", $menu)){
            $penjualan = Po::with(['toko', 'barang', 'detail_po.barang', 'barang_pembelian'])->where('id', $id)->first();
            return view('po.terima', compact('penjualan'));
        }else{
            return abort(403);
        }
    }

    public function select(Request $request)
    {
        $search = $request->term;
        $data = Po::select("id", "kode_po as text")
            ->where('kode_po', 'LIKE', "%$search%")
            ->get();
        $results = array(
            "results" => $data,
        );
        return response()->json($results);
    }
}
