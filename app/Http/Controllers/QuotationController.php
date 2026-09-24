<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use App\Models\GudangBarang;
use App\Models\Toko;
use App\Models\QuotationDetail;
use App\Models\Barang;
use App\Models\Setting;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use PDF;
use Auth;


class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Quotation", $menu)){
            $user = Auth::user();
            return view('quotation.list', compact('user'));
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
        if(auth()->user()->status == 1 || in_array("Quotation", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            $quotation = Quotation::orderBy('id', 'DESC')->first();
            return view('quotation.create', compact('quotation', 'nama_toko'));
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
       
        $create = Quotation::create([
            'date' => date('Y-m-d', strtotime($request->date)),
            'kode_quotation' => $request->ref_number,
            'nama_pembeli' => $nama_pembeli,
            'alamat_pembeli' => $request->alamat_pembeli,
            'telepon' => $request->telepon_pembeli,
            'toko_id' => $toko_id,
            'nama_sales' => $nama_sales,
            'subtotal' => $total,
            'ppn' => $request->input_ppn,
            'status' => $status,
            'keterangan' => $request->keterangan_pembayaran,
            'show_infopembayaran' => $show_infopembayaran,
            'show_option' => $show_option,
            'option_text' => $request->option_text,
            'show_project' => $show_project,
            'nama_project' => $request->nama_project,
       ]);

        return response()->json($create);
    }
    
	 public function duplicate(Request $request)
	 {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Quotation", $menu)){
            $penjualan = Quotation::with(['toko', 'barang', 'detail_quotation.barang', 'barang_pembelian'])->where('id', $request->id)->first();
            $quotation = Quotation::orderBy('id', 'DESC')->first();
			$ref_num = 'QT - '.sprintf('%07d', $quotation === null ? "1" : $quotation->id+1);
			$create = Quotation::create([
				'date' => date('Y-m-d', strtotime(date('Y-m-d'))),
				'kode_quotation' => $ref_num,
				'nama_pembeli' => $penjualan->nama_pembeli,
				'alamat_pembeli' => $penjualan->alamat_pembeli,
				'telepon' => $penjualan->telepon,
				'toko_id' => $penjualan->toko_id,
				'nama_sales' => $penjualan->nama_sales,
				'subtotal' => $penjualan->subtotal,
				'ppn' => $penjualan->ppn,
				'status' => $penjualan->status,
				'keterangan' => $penjualan->keterangan,
			]);
            if($create->id > 0):
				foreach($penjualan->detail_quotation as $rs):
					$addBarang = QuotationDetail::create([
						'quotation_id' => $create->id ,
						'barang_id' => $rs->barang_id,
						'jumlah' => $rs->jumlah,
						'price' => $rs->price,
						'discount' => $rs->discount,
						'subtotal' => $rs->subtotal,
					]);
				endforeach;
            endif;
            
            return response()->json($create);           
        }else{
            return abort(403);
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
        $query = Quotation::with('toko');
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
        $quotations = $query->get();
        $data = [];
        foreach ($quotations as $key => $quotation) {
			$data[$key] = $quotation;
            $data[$key]['tanggal'] = date('d-F-Y', strtotime($quotation->date));
            $data[$key]['total_pembayaran'] = number_format($quotation->subtotal);
            $data[$key]['sisa'] = number_format($quotation->sisa);
            $data[$key]['dp_payment'] = number_format($quotation->dp_payment);
            if ($quotation->status == 2) {
                $data[$key]['status_name'] = 'Draft';
            } else {
                $data[$key]['status_name'] = 'Done';
            }
            if ($quotation->ppn != 0) {
                $data[$key]['sub_total'] = number_format($quotation->subtotal + ($quotation->subtotal * 11 /100));
            } else {
                $data[$key]['sub_total'] = number_format($quotation->subtotal);
            }
            
        }
        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
            $btn_delete = '';
            if(auth()->user()->status == 1){
                $btn_delete = '<button data-id="' . $row->id . '" href="#" class="btn btn-icon btn-danger btn-delete" data-toggle="tooltip" data-placement="top" title="Delete"><i class="far fas fa-trash"></i></button>';
            }
            $btn = '<div class="button"><a href="quotation/edit/' . $row->id . '" class="btn btn-icon btn-info btn-edit" data-id="' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Edit"><i class="far fa-edit"></i></a> <a class="btn btn-icon btn-success btn-detail" href="/quotation/detail/' . $row->id . '" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fas fa-th"></i></a> <a target="_blank" class="btn btn-icon btn-dark btn-print" href="'.url('/quotation/print/' . $row->id ). '" data-toggle="tooltip" data-placement="top" title="Print"><i class="fas fa-print"></i></a> '.$btn_delete.' <div class="dropdown"> <button class="btn btn-icon btn-warning  dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="far fas fa-sync-alt"></i></button><div class="dropdown-menu" aria-labelledby="dropdownMenuButton"><a class="dropdown-item btn-convert-penjualan" href="#" data-id="' . $row->id . '">Convert to Penjualan</a><a class="dropdown-item btn-convert-invoice" href="#" data-id="' . $row->id . '">Convert to Invoice</a><a class="dropdown-item btn-duplicate-penawaran" href="#" data-id="' . $row->id . '">Duplicate Penawaran</a></div></div></div>';
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
        if(auth()->user()->status == 1 || in_array("Quotation", $menu)){
            $penjualan = Quotation::with(['toko', 'barang', 'detail_quotation.barang', 'barang_pembelian'])->where('id', $id)->first();
            return view('quotation.edit', compact('penjualan'));
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

        DB::beginTransaction();
        try {
            $query = Quotation::where('id', $id);
            if (Auth::user()->status != 1) {
                $query->where('toko_id', Auth::user()->toko_id);
            }
            $existingQuotation = $query->first();

            if (!$existingQuotation) {
                return response()->json(['error' => 'Unauthorized or Not Found'], 403);
            }

            $update = $existingQuotation->update([
                'date' => date('Y-m-d', strtotime($request->date)),
                'kode_quotation' => $request->ref_number,
                'nama_pembeli' => $nama_pembeli,
                'alamat_pembeli' => $request->alamat_pembeli,
                'telepon' => $request->telepon,
                'toko_id' => $toko_id,
                'nama_sales' => $nama_sales,
                'subtotal' => intval(preg_replace('/[^\d.]/', '', $total)),
                'ppn' => intval(preg_replace('/[^\d.]/', '', $request->input_ppn)),
                'status' => $status,
                'keterangan' => $request->keterangan_pembayaran,
                'show_infopembayaran' => $show_infopembayaran,
                'show_option' => $show_option,
                'option_text' => $request->option_text,
                'show_project' => $show_project,
                'nama_project' => $request->nama_project,
            ]);

            // Delete existing details
            QuotationDetail::where('quotation_id', $id)->delete();

            // Re-create details in order
            if (count($request->databarang) > 0) {
                foreach ($request->databarang as $barang) {
                    if (count($barang) == 6) {
                        // Existing barang (id, name, qty, price, discount, subtotal)
                        $barang_id = $barang[1];
                        if (!is_numeric($barang_id)) {
                             $dataBarang = Barang::create([
                                'nama_product' => $barang_id
                            ]);
                            $barang_id = $dataBarang->id;
                        }

                        QuotationDetail::create([
                            'quotation_id' => $id,
                            'barang_id' => $barang_id,
                            'jumlah' => $barang[2],
                            'price' => intval(preg_replace('/[^\d.]/', '', $barang[3])),
                            'discount' => $barang[4],
                            'subtotal' => intval(preg_replace('/[^\d.]/', '', $barang[5])),
                        ]);
                    } else {
                        // New barang (name, qty, price, discount, subtotal)
                        $barang_id = $barang[0];
                        if (!is_numeric($barang_id)) {
                             $dataBarang = Barang::create([
                                'nama_product' => $barang_id
                            ]);
                            $barang_id = $dataBarang->id;
                        }

                        QuotationDetail::create([
                            'quotation_id' => $id,
                            'barang_id' => $barang_id,
                            'jumlah' => $barang[1],
                            'price' => intval(preg_replace('/[^\d.]/', '', $barang[2])),
                            'discount' => $barang[3],
                            'subtotal' => intval(preg_replace('/[^\d.]/', '', $barang[4])),
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => $update]);
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
        $query = Quotation::where('id', $request->id);
        if (Auth::user()->status != 1) {
            $query->where('toko_id', Auth::user()->toko_id);
        }
        $quotation = $query->first();

        if (!$quotation) {
            return response()->json(['error' => 'Unauthorized or Not Found'], 403);
        }

        $delete = $quotation->delete();
        QuotationDetail::where('quotation_id', $request->id)->delete();
        return response()->json('success');
    }

    public function barang(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data_barang = array_chunk($request->all(), 5);
            foreach ($data_barang as $barang) {
                if (is_numeric($barang[0])) {
                    $addBarang = QuotationDetail::create([
                        'quotation_id' => $id,
                        'barang_id' => $barang[0],
                        'jumlah' => $barang[1],
                        'price' => $barang[2],
                        'discount' => $barang[3],
                        'subtotal' => $barang[4],
                    ]);
                } else {
                    //insert barang
                    $dataBarang = Barang::create([
                        'nama_product' => $barang[0]
                    ]);
                    $addBarang = QuotationDetail::create([
                        'quotation_id' => $id,
                        'barang_id' => $dataBarang->id,
                        'jumlah' => $barang[1],
                        'price' => $barang[2],
                        'discount' => $barang[3],
                        'subtotal' => $barang[4],
                    ]);
                }
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => $addBarang]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function print($id)
    {
        $quotation = Quotation::with(['toko', 'detail_quotation.barang', 'barang_pembelian', 'user'])->where('id', $id)->first();
       //print_r($quotation['toko']['image']);exit;
        $setting = Setting::where('toko_id', $quotation->toko_id)->first();
        if(!empty($setting)):
			$quotation['cara_pembayaran'] = $setting->cara_pembayaran;
		else:
			$quotation['cara_pembayaran'] = '';
        endif;
       
        $pdf = PDF::loadView('quotation.print', $quotation);
        return $pdf->stream();
    }

    public function destroyBarang(Request $request)
    {
        $getBarang = QuotationDetail::find($request->id);
        $getPenjualan = Quotation::where('id', (int)$request->id_penjualan)->first();
        $subtotal = $getPenjualan->subtotal - $getBarang->subtotal;
        QuotationDetail::destroy($request->id);
        Quotation::where('id', (int)$request->id_penjualan)->update([
            'subtotal' => $subtotal,
        ]);
        return response()->json($getPenjualan);
    }

    public function get($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Quotation", $menu)){
            $penjualan = Quotation::with(['toko', 'barang', 'detail_quotation', 'barang_pembelian'])->where('id', $id)->first();
            return view('quotation.show', compact('penjualan'));
        }else{
            return abort(403);
        }
    }
}
