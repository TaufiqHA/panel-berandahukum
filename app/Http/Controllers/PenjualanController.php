<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\GudangBarang;
use App\Models\Toko;
use App\Models\Penjualan;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\SerialNumber;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Barang;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use PDF;
use Auth;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        
        if(auth()->user()->status == 1 || in_array("Penjualan", $menu)){
            $user = Auth::user();
            return view('penjualan.list', compact('user'));
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
        if(auth()->user()->status == 1 || in_array("Penjualan", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            $penjualan = Penjualan::orderBy('id', 'DESC')->first();
            return view('penjualan.create', compact('penjualan', 'nama_toko'));
        }else{
            return abort(404);
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
        $request->validate([
            'date' => 'required|date',
            'ref_number' => 'required|string|max:255',
            'nama_pembeli' => 'nullable|string|max:255',
            'alamat_pembeli' => 'nullable|string',
            'telepon_pembeli' => 'nullable|string|max:50',
            'metode_pembayaran' => 'required|string|max:100',
            'nama_toko' => 'required|exists:tokos,id',
            'nama_sales' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'status_pembayaran' => 'required|string|max:50',
            'tempo_waktu' => 'nullable|string|max:100',
            'sub_total' => 'required',
            'dp_payment' => 'nullable',
            'input_ppn' => 'nullable',
            'sisa' => 'nullable',
            'keterangan_pembayaran' => 'nullable|string',
            'databarang' => 'required|array',
        ]);

        $total = $request->sub_total;
        if($request->input_ppn != 0){
            $total = $request->input_total;
        }
        $show_infopembayaran = 0;
        if($request->show_infopembayaran > 0){
            $show_infopembayaran = $request->show_infopembayaran;
        }
        $show_option = 0;
        if($request->show_option > 0){
            $show_option = $request->show_option;
        }
        $show_project = 0;
        if($request->show_project > 0){
            $show_project = $request->show_project;
        }
        $status = 1;
        if($request->status > 0){
            $status = $request->status;
        }
        if(!empty($request->nama_pembeli)){
            $nama_pembeli = $request->nama_pembeli;
        }else{
			$nama_pembeli = '-draft-';
		}
		if(!empty($request->nama_sales)){
            $nama_sales = $request->nama_sales;
        }else{
			$nama_sales = '-';
		}
        $toko_id = $request->nama_toko;
        if (Auth::user()->status != 1) {
            $toko_id = Auth::user()->toko_id;
        }
        $penjualan = array(
            'user_id' => Auth::user()->id ?? Auth::id(),
            'date' => date('Y-m-d', strtotime($request->date)),
            'kode_penjualan' => $request->ref_number,
            'nama_pembeli' => $nama_pembeli,
            'alamat_pembeli' => $request->alamat_pembeli,
            'telepon_pembeli' => $request->telepon_pembeli,
            'metode_pembayaran' => $request->metode_pembayaran,
            'toko_id' => $toko_id,
            'nama_sales' => $nama_sales,
            'status' => $status,
            'payment_status' => $request->status_pembayaran,
            'waktu' => $request->tempo_waktu,
            'subtotal' => $total,
            'dp_payment' => $request->dp_payment,
            'ppn' => $request->input_ppn,
            'sisa' => $request->sisa,
            'keterangan' => $request->keterangan_pembayaran,
            'show_infopembayaran' => $show_infopembayaran,
            'show_option' => $show_option,
            'option_text' => $request->option_text,
            'show_project' => $show_project,
            'nama_project' => $request->nama_project,
        );
        DB::beginTransaction();
        try {
            $create = Penjualan::create($penjualan);
            if(!empty($request->databarang)):
            $data_barang = array_chunk($request->databarang,7);
            foreach ($data_barang as $key => $barangs) {
                if(count($barangs[3]) == 0){
                    $query = GudangBarang::where('barang_id', $barangs[0])->where('toko_id', $toko_id)->limit($barangs[2])->get();
                    $dataBarang = array();
                    foreach ($query as $key => $value) {
                        DetailPenjualan::create([
                            'penjualan_id' => $create->id,
                            'gudang_barang_id' => $value->id,
                            'serial_number_id' => is_numeric($value->serial_number_id) ? $value->serial_number_id : null,
                            'barang_id' => $barangs[0],
                            'discount' => $barangs[5],
                            'price' => $barangs[4],
                        ]);
                        $dataBarang[] = $value->id;
                    }
                    GudangBarang::whereIn('id', $dataBarang)->delete();
                }else{
                    foreach ($barangs[3] as $k => $barang) {
                        $gb = GudangBarang::where('id', $barang)->orWhere('serial_number_id', $barang)->first();
                        $gb_id = $gb ? $gb->id : (is_numeric($barang) ? $barang : null);
                        $sn_id = ($gb && is_numeric($gb->serial_number_id)) ? $gb->serial_number_id : (is_numeric($barang) ? $barang : null);

                        DetailPenjualan::create([
                            'penjualan_id' => $create->id,
                            'gudang_barang_id' => $gb_id,
                            'serial_number_id' => $sn_id,
                            'barang_id' => $barangs[0],
                            'discount' => $barangs[5],
                            'price' => $barangs[4],
                        ]);    
                        if ($gb_id) {
                            GudangBarang::where('id', $gb_id)->delete();
                        } elseif ($barang) {
                            GudangBarang::where('serial_number_id', $barang)->delete();
                        }
                    }
                }
            }
            endif;
            DB::commit();
            return response()->json(['status' => 'success', 'id' => $create->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
		 $query = Penjualan::with('toko');
        if($request->date_from != null){
            $from = date('Y-m-d', strtotime($request->date_from));
            $to = date('Y-m-d', strtotime($request->date_to));;
            $query->whereBetween('date', [$from, $to]);
        }
        if($request->toko != null){
            $query->whereIn('toko_id', explode(",", $request->toko));
        }
        if(auth()->user()->status != 1){
            $query->where('toko_id', auth()->user()->toko_id);
        }
        $query->orderBy('date', 'desc');
        $penjualans = $query->get();
        $data = [];
        foreach ($penjualans as $key => $penjualan) {
            $data[$key] = $penjualan;
            $data[$key]['tanggal'] = date('d F Y', strtotime($penjualan->date));
            $data[$key]['total_pembayaran'] = number_format($penjualan->subtotal);
            if($penjualan->ppn != '0'){
                $data[$key]['total_pembayaran'] = number_format($penjualan->subtotal + ($penjualan->subtotal * 11 /100));
            }
            $data[$key]['sisa'] = number_format($penjualan->sisa);
            $data[$key]['dp_payment'] = number_format($penjualan->dp_payment);
            if (strlen($penjualan->keterangan) > 100){
                $data[$key]['keterangan'] = substr($penjualan->keterangan, 0, 100) . '...';
            }else{
                $data[$key]['keterangan'] = $penjualan->keterangan;
            }
            if($penjualan->status == 2){
                $data[$key]['status_name'] = 'Draft';
            }else{
                $data[$key]['status_name'] = 'Done';
            }
        }
        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
            $btn_delete = '';
            if(auth()->user()->status == 1){
                $btn_delete = '<button data-id="'.$row->id.'" href="#" class="btn btn-icon btn-danger btn-delete" data-toggle="tooltip" data-placement="top" title="Delete"><i class="far fas fa-trash"></i></button>';
            }
            $btn = '<div class="button"><a href="/penjualan/edit/'.$row->id.'" class="btn btn-icon btn-info btn-edit" data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Edit"><i class="far fa-edit"></i></a> <a class="btn btn-icon btn-success btn-detail" href="/penjualan/detail/'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fas fa-th"></i></a> <a class="btn btn-icon btn-dark btn-print" target="_blank" href="/penjualan/print/'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Print"><i class="fas fa-print"></i></a> '.$btn_delete.' <a class="btn btn-icon btn-warning btn-print" target="_blank" href="/penjualan/surat-jalan/'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Surat Jalan"><i class="fas fa-file"></i></a></div>';
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
        if(auth()->user()->status == 1 || in_array("Penjualan", $menu)){
            $penjualan = Penjualan::with(['toko', 'detail_penjualan.barang', 'detail_penjualan.serial_number', 'detail_penjualan.gudang_barang'])->where('id', $id)->first();
            return view('penjualan.edit', compact('penjualan'));
        }else{
            return abort(404);
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
        $request->validate([
            'date' => 'required|date',
            'ref_number' => 'required|string|max:255',
            'nama_pembeli' => 'nullable|string|max:255',
            'alamat_pembeli' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
            'metode_pembayaran' => 'required|string|max:100',
            'nama_toko' => 'required|exists:tokos,id',
            'nama_sales' => 'nullable|string|max:255',
            'status' => 'nullable|integer',
            'status_pembayaran' => 'required|string|max:50',
            'tempo_waktu' => 'nullable|string|max:100',
            'sub_total' => 'required',
            'dp_payment' => 'nullable',
            'input_ppn' => 'nullable',
            'sisa' => 'nullable',
            'keterangan_pembayaran' => 'nullable|string',
            'databarang' => 'required|array',
        ]);

        $total = $request->sub_total;
        if($request->input_ppn != 0){
            $total = $request->input_total;
        }
        
        $show_infopembayaran = 0;
        if($request->show_infopembayaran > 0){
            $show_infopembayaran = $request->show_infopembayaran;
        }
        $show_option = 0;
        if($request->show_option > 0){
            $show_option = $request->show_option;
        }
        $show_project = 0;
        if($request->show_project > 0){
            $show_project = $request->show_project;
        }
        $status = 1;
        if($request->status > 0){
            $status = $request->status;
        }
        if(!empty($request->nama_pembeli)){
            $nama_pembeli = $request->nama_pembeli;
        }else{
			$nama_pembeli = '-draft-';
		}
		if(!empty($request->nama_sales)){
            $nama_sales = $request->nama_sales;
        }else{
			$nama_sales = '-';
		}
        $toko_id = $request->nama_toko;
        if (Auth::user()->status != 1) {
            $toko_id = Auth::user()->toko_id;
        }
        $penjualan = array(
            'date' => date('Y-m-d', strtotime($request->date)),
            'kode_penjualan' => $request->ref_number,
            'nama_pembeli' => $nama_pembeli,
            'alamat_pembeli' => $request->alamat_pembeli,
            'telepon' => $request->telepon,
            'metode_pembayaran' => $request->metode_pembayaran,
            'toko_id' => $toko_id,
            'nama_sales' => $nama_sales,
            'status' => $status,
            'payment_status' => $request->status_pembayaran,
            'ppn' => intval(preg_replace('/[^\d.]/', '', $request->input_ppn)),
            'waktu' => $request->tempo_waktu,
            'subtotal' => intval(preg_replace('/[^\d.]/', '', $total)),
            'dp_payment' => intval(preg_replace('/[^\d.]/', '', $request->dp_payment)),
            'sisa' => intval(preg_replace('/[^\d.]/', '', $request->sisa)),
            'keterangan' => $request->keterangan_pembayaran,
            'show_infopembayaran' => $show_infopembayaran,
            'show_option' => $show_option,
            'option_text' => $request->option_text,
            'show_project' => $show_project,
            'nama_project' => $request->nama_project,
        );
        DB::beginTransaction();
        try {
            $query = Penjualan::where('id', $id);
            if (Auth::user()->status != 1) {
                $query->where('toko_id', Auth::user()->toko_id);
            }
            $existingPenjualan = $query->first();

            if (!$existingPenjualan) {
                return response()->json(['error' => 'Unauthorized or Not Found'], 403);
            }

            $update = $existingPenjualan->update($penjualan);   

            // Restore GudangBarang for all current details
            $details = DetailPenjualan::where('penjualan_id', $id)->get();
            foreach ($details as $detail) {
                if ($detail->gudang_barang_id) {
                    GudangBarang::where('id', $detail->gudang_barang_id)->update(['status' => 1]);
                }
                if ($detail->serial_number_id) {
                    GudangBarang::where('id', $detail->serial_number_id)->orWhere('serial_number_id', $detail->serial_number_id)->update(['status' => 1]);
                }
            }
            // Delete current details
            DetailPenjualan::where('penjualan_id', $id)->delete();

            // Re-create details in order from $request->databarang
            if (count($request->databarang) > 0) {
                foreach ($request->databarang as $barang_row) {
                    // Existing row has 8 items, new has 7.
                    if (count($barang_row) == 8) {
                        $barang_id = $barang_row[1];
                        $jumlah = $barang_row[3];
                        $serial_numbers = $barang_row[4];
                        $price = intval(preg_replace('/[^\d.]/', '', $barang_row[5]));
                        $discount = $barang_row[6];
                    } else {
                        $barang_id = $barang_row[0];
                        $jumlah = $barang_row[2];
                        $serial_numbers = $barang_row[3];
                        $price = intval(preg_replace('/[^\d.]/', '', $barang_row[4]));
                        $discount = $barang_row[5];
                    }

                    // Handle serial numbers
                    $sn_array = is_array($serial_numbers) ? array_filter($serial_numbers, function($val) {
                        return $val !== null && $val !== '';
                    }) : (!empty($serial_numbers) ? [$serial_numbers] : []);

                    if (empty($sn_array)) {
                        // Not serialized or auto-pick
                        $query = GudangBarang::where('barang_id', $barang_id)->where('toko_id', $toko_id)->where('status', 1)->limit($jumlah)->get();
                        $dataBarang = array();
                        foreach ($query as $value) {
                            DetailPenjualan::create([
                                'penjualan_id' => $id,
                                'barang_id' => $barang_id,
                                'gudang_barang_id' => $value->id,
                                'discount' => $discount,
                                'serial_number_id' => is_numeric($value->serial_number_id) ? $value->serial_number_id : null,
                                'price' => $price,
                            ]);
                            $dataBarang[] = $value->id;
                        }
                        if (count($dataBarang) > 0) {
                            GudangBarang::whereIn('id', $dataBarang)->update(['status' => 2]);
                        }
                    } else {
                        // Specified serial numbers
                        foreach ($sn_array as $brg) {
                            $gb = GudangBarang::where('id', $brg)->orWhere('serial_number_id', $brg)->first();
                            $gb_id = $gb ? $gb->id : (is_numeric($brg) ? $brg : null);
                            $sn_id = ($gb && is_numeric($gb->serial_number_id)) ? $gb->serial_number_id : (is_numeric($brg) ? $brg : null);

                            DetailPenjualan::create([
                                'penjualan_id' => $id,
                                'barang_id' => $barang_id,
                                'gudang_barang_id' => $gb_id,
                                'discount' => $discount,
                                'serial_number_id' => $sn_id,
                                'price' => $price,
                            ]);    
                            if ($gb_id) {
                                GudangBarang::where('id', $gb_id)->update(['status' => 2]);
                            } elseif ($brg) {
                                GudangBarang::where('serial_number_id', $brg)->update(['status' => 2]);
                            }
                        }
                    }
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'id' => $id]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Penjualan  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $query = Penjualan::where('id', $request->id);
        if (Auth::user()->status != 1) {
            $query->where('toko_id', Auth::user()->toko_id);
        }
        $penjualan = $query->first();

        if (!$penjualan) {
            return response()->json(['error' => 'Unauthorized or Not Found'], 403);
        }

        $details = DetailPenjualan::where('penjualan_id',$request->id)->get();
        foreach ($details as $key => $detail) {
            GudangBarang::where('id', $detail->gudang_barang_id)->restore();
        }
        $delete = $penjualan->delete();
        $detailPenjualan = DetailPenjualan::where('penjualan_id',$request->id)->delete();
        return response()->json($delete);
    }

    public function barang(Request $request, $id)
    {
        $data_barang = array_chunk($request->all(),7);
        foreach ($data_barang as $key => $barangs) {
            if(count($barangs[3]) == 0){
                $create = DetailPenjualan::create([
                    'penjualan_id' => $id,
                    'barang_id' => $barangs[0],
                    'discount' => $barangs[5],
                    'price' => intval(preg_replace('/[^\d.]/', '', $barangs[4])),
                ]);
            }else{
                foreach ($barangs[3] as $k => $barang) {
                    $create[] = DetailPenjualan::create([
                        'penjualan_id' => $id,
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
        if(auth()->user()->status == 1 || in_array("Penjualan", $menu)){
            $penjualan = Penjualan::with(['toko', 'barang', 'detail_penjualan', 'serial_number', 'barang_pembelian'])->where('id', $id)->first();
            return view('penjualan.print', compact('penjualan'));
        }else{
            return abort(404);
        }
    }

    public function destroyBarang(Request $request)
    {
        $getBarang = DetailPenjualan::find($request->id);
        $getPenjualan = Penjualan::where('id', (int)$request->id_penjualan)->first();
        $deleteBarang =  DetailPenjualan::find($request->id); 
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
        $updatePenjualan = Penjualan::where('id', (int)$request->id_penjualan)->update([
            'subtotal' => $subtotal,
            'total_pembayaran' => $subtotal,
            'sisa' => $sisa,
        ]);
        $getPenjualanNew = Penjualan::where('id', (int)$request->id_penjualan)->first();
        $updateGudangBarang = GudangBarang::withTrashed()->where('id', $getBarang->gudang_barang_id)->restore();
        return response()->json($getPenjualanNew);
    }

    public function get($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Penjualan", $menu)){
            $penjualan = Penjualan::with(['toko', 'detail_penjualan.barang', 'barang'])->where('id', $id)->first();
            $barang = array();
            foreach ($penjualan->barang as $key => $value) {
                $barang[] = Barang::where('id',$value->barang_id)->first()->toArray();
            }
            $penjualan['detail_barang'] = $barang;
            return view('penjualan.show', compact('penjualan'));
        }else{
            return abort(404);
        }
    }

    public function convert($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Penjualan", $menu)){
            $penjualan = Penjualan::orderBy('id', 'DESC')->first();
            $quotation = Quotation::with(['toko', 'barang', 'detail_quotation', 'barang_pembelian'])->where('id', $id)->first();
            return view('penjualan.convert', compact('penjualan', 'quotation'));
        }else{
            return abort(404);
        }
    }

    public function convert_invoice($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Penjualan", $menu)){
            $penjualan = Penjualan::orderBy('id', 'DESC')->first();
            $quotation = Invoice::with(['toko', 'detail_invoice.barang'])->where('id', $id)->first();
            return view('penjualan.convert-invoice', compact('penjualan', 'quotation'));
        }else{
            return abort(404);
        }
    }

    private function resolveSerialNumber($detail)
    {
        $serial_number = $this->serialNumberValue($detail->serial_number_id);
        if (!empty($serial_number)) {
            return $serial_number;
        }

        $gudang_barang = $detail->gudang_barang;
        if ($gudang_barang) {
            $serial_number = $this->serialNumberValue($gudang_barang->serial_number_id);
            if (!empty($serial_number)) {
                return $serial_number;
            }

            $serial_number = optional($gudang_barang->serial_number)->serial_number;
            if (!empty($serial_number)) {
                return $serial_number;
            }
        }

        return null;
    }

    private function serialNumberValue($value)
    {
        if (empty($value)) {
            return null;
        }

        $serial_number = SerialNumber::withTrashed()->find($value);
        if ($serial_number && !empty($serial_number->serial_number)) {
            return $serial_number->serial_number;
        }

        // Legacy data stores the serial value directly instead of a foreign key.
        return is_scalar($value) ? (string) $value : null;
    }

    private function attachSerialNumbers($penjualan)
    {
        foreach ($penjualan->barang_pembelian as $barang) {
            $detail_penjualan = DetailPenjualan::with(['gudang_barang.serial_number', 'serial_number'])
                ->where('barang_id', $barang->pivot->barang_id)
                ->where('penjualan_id', $barang->pivot->penjualan_id)
                ->get();

            $data_sn = [];
            foreach ($detail_penjualan as $detail) {
                $serial_number = $this->resolveSerialNumber($detail);
                if (!empty($serial_number)) {
                    $data_sn[] = $serial_number;
                }
            }

            $barang->pivot['serial_number'] = implode(", ", $data_sn);
        }

        return $penjualan;
    }

    public function download($id)
    {
        $penjualan = Penjualan::with(['toko', 'detail_penjualan.barang', 'barang_pembelian'])->where('id', $id)->first();
        $this->attachSerialNumbers($penjualan);
        $setting = Setting::where('toko_id', $penjualan->toko_id)->first();
        if(!empty($setting)):
			$penjualan['cara_pembayaran']= $setting->cara_pembayaran;
		else:
			$penjualan['cara_pembayaran']= '';
        endif;
        
        $pdf = PDF::loadView('penjualan.print', $penjualan);
        return $pdf->stream();
    }

    public function surat_jalan($id)
    {
        $penjualan = Penjualan::with(['toko', 'detail_penjualan.barang', 'barang_pembelian'])->where('id', $id)->first();
        $this->attachSerialNumbers($penjualan);
        $pdf = PDF::loadView('penjualan.surat-jalan', $penjualan);
        return $pdf->stream();
    }
}
