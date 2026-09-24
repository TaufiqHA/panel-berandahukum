<?php

namespace App\Http\Controllers;

use App\Models\InvoiceDetail;
use App\Models\GudangBarang;
use App\Models\Toko;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\SerialNumber;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Setting;
use Yajra\DataTables\Facades\DataTables;
use DB;
use PDF;
use Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Invoice", $menu)){
            $user = Auth::user();
            return view('invoice.list', compact('user'));
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
        if(auth()->user()->status == 1 || in_array("Invoice", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            $invoice = Invoice::orderBy('id', 'DESC')->first();
            return view('invoice.create', compact('invoice', 'nama_toko'));
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
        $request->validate([
            'date' => 'required|date',
            'ref_number' => 'required|string|max:255',
            'nama_pembeli' => 'nullable|string|max:255',
            'alamat_pembeli' => 'nullable|string',
            'telepon_pembeli' => 'nullable|string|max:50',
            'metode_pembayaran' => 'required|string|max:100',
            'nama_toko' => 'required|exists:tokos,id',
            'nama_sales' => 'nullable|string|max:255',
            'status_pembayaran' => 'required|string|max:50',
            'tempo_waktu' => 'nullable|string|max:100',
            'status' => 'nullable|integer',
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

        $create = Invoice::create([
            'date' => date('Y-m-d', strtotime($request->date)),
            'kode_invoice' => $request->ref_number,
            'nama_pembeli' => $nama_pembeli,
            'alamat_pembeli' => $request->alamat_pembeli,
            'tlp_pembeli' => $request->telepon_pembeli,
            'metode_pembayaran' => $request->metode_pembayaran,
            'toko_id' => $toko_id,
            'nama_sales' => $nama_sales,
            'payment_status' => $request->status_pembayaran,
            'waktu' => $request->tempo_waktu,
            'status' => $status,
            'subtotal' => intval(preg_replace('/[^\d.]/', '', $total)),
            'dp_payment' => intval(preg_replace('/[^\d.]/', '', $request->dp_payment)),
            'ppn' => intval(preg_replace('/[^\d.]/', '', $request->input_ppn)),
            'sisa' => intval(preg_replace('/[^\d.]/', '', $request->sisa)),
            'keterangan' => $request->keterangan_pembayaran,
            'show_infopembayaran' => $show_infopembayaran,
            'show_option' => $show_option,
            'option_text' => $request->option_text,
            'show_project' => $show_project,
            'nama_project' => $request->nama_project,
        ]);
        $data_barang = array_chunk($request->databarang,6);
        if(!empty($data_barang)):
        foreach ($data_barang as $barangs) {
            InvoiceDetail::create([
                'invoice_id' => $create->id,
                'barang_id' => $barangs[0],
                'jumlah' => $barangs[2],
                'discount' => $barangs[4],
                'price' => intval(preg_replace('/[^\d.]/', '', $barangs[3])),
                'subtotal' => intval(preg_replace('/[^\d.]/', '', $barangs[5])),
            ]);
        }
        endif;


        return response()->json($create);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $query = Invoice::with('toko');
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
            $btn = '<div class="button"><a href="/invoice/edit/'.$row->id.'" class="btn btn-icon btn-info btn-edit" data-id="'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Edit"><i class="far fa-edit"></i></a> <a class="btn btn-icon btn-success btn-detail" href="/invoice/detail/'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fas fa-th"></i></a> <a class="btn btn-icon btn-dark btn-print" target="_blank" href="/invoice/print/'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Print"><i class="fas fa-print"></i></a> '.$btn_delete.' <button data-id="'.$row->id.'" href="#" class="btn btn-icon btn-warning btn-convert" data-toggle="tooltip" data-placement="top" title="Convert To Penjualan"><i class="far fas fa-sync"></i></button></div>';
            return $btn;
        })->rawColumns(['action'])->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invoice  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu = explode(',', auth()->user()->user_menu ?? '');
        if(auth()->user()->status == 1 || in_array("Invoice", $menu)){
            $invoice = Invoice::with(['toko', 'detail_invoice.barang'])->where('id', $id)->first();
            return view('invoice.edit', compact('invoice'));
        }else{
            return abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $penjualan
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
            'kode_invoice' => $request->ref_number,
            'nama_pembeli' => $nama_pembeli,
            'alamat_pembeli' => $request->alamat_pembeli,
            'tlp_pembeli' => $request->telepon,
            'metode_pembayaran' => $request->metode_pembayaran,
            'toko_id' => $toko_id,
            'nama_sales' => $nama_sales,
            'status' => $status,
            'payment_status' => $request->status_pembayaran,
            'ppn' => intval(preg_replace('/[^\d.]/', '', $request->input_ppn)),
            'waktu' => $request->tempo_waktu,
            'subtotal' => intval(preg_replace('/[^\d.]/', '', $total)),
            'total_pembayaran' => intval(preg_replace('/[^\d.]/', '', $total)),
            'dp_payment' => intval(preg_replace('/[^\d.]/', '', $request->dp_payment)),
            'sisa' => intval(preg_replace('/[^\d.]/', '', $request->sisa)),
            'keterangan' => $request->keterangan_pembayaran,
            'show_infopembayaran' => $show_infopembayaran,
            'show_option' => $show_option,
            'option_text' => $request->option_text,
            'show_project' => $show_project,
            'nama_project' => $request->nama_project,
        );
        $query = Invoice::where('id', $id);
        if (auth()->user()->status != 1) {
            $query->where('toko_id', auth()->user()->toko_id);
        }
        $invoice = $query->first();

        if (!$invoice) {
            return response()->json(['error' => 'Unauthorized or Not Found'], 403);
        }

        $update = $invoice->update($penjualan);   

        // 1. Hapus semua detail invoice lama terlebih dahulu
        InvoiceDetail::where('invoice_id', $id)->delete();

        // 2. Simpan kembali semua barang dengan urutan baru dari form
        if(count($request->databarang) > 0){
            foreach ($request->databarang as $barang) {
                if (count($barang) == 7) {
                    // Barang lama (ID, barang_id, stock, jumlah, harga, discount, subtotal)
                    InvoiceDetail::create([
                        'invoice_id' => $id,
                        'barang_id'  => $barang[1],
                        'jumlah'     => $barang[3],
                        'price'      => intval(preg_replace('/[^\d.]/', '', $barang[4])),
                        'discount'   => $barang[5],
                        'subtotal'   => intval(preg_replace('/[^\d.]/', '', $barang[6])),
                    ]);
                } else {
                    // Barang baru (barang_id, stock, jumlah, harga, discount, subtotal)
                    InvoiceDetail::create([
                        'invoice_id' => $id,
                        'barang_id'  => $barang[0],
                        'jumlah'     => $barang[2],
                        'price'      => intval(preg_replace('/[^\d.]/', '', $barang[3])),
                        'discount'   => $barang[4],
                        'subtotal'   => intval(preg_replace('/[^\d.]/', '', $barang[5])),
                    ]);
                }
            }
        }

        return response()->json($update);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $penjualan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $delete = Invoice::destroy($request->id);
        $InvoiceDetail = InvoiceDetail::where('invoice_id',$request->id)->delete();
        return response()->json($delete);
    }

    public function barang(Request $request, $id)
    {
        $data_barang = array_chunk($request->all(),6);
        foreach ($data_barang as $key => $barangs) {
            $create = InvoiceDetail::create([
                'invoice_id' => $id,
                'barang_id' => $barangs[0],
                'jumlah' => $barang[2],
                'discount' => $barangs[4],
                'price' => intval(preg_replace('/[^\d.]/', '', $barangs[3])),
                'subtotal' => intval(preg_replace('/[^\d.]/', '', $barangs[5])),
            ]);
        }
        return response()->json($data_barang);
    }

    public function print($id)
    {
        $invoice = Invoice::with(['toko', 'detail_invoice.barang'])->where('id', $id)->first();
        $setting = Setting::where('toko_id', $invoice->toko_id)->first();
        if(!empty($setting)):
			$invoice['cara_pembayaran'] = $setting->cara_pembayaran;
		else:
			$invoice['cara_pembayaran'] = '';
        endif;        
        $pdf = PDF::loadView('invoice.print', $invoice);
        return $pdf->stream();
    }

    public function destroyBarang(Request $request)
    {
        $invoice = Invoice::where('id', (int)$request->id_invoice)->first();
        $invoice_detail = InvoiceDetail::where('id', (int)$request->id)->first();
        $subtotal = $invoice->subtotal - $invoice_detail->subtotal;
        if($invoice->ppn != 0){
            $ppn = $subtotal * 11 / 100;
            $total_pembayaran = $subtotal + $ppn;
        }else{
            $ppn = 0;
            $total_pembayaran = $subtotal;
        }
        $sisa = $total_pembayaran - $invoice->dp_payment;
        Invoice::where('id',  (int)$request->id_invoice)->update([
            'subtotal' => $subtotal,
            'ppn' => $ppn,
            'sisa' => $sisa,            
            'total_pembayaran' => $total_pembayaran,
        ]);
        InvoiceDetail::where('id', (int)$request->id)->delete();

        return response()->json($invoice);
    }

    public function get($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Invoice", $menu)){
            $invoice = Invoice::with(['toko', 'detail_invoice.barang'])->where('id', $id)->first();
            $barang = array();
            return view('invoice.show', compact('invoice'));
        }else{
            return abort(403);
        }
    }

    public function convert($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Invoice", $menu)){
            $invoice = Invoice::orderBy('id', 'DESC')->first();
            $quotation = Quotation::with(['toko', 'barang', 'detail_quotation', 'barang_pembelian'])->where('id', $id)->first();
            return view('invoice.convert', compact('invoice', 'quotation'));
        }else{
            return abort(403);
        }
    }

    public function download($id)
    {
        $penjualan = Invoice::with(['toko', 'detail_invoice.barang', 'barang_pembelian'])->where('id', $id)->first();
        for ($i=0; $i <sizeof($penjualan->barang_pembelian); $i++) { 
            $data_serial_number[$i] = InvoiceDetail::with('gudang_barang')->select('gudang_barang_id')->where('barang_id', $penjualan->barang_pembelian[$i]->pivot->barang_id)->where('invoice_id', $penjualan->barang_pembelian[$i]->pivot->invoice_id)->get()->toArray();
            for ($j=0; $j <sizeof($data_serial_number[$i]) ; $j++) { 
                $data_sn[$i][$j] = $data_serial_number[$i][$j]['gudang_barang']['serial_number'];
            }
            $penjualan->barang_pembelian[$i]->pivot['serial_number'] = implode(", ", array_filter($data_sn[$i]));
        }
        
        $pdf = PDF::loadView('invoice.print', $penjualan);
        return $pdf->stream();
    }
}
