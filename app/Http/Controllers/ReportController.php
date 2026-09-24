<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Toko;
use App\Models\PindahGudang;
use App\Models\GudangBarang;
use App\Models\StockIn;
use App\Models\Barang;
use App\Models\DetailBarangKeluar;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Po;

class ReportController extends Controller
{
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Report", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            if (auth()->user()->status_admin != 1) {
                return view('report.report', compact('nama_toko'));
            }else{
                return abort(403);
            }
          
        }else{
            return abort(403);
        }
    }

    public function get_barang_masuk(Request $request)
    {
		$barang = $request->nama_barang;
        $dateFrom = $request->tanggalAwal;
        $dateTo = $request->tanggalAkhir;
        $toko = $request->nama_toko;
        $nama_toko = '';
        if (Auth::user()->status == 2){
            $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
            $nama_toko = $get['nama_toko'];
        }
        $from = date('Y-m-d', strtotime($request->tanggalAwal));
        $to = date('Y-m-d', strtotime($request->tanggalAkhir));
        $data = [];
        $query = StockIn::with(['barang', 'toko'])->whereBetween('tanggal_masuk', [$from, $to]);
        if($request->nama_toko){
            $query->whereIn('toko_id', $request->nama_toko);
        }
        if($request->nama_barang){
            $query->whereIn('barang_id', $request->nama_barang);
        }
        if(Auth::user()->status == 2){
            $query->whereIn('toko_id', [Auth::user()->toko_id]);   
        }
        $stocks = $query->get()->toArray();
        foreach ($stocks as $key => $stock) {
        	if($stock['barang'] != ''){
	            $data[$key]['id'] = $key+1;
	            $data[$key]['nama_product'] = $stock['barang']['nama_product'];
	            $data[$key]['jumlah'] = $stock['jumlah'];
	            $data[$key]['tanggal_masuk'] = date('d F Y', strtotime($stock['tanggal_masuk']));
	            $data[$key]['harga_beli'] = number_format($stock['harga_beli']);
	            $data[$key]['harga_jual'] = number_format($stock['harga_jual']);
	            $data[$key]['price_list'] = number_format($stock['price_list']);
	            $data[$key]['made_in'] = $stock['made_in'];
	            $data[$key]['supplier'] = $stock['supplier'];
	            $data[$key]['toko'] = $stock['toko']['nama_toko'];
	            $data[$key]['keterangan'] = $stock['keterangan'];
        	}
        }
        $datas = array_values($data);
        return response()->json($datas);
    }

    public function export_barang_masuk(Request $request)
    {
        $from = date('Y-m-d', strtotime($request->tanggalAwal));
        $to = date('Y-m-d', strtotime($request->tanggalAkhir));
        $column = array(
            array(
                    "No",
                    "Nama Barang",
                    "Jumlah",
                    "Tanggal Masuk",
                    "Harga Beli",
                    "Harga Jual",
                    "Price List",
                    "Made In",
                    "Supplier",
                    "Toko",
                    "Keterangan"
                )
        );
        $data = [];
        $query = StockIn::with(['barang', 'toko'])->whereBetween('tanggal_masuk', [$from, $to]);
        if($request->nama_toko){
            $query->whereIn('toko_id', $request->nama_toko);
        }

        if(Auth::user()->status == 2){
            $query->whereIn('toko_id', [Auth::user()->toko_id]);   
        }
        $stocks = $query->get()->toArray();
        foreach ($stocks as $key => $stock) {
            if($stock['barang'] != ''){
                $data[$key]['id'] = $key+1;
                $data[$key]['nama_product'] = $stock['barang']['nama_product'];
                $data[$key]['Jumlah'] = $stock['jumlah'];
                $data[$key]['tanggal_masuk'] = date('d F Y', strtotime($stock['tanggal_masuk']));
                $data[$key]['harga_beli'] = number_format($stock['harga_beli']);
                $data[$key]['harga_jual'] = number_format($stock['harga_jual']);
                $data[$key]['price_list'] = number_format($stock['price_list']);
                $data[$key]['made_in'] = $stock['made_in'];
                $data[$key]['supplier'] = $stock['supplier'];
                $data[$key]['toko'] = $stock['toko']['nama_toko'];
                $data[$key]['keterangan'] = $stock['keterangan'];
            }
        }
        $data_array = array_merge($column, $data);
        $export = new ReportExport($data_array);
        return Excel::download($export, 'Report Barang Masuk' . '.xlsx');
    }

    public function pindah_barang()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Report", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            return view('report.pindah_barang', compact('nama_toko'));
        }else{
            return abort(403);
        }
    }

    public function get_pindah_barang(Request $request)
    {
        $from = date('Y-m-d', strtotime($request->tanggalAwal));
        $to = date('Y-m-d', strtotime($request->tanggalAkhir));
        $toko_from = $request->toko_from;
        $toko_to = $request->toko_to;

        if(Auth::user()->status == 2){
            $toko_from = Auth::user()->toko_id;   
        }

        $data = [];
        $getdata = DetailbarangKeluar::with(['pindah_gudang.toko', 'pindah_gudang.toko_to', 'barang', 'barang.stock_in', 'gudang_barang','gudang_barang.detail_barang_masuk.stock_in', 'pindah_gudang.barang_pindah'])->whereHas('pindah_gudang', function (Builder $query) use ($from, $to, $toko_from, $toko_to) {
                $query->whereBetween('date',[$from, $to]);
                if($toko_from != null){
                    $query->where('from', $toko_from);
                }
                if($toko_to != null){
                    $query->where('to', $toko_to);
                }
            })->get()->toArray();
        $getdata = array_values($getdata);
        foreach ($getdata as $key => $value) {
            $data[$key]['id'] = $key+1;
            $data[$key]['date'] = date('d F Y', strtotime($value['pindah_gudang']['date']));
            $data[$key]['no_ref'] = $value['pindah_gudang']['no_ref'];
            $data[$key]['from'] = $value['pindah_gudang']['toko']['nama_toko'];
            $data[$key]['to'] = $value['pindah_gudang']['toko_to']['nama_toko'];
            if($value['barang'] != ''){
                $data[$key]['nama_product'] = $value['barang']['nama_product'];
            }else{
                $data[$key]['nama_product'] = '';
            }

            if($value['gudang_barang'] != ''){
                $data[$key]['serial_number'] = $value['gudang_barang']['serial_number'];
            }else{
                $data[$key]['serial_number'] = '';
            }
            $data[$key]['keterangan'] = $value['keterangan'];
            if($value['status'] == 1){
                $data[$key]['status'] = 'Dikirim';    
            }else{
                $data[$key]['status'] = 'Diterima';    
            }

            if($value['gudang_barang'] != '' && $value['gudang_barang']['detail_barang_masuk'] != '' && $value['gudang_barang']['detail_barang_masuk']['stock_in'] != ''){
                $data[$key]['harga_beli'] = $value['gudang_barang']['detail_barang_masuk']['stock_in']['harga_beli'];
            }else{
                $data[$key]['harga_beli'] = '';
            }
        }
        return response()->json($data);
    }

    public function export_pindah_barang(Request $request)
    {
        $from = date('Y-m-d', strtotime($request->tanggalAwal));
        $to = date('Y-m-d', strtotime($request->tanggalAkhir));
        $toko_from = $request->toko_from;
        $toko_to = $request->toko_to;

        if(Auth::user()->status == 2){
            $toko_from = Auth::user()->toko_id;   
        }
        $column = array(
                        array(
                                "No",
                                "Tanggal",
                                "Ref Number",
                                "Toko Awal",
                                "Toko Tujuan",
                                "Nama Barang",
                                "Serial Number",
                                "Keterangan",
                                "Status",
                                "Harga Beli",
                            )
                    );
            $data = [];
            $getdata = DetailbarangKeluar::with(['pindah_gudang.toko', 'pindah_gudang.toko_to', 'barang', 'barang.stock_in', 'gudang_barang','gudang_barang.detail_barang_masuk.stock_in', 'pindah_gudang.barang_pindah'])->whereHas('pindah_gudang', function (Builder $query) use ($from, $to, $toko_from, $toko_to) {
                    $query->whereBetween('date',[$from, $to]);
                    if($toko_from != null){
                        $query->where('from', $toko_from);
                    }
                    if($toko_to != null){
                        $query->where('to', $toko_to);
                    }
                })->get()->toArray();
            $getdata = array_values($getdata);
            foreach ($getdata as $key => $value) {
                $data[$key]['id'] = $key+1;
                $data[$key]['date'] = date('d F Y', strtotime($value['pindah_gudang']['date']));
                $data[$key]['no_ref'] = $value['pindah_gudang']['no_ref'];
                $data[$key]['from'] = $value['pindah_gudang']['toko']['nama_toko'];
                $data[$key]['to'] = $value['pindah_gudang']['toko_to']['nama_toko'];
                if($value['barang'] != ''){
                    $data[$key]['nama_product'] = $value['barang']['nama_product'];
                }else{
                    $data[$key]['nama_product'] = '';
                }

                if($value['gudang_barang'] != ''){
                    $data[$key]['serial_number'] = $value['gudang_barang']['serial_number'];
                }else{
                    $data[$key]['serial_number'] = '';
                }
                $data[$key]['keterangan'] = $value['keterangan'];
                if($value['status'] == 1){
                    $data[$key]['status'] = 'Dikirim';    
                }else{
                    $data[$key]['status'] = 'Diterima';    
                }

                if($value['gudang_barang'] != '' && $value['gudang_barang']['detail_barang_masuk'] != '' && $value['gudang_barang']['detail_barang_masuk']['stock_in'] != ''){
                    $data[$key]['harga_beli'] = $value['gudang_barang']['detail_barang_masuk']['stock_in']['harga_beli'];
                }else{
                    $data[$key]['harga_beli'] = '';
                }
            }
            $data_array = array_merge($column, $data);
            $export = new ReportExport($data_array);
            return Excel::download($export, 'Report Pindah Barang' . '.xlsx');
    }

    public function barang_keluar()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Report", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            return view('report.barang_keluar', compact('nama_toko'));
        }else{
            return abort(403);
        }
    }

    public function get_barang_keluar(Request $request)
    {
		$cek_barang = false;
		$barang = $request->nama_barang;
        $from = date('Y-m-d', strtotime($request->tanggalAwal));
        $to = date('Y-m-d', strtotime($request->tanggalAkhir));
        $nama_toko = $request->nama_toko;
        //DB::enableQueryLog();
        $query = Penjualan::with('toko','detail_penjualan.gudang_barang.barang')->whereBetween('date', [$from, $to]);
        if($nama_toko){
            $query->whereIn('toko_id',$nama_toko);
        }
        if(!empty($barang)){
			$cek_barang = true;
			$query->whereIn('id',function ($query2) use ($barang) {
					 $query2->select('penjualan_id')
                     ->from('detail_penjualans')
                     ->whereIn('barang_id',$barang)
                     ->groupBy('penjualan_id');
				});
	    }
	    
        $penjualan = $query->get()->toArray();
        //print_r(DB::getQueryLog());
        
        //exit;
        $datas = [];
        for ($i=0; $i <sizeof($penjualan); $i++) { 
            for ($j=0; $j <sizeof($penjualan[$i]['detail_penjualan']); $j++) { 
				//if($cek_barang AND in_array( $penjualan[$i]['detail_penjualan'][$j]['barang_id'],$barang)):
                $penjualan[$i]['detail_penjualan'][$j]['no_ref'] = $penjualan[$i]['kode_penjualan'];
                $penjualan[$i]['detail_penjualan'][$j]['tanggal'] = $penjualan[$i]['date'];
                $penjualan[$i]['detail_penjualan'][$j]['nama_pembeli'] = $penjualan[$i]['nama_pembeli'];
                $penjualan[$i]['detail_penjualan'][$j]['alamat_pembeli'] = $penjualan[$i]['alamat_pembeli'];
                $penjualan[$i]['detail_penjualan'][$j]['telepon_pembeli'] = $penjualan[$i]['telepon'];
                $penjualan[$i]['detail_penjualan'][$j]['sales'] = $penjualan[$i]['nama_sales'];
                if($penjualan[$i]['toko'] != ''){
                    $penjualan[$i]['detail_penjualan'][$j]['toko'] = $penjualan[$i]['toko']['nama_toko'];
                }else{
                    $penjualan[$i]['detail_penjualan'][$j]['toko'] = '';
                }
                $datas[] =$penjualan[$i]['detail_penjualan'][$j];
                //endif;
            }
        }
        $dataExport = [];
       
        foreach ($datas as $key => $data) {
			$dataExport[$key]['tanggal'] = date('d F Y', strtotime($data['tanggal']));
            $dataExport[$key]['no_ref'] = $data['no_ref'];
            $dataExport[$key]['nama_product'] = $data['gudang_barang']['barang']['nama_product'];
            $dataExport[$key]['serial_number'] = $data['gudang_barang']['serial_number'];
            if($data['toko'] != ''){
                $dataExport[$key]['nama_toko'] = $data['toko'];
            }else{
                $dataExport[$key]['nama_toko'] = '';
            }
            $dataExport[$key]['nama_pembeli'] = $data['nama_pembeli'];
            $dataExport[$key]['alamat_pembeli'] = $data['alamat_pembeli'];
            $dataExport[$key]['telepon_pembeli'] = $data['telepon_pembeli'];
            $dataExport[$key]['harga_terjual'] = $data['price']-($data['price'] * $data['discount'] / 100);
            $dataExport[$key]['sales'] = $data['sales'];
        
        }
       
        return response()->json($dataExport);
    }

    public function export_barang_keluar(Request $request)
    {
		$cek_barang = false;
		$barang = $request->nama_barang;
        $from = date('Y-m-d', strtotime($request->tanggalAwal));
        $to = date('Y-m-d', strtotime($request->tanggalAkhir));
        $nama_toko = $request->nama_toko;
        $column = array(
            array(
                    "No",
                    "No Ref",
                    "Nama Barang",
                    "Serial Number",
                    "Nama Toko",
                    "Tanggal Keluar",
                    "Nama Pembeli",
                    "Alamat Pembeli",
                    "No Telepon",
                    "Harga Terjual",
                    "Sales",
                )
        );
        $datas = [];
        $query = Penjualan::with('toko','detail_penjualan.gudang_barang.barang')->whereBetween('date', [$from, $to]);
        if($nama_toko){
            $query->whereIn('toko_id',$nama_toko);
        }
        if(!empty($barang)){
			$cek_barang = true;
			$query->whereIn('id',function ($query2) use ($barang) {
					 $query2->select('penjualan_id')
                     ->from('detail_penjualans')
                     ->whereIn('barang_id',$barang)
                     ->groupBy('penjualan_id');
				});
	    }
        $penjualan = $query->get()->toArray();
        for ($i=0; $i <sizeof($penjualan); $i++) { 
            for ($j=0; $j <sizeof($penjualan[$i]['detail_penjualan']); $j++) { 
				if($cek_barang AND in_array( $penjualan[$i]['detail_penjualan'][$j]['barang_id'],$barang)):
                $penjualan[$i]['detail_penjualan'][$j]['no_ref'] = $penjualan[$i]['kode_penjualan'];
                $penjualan[$i]['detail_penjualan'][$j]['tanggal'] = $penjualan[$i]['date'];
                $penjualan[$i]['detail_penjualan'][$j]['nama_pembeli'] = $penjualan[$i]['nama_pembeli'];
                $penjualan[$i]['detail_penjualan'][$j]['alamat_pembeli'] = $penjualan[$i]['alamat_pembeli'];
                $penjualan[$i]['detail_penjualan'][$j]['telepon_pembeli'] = $penjualan[$i]['telepon'];
                $penjualan[$i]['detail_penjualan'][$j]['sales'] = $penjualan[$i]['nama_sales'];
                if($penjualan[$i]['toko'] != ''){
                    $penjualan[$i]['detail_penjualan'][$j]['toko'] = $penjualan[$i]['toko']['nama_toko'];
                }else{
                    $penjualan[$i]['detail_penjualan'][$j]['toko'] = '';
                }
                $datas[] =$penjualan[$i]['detail_penjualan'][$j];
                endif;
            }
        }
        $dataExport = [];
        foreach ($datas as $key => $data) {
            $dataExport[$key]['no'] = $key +1;
            $dataExport[$key]['no_ref'] = $data['no_ref'];
            $dataExport[$key]['nama_product'] = $data['gudang_barang']['barang']['nama_product'];
            $dataExport[$key]['serial_number'] = $data['gudang_barang']['serial_number'];
            $dataExport[$key]['nama_toko'] = $data['toko'];
            $dataExport[$key]['tanggal'] = date('d F Y', strtotime($data['tanggal']));
            $dataExport[$key]['nama_pembeli'] = $data['nama_pembeli'];
            $dataExport[$key]['alamat_pembeli'] = $data['alamat_pembeli'];
            $dataExport[$key]['telepon_pembeli'] = $data['telepon_pembeli'];
            $dataExport[$key]['harga_terjual'] = $data['price']-($data['price'] * $data['discount'] / 100);
            $dataExport[$key]['sales'] = $data['sales'];
        }
        $data_array = array_merge($column, $dataExport);
        $export = new ReportExport($data_array);
        return Excel::download($export, 'Report Penjualan' . '.xlsx');
    }

    public function barang_stock()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Report", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            return view('report.barang_stock',compact('nama_toko'));
        }else{
            return abort(403);
        }
    }

    public function get_stock(Request $request)
    {   
        DB::beginTransaction();
        try {

            $nama_toko = $request->nama_toko;
            $nama_barang = $request->nama_barang;
            $data = [];
            $query = GudangBarang::with('barang', 'detail_barang_masuk.stock_in.po', 'toko');
            if($nama_toko){
                $query->whereIn('toko_id', $nama_toko);
            }
            if($nama_barang){
                $query->whereIn('barang_id', $nama_barang);
            }
            $barangs = $query->get()->toArray();
            $dataBarang = array_values($barangs);
            foreach ($dataBarang as $key => $barang) {
                if($barang['detail_barang_masuk'] != '' && $barang['detail_barang_masuk']['stock_in'] && $barang['detail_barang_masuk']['stock_in']['po'] != '' ){
                    $data[$key]['no_po'] = $barang['detail_barang_masuk']['stock_in']['po']['kode_po'];
                    $data[$key]['tanggal_po'] = date('d F Y', strtotime($barang['detail_barang_masuk']['stock_in']['po']['date']));
                }else{
                    $data[$key]['tanggal_po'] = '';
                    $data[$key]['no_po'] = '';
                }
                if($barang['detail_barang_masuk'] != '' && $barang['detail_barang_masuk']['stock_in']){
                    $data[$key]['tanggal_masuk'] = date('d F Y', strtotime($barang['detail_barang_masuk']['stock_in']['tanggal_masuk']));
                }else{
                    $data[$key]['tanggal_masuk'] = '';
                }
                if($barang['barang'] != ''){
                    $data[$key]['barang'] = $barang['barang']['nama_product'];
                }else{
                    $data[$key]['barang'] = '';
                }
                if($barang['barang'] != ''){
                    $data[$key]['warna'] = $barang['barang']['warna'];
                }else{
                    $data[$key]['warna'] = '';
                }
                if($barang['toko'] != ''){
                    $data[$key]['toko'] = $barang['toko']['nama_toko'];
                }else{
                    $data[$key]['toko'] = '';
                }
                $data[$key]['serial_number'] = $barang['serial_number'];
                $data[$key]['jumlah'] = 1;
                if($barang['detail_barang_masuk'] != '' && $barang['detail_barang_masuk']['stock_in']){
                    $data[$key]['harga_beli'] = $barang['detail_barang_masuk']['stock_in']['harga_beli'];
                }else{
                    $data[$key]['harga_beli'] = '';
                }
                if($barang['detail_barang_masuk'] != '' && $barang['detail_barang_masuk']['stock_in']){
                    $data[$key]['harga_jual'] = $barang['detail_barang_masuk']['stock_in']['harga_jual'];
                }else{
                    $data[$key]['harga_jual'] = '';
                }
                if($barang['detail_barang_masuk'] != '' && $barang['detail_barang_masuk']['stock_in']){
                    $data[$key]['supplier'] = $barang['detail_barang_masuk']['stock_in']['supplier'];
                }else{
                    $data[$key]['supplier'] = '';
                }
            }
            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'data' => $e->getMessage()]);
        }


       
    }

    public function export_stock(Request $request)
    {
        $nama_toko = $request->nama_toko;
        $nama_barang = $request->nama_barang;
        $column = array(
            array(
                    "No",
                    "Tanggal Masuk",
                    "Nama Barang",
                    "Serial Number",
                    "Jumlah",
                    "Warna",
                    "Nama Toko",
                    "Harga Beli",
                    "Harga Jual",
                    "Supplier",

                )
        );
        $data = [];
        $query = GudangBarang::with('barang', 'detail_barang_masuk.stock_in', 'toko');
        if (Auth::user()->status == 2){
            $query->whereIn('toko_id', [Auth::user()->toko_id]);
        }else{
            if($nama_toko){
                $query->whereIn('toko_id', $nama_toko);
            }
        }
        if($nama_barang){
            $query->whereIn('barang_id', $nama_barang);
        }

        $barangs = $query->get()->toArray();
        $dataBarang = array_values($barangs);
        foreach ($dataBarang as $key => $barang) {
                $data[$key]['no'] = $barang['id'];
                if($barang['detail_barang_masuk'] != '' && $barang['detail_barang_masuk']['stock_in']){
                    $data[$key]['tanggal_masuk'] = date('d F Y', strtotime($barang['detail_barang_masuk']['stock_in']['tanggal_masuk']));
                }else{
                    $data[$key]['tanggal_masuk'] = '';
                }
                if($barang['barang'] != ''){
                    $data[$key]['barang'] = $barang['barang']['nama_product'];
                }else{
                    $data[$key]['barang'] = '';
                }
                $data[$key]['serial_number'] = $barang['serial_number'];
                $data[$key]['jumlah'] = 1;
                if($barang['barang'] != ''){
                    $data[$key]['warna'] = $barang['barang']['warna'];
                }else{
                    $data[$key]['warna'] = '';
                }
                if($barang['toko'] != ''){
                    $data[$key]['toko'] = $barang['toko']['nama_toko'];
                }else{
                    $data[$key]['toko'] = '';
                }
                if($barang['detail_barang_masuk'] != '' && $barang['detail_barang_masuk']['stock_in']){
                    $data[$key]['harga_beli'] = $barang['detail_barang_masuk']['stock_in']['harga_beli'];
                }else{
                    $data[$key]['harga_beli'] = '';
                }
                if($barang['detail_barang_masuk'] != '' && $barang['detail_barang_masuk']['stock_in']){
                    $data[$key]['harga_jual'] = $barang['detail_barang_masuk']['stock_in']['harga_jual'];
                }else{
                    $data[$key]['harga_jual'] = '';
                }
                if($barang['detail_barang_masuk'] != '' && $barang['detail_barang_masuk']['stock_in']){
                    $data[$key]['supplier'] = $barang['detail_barang_masuk']['stock_in']['supplier'];
                }else{
                    $data[$key]['supplier'] = '';
                }
        }
        $data_array = array_merge($column, $data);
        $export = new ReportExport($data_array);
        return Excel::download($export, 'Report Stock' . '.xlsx');
    }

    public function laba_rugi()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Report", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            return view('report.laba_rugi', compact('nama_toko'));
        }else{
            return abort(403);
        }
    }

    public function get_laba_rugi(Request $request)
    {
        $from = date('Y-m-d', strtotime($request->tanggalAwal));
        $to = date('Y-m-d', strtotime($request->tanggalAkhir));
        $nama_toko = $request->nama_toko;
        $nama_barang = $request->nama_barang;
        $jenis_report = $request->jenis_report;
        $data = [];
        $query = Penjualan::with(['toko','detail_penjualan.gudang_barang.detail_barang_masuk.stock_in','detail_penjualan.barang'])->whereBetween('date', [$from, $to]);
        if($nama_toko){
            $query->whereIn('toko_id',$nama_toko);
        }
        
        if($nama_barang){
			
			$query->whereIn('id',function ($query2) use ($nama_barang) {
					 $query2->select('penjualan_id')
                     ->from('detail_penjualans')
                     ->whereIn('barang_id',$nama_barang)
                     ->groupBy('penjualan_id');
				});
	    }
        $barangs = $query->get()->toArray();
        $datas = [];
        for ($i=0; $i <sizeof($barangs); $i++) { 
			if($jenis_report == '2'):
			//berdasarkan penjualan
				$barangs[$i]['no'] = 1;
				$total_harga_beli = 0;
				for ($j=0; $j <sizeof($barangs[$i]['detail_penjualan']); $j++) { 
					$harga_beli = 0 ;
					if($barangs[$i]['detail_penjualan'][$j]['gudang_barang']['detail_barang_masuk'] != ''){
						$harga_beli = $barangs[$i]['detail_penjualan'][$j]['gudang_barang']['detail_barang_masuk']['stock_in']['harga_beli'];
					}else{
						$harga_beli = 0;
					}
					$total_harga_beli +=$harga_beli;
				}
				$barangs[$i]['toko'] = $barangs[$i]['toko']['nama_toko'];
				$barangs[$i]['harga_beli'] = $total_harga_beli;
				if($barangs[$i]['ppn'] != '0'){
					$barangs[$i]['total_pembayaran'] = $barangs[$i]['subtotal'] + ($barangs[$i]['subtotal'] * 11 /100);
				}else{
					$barangs[$i]['total_pembayaran'] = $barangs[$i]['subtotal'] ;
				}
				$barangs[$i]['dp_payment'] = doubleval($barangs[$i]['dp_payment']);
				$barangs[$i]['sisa'] = doubleval($barangs[$i]['sisa']);
				$barangs[$i]['total_pembayaran'] = doubleval($barangs[$i]['total_pembayaran']) ;
				$barangs[$i]['keuntungan'] = $barangs[$i]['total_pembayaran']- $total_harga_beli ;
				if($barangs[$i]['nama_project'] == null):
				$barangs[$i]['nama_project'] = '';
				endif;
				$datas[] = $barangs[$i];
			else:
			//berdasarkan barang
            for ($j=0; $j <sizeof($barangs[$i]['detail_penjualan']); $j++) { 
				$barangs[$i]['detail_penjualan'][$j]['tanggal'] = $barangs[$i]['date'];
                $barangs[$i]['detail_penjualan'][$j]['no_ref'] = $barangs[$i]['kode_penjualan'];
                if($barangs[$i]['toko'] != ''){
                    $barangs[$i]['detail_penjualan'][$j]['toko'] = $barangs[$i]['toko']['nama_toko'];
                }else{
                    $barangs[$i]['detail_penjualan'][$j]['toko'] = '';
                }
				if($nama_barang){
					if(in_array($barangs[$i]['detail_penjualan'][$j]['barang_id'],$nama_barang)){
						$datas[] = $barangs[$i]['detail_penjualan'][$j];
					}
				}else{
					$datas[] =$barangs[$i]['detail_penjualan'][$j];
				}
            }
            endif;
        }
       
        $dataExport = [];
        if($jenis_report == '2'):
			//berdasarkan penjualan
			foreach ($datas as $key => $data) {
				$dataExport[$key]['tanggal'] = date('d F Y', strtotime($data['date']));
				$dataExport[$key]['kode_penjualan'] = $data['kode_penjualan'];
				$dataExport[$key]['nama_pembeli'] = $data['nama_pembeli'];
				$dataExport[$key]['metode_pembayaran'] = $data['metode_pembayaran'];
				$dataExport[$key]['nama_toko'] = $data['toko'];
				$dataExport[$key]['payment_status'] = $data['payment_status'];
				$dataExport[$key]['total_pembayaran'] =$data['total_pembayaran'];
				$dataExport[$key]['harga_beli'] =$data['harga_beli'];
				$dataExport[$key]['keuntungan'] =$data['keuntungan'];
				$dataExport[$key]['dp_payment'] =$data['dp_payment'];
				$dataExport[$key]['sisa'] =$data['sisa'];
				if($data['status'] == 2){
					$dataExport[$key]['status_name'] = 'Draft';
				}else{
					$dataExport[$key]['status_name'] = 'Done';
				}
				$dataExport[$key]['nama_project'] = $data['nama_project'];
				
			}
        else:
			//berdasarkan barang
			foreach ($datas as $key => $data) {
				$dataExport[$key]['tanggal'] = date('d F Y', strtotime($data['tanggal']));
				$dataExport[$key]['no_ref'] = $data['no_ref'];
				$dataExport[$key]['nama_product'] = $data['barang']['nama_product'];
				$dataExport[$key]['serial_number'] = (isset($data['gudang_barang']['serial_number'])) ? $data['gudang_barang']['serial_number'] : '';
				$dataExport[$key]['nama_toko'] = $data['toko'];
				if(isset($data['gudang_barang']['detail_barang_masuk'])){
					$dataExport[$key]['harga_beli'] = $data['gudang_barang']['detail_barang_masuk']['stock_in']['harga_beli'];
				}else{
					$dataExport[$key]['harga_beli'] = '';
				}
				$dataExport[$key]['harga_jual'] = $data['price']-($data['price'] * $data['discount'] / 100);
				if($dataExport[$key]['harga_beli'] != ''){
					$dataExport[$key]['keuntungan'] = $dataExport[$key]['harga_jual'] - $dataExport[$key]['harga_beli'];
				}else{
					$dataExport[$key]['keuntungan'] = $dataExport[$key]['harga_jual'] - 0;
				}
			}
        endif;
        return response()->json(array_values($dataExport));
    }

    public function export_laba_rugi(Request $request)
    {
        $from = date('Y-m-d', strtotime($request->tanggalAwal));
        $to = date('Y-m-d', strtotime($request->tanggalAkhir));
        $nama_toko = $request->nama_toko;
        $nama_barang = $request->nama_barang;
        $jenis_report = $request->jenis_report;
        $data = [];
        $query = Penjualan::with(['toko','detail_penjualan.gudang_barang.detail_barang_masuk.stock_in','detail_penjualan.barang'])->whereBetween('date', [$from, $to]);
        if($nama_toko){
            $query->whereIn('toko_id',$nama_toko);
        }
        
        if($nama_barang){
			
			$query->whereIn('id',function ($query2) use ($nama_barang) {
					 $query2->select('penjualan_id')
                     ->from('detail_penjualans')
                     ->whereIn('barang_id',$nama_barang)
                     ->groupBy('penjualan_id');
				});
	    }
        $barangs = $query->get()->toArray();
        if($jenis_report == '2'):
        	$column = array(
				array(
						"No",
						"Tanggal",
						"Kode Penjualan",
						"Nama Pembeli",
						"Metode Pembayaran",
						"Nama Toko",
						"Cara Pembayaran",
						"Total Pembayaran",
						"DP",
						"Sisa",
						"Total Hrg.Beli",
						"Keuntungan",
						"Status",
						"Nama Project"
					)
			);

        else:
			$column = array(
				array(
						"No",
						"Tanggal",
						"No Ref",
						"Nama Barang",
						"Serial Number",
						"Nama Toko",
						"Harga Beli",
						"Harga Jual",
						"Keuntungan"
					)
			);
        endif;
        $data = [];
        
        
        $datas = [];
        for ($i=0; $i <sizeof($barangs); $i++) { 
			if($jenis_report == '2'):
			//berdasarkan penjualan
				$total_harga_beli = 0;
				for ($j=0; $j <sizeof($barangs[$i]['detail_penjualan']); $j++) { 
					$harga_beli = 0 ;
					if($barangs[$i]['detail_penjualan'][$j]['gudang_barang']['detail_barang_masuk'] != ''){
						$harga_beli = $barangs[$i]['detail_penjualan'][$j]['gudang_barang']['detail_barang_masuk']['stock_in']['harga_beli'];
					}
					$total_harga_beli +=$harga_beli;
				}
				$barangs[$i]['toko'] = $barangs[$i]['toko']['nama_toko'];
				$barangs[$i]['harga_beli'] = $total_harga_beli;
				if($barangs[$i]['ppn'] != '0'){
					$barangs[$i]['total_pembayaran'] = $barangs[$i]['subtotal'] + ($barangs[$i]['subtotal'] * 11 /100);
				}else{
					$barangs[$i]['total_pembayaran'] = $barangs[$i]['subtotal'] ;
				}
				$barangs[$i]['dp_payment'] = doubleval($barangs[$i]['dp_payment']);
				$barangs[$i]['sisa'] = doubleval($barangs[$i]['sisa']);
				$barangs[$i]['total_pembayaran'] = doubleval($barangs[$i]['total_pembayaran']) ;
				$barangs[$i]['keuntungan'] = $barangs[$i]['total_pembayaran']- $total_harga_beli ;
				if($barangs[$i]['nama_project'] == null):
				$barangs[$i]['nama_project'] = '';
				endif;
				$datas[] = $barangs[$i];
			else:
			//berdasarkan barang
            for ($j=0; $j <sizeof($barangs[$i]['detail_penjualan']); $j++) { 
				$barangs[$i]['detail_penjualan'][$j]['tanggal'] = $barangs[$i]['date'];
                $barangs[$i]['detail_penjualan'][$j]['no_ref'] = $barangs[$i]['kode_penjualan'];
                if($barangs[$i]['toko'] != ''){
                    $barangs[$i]['detail_penjualan'][$j]['toko'] = $barangs[$i]['toko']['nama_toko'];
                }else{
                    $barangs[$i]['detail_penjualan'][$j]['toko'] = '';
                }
				if($nama_barang){
					if(in_array($barangs[$i]['detail_penjualan'][$j]['barang_id'],$nama_barang)){
						$datas[] = $barangs[$i]['detail_penjualan'][$j];
					}
				}else{
					$datas[] =$barangs[$i]['detail_penjualan'][$j];
				}
            }
            endif;
        }
       
        $dataExport = [];
        $no = 1;
        $sub_judul = '';
        $total_hrg_beli = 0;
        $total_hrg_jual = 0;
        $total_dp = 0;
        $total_sisa = 0;
        
        $total_pembayaran = 0;
        $total_keuntungan = 0 ;
        if($jenis_report == '2'):
			//berdasarkan penjualan
			$sub_judul = 'Berdasarkan Penjualan';
			foreach ($datas as $key => $data) {
				$dataExport[$key]['no'] = $no;
				$dataExport[$key]['tanggal'] = date('d F Y', strtotime($data['date']));
				$dataExport[$key]['kode_penjualan'] = $data['kode_penjualan'];
				$dataExport[$key]['nama_pembeli'] = $data['nama_pembeli'];
				$dataExport[$key]['metode_pembayaran'] = $data['metode_pembayaran'];
				$dataExport[$key]['nama_toko'] = $data['toko'];
				$dataExport[$key]['payment_status'] = $data['payment_status'];
				$dataExport[$key]['total_pembayaran'] =$data['total_pembayaran'];
				$dataExport[$key]['dp_payment'] =$data['dp_payment'];
				$dataExport[$key]['sisa'] = $data['sisa'];
				$dataExport[$key]['harga_beli'] =$data['harga_beli'];
				$dataExport[$key]['keuntungan'] =$data['keuntungan'];
				if($data['status'] == 2){
					$dataExport[$key]['status_name'] = 'Draft';
				}else{
					$dataExport[$key]['status_name'] = 'Done';
				}
				$dataExport[$key]['nama_project'] = $data['nama_project'];
				$total_dp += $dataExport[$key]['dp_payment'];
				$total_sisa += $dataExport[$key]['sisa'];
				$total_hrg_beli += $dataExport[$key]['harga_beli'];
				$total_pembayaran += $dataExport[$key]['total_pembayaran'];
				$total_keuntungan += $dataExport[$key]['keuntungan'];
				$no++;
			}
        else:
			//berdasarkan barang
			$sub_judul = 'Berdasarkan Barang';
			foreach ($datas as $key => $data) {
				$dataExport[$key]['no'] = $no;
				$dataExport[$key]['tanggal'] = date('d F Y', strtotime($data['tanggal']));
				$dataExport[$key]['no_ref'] = $data['no_ref'];
				$dataExport[$key]['nama_product'] = $data['barang']['nama_product'];
				$dataExport[$key]['serial_number'] = $data['gudang_barang']['serial_number'];
				$dataExport[$key]['nama_toko'] = $data['toko'];
				if($data['gudang_barang']['detail_barang_masuk'] != ''){
					$dataExport[$key]['harga_beli'] = $data['gudang_barang']['detail_barang_masuk']['stock_in']['harga_beli'];
				}else{
					$dataExport[$key]['harga_beli'] = '';
				}
				$dataExport[$key]['harga_jual'] = $data['price']-($data['price'] * $data['discount'] / 100);
				if($dataExport[$key]['harga_beli'] != ''){
					$dataExport[$key]['keuntungan'] = $dataExport[$key]['harga_jual'] - $dataExport[$key]['harga_beli'];
				}else{
					$dataExport[$key]['keuntungan'] = $dataExport[$key]['harga_jual'] - 0;
				}
				$total_hrg_beli += $dataExport[$key]['harga_beli'];
				$total_hrg_jual += $dataExport[$key]['harga_jual'];
				$total_keuntungan += $dataExport[$key]['keuntungan'];
				$no++;
			}
        endif;
        $key_last = count($dataExport);
        if($jenis_report == '2'):
				$dataExport[$key]['no'] = '';
				$dataExport[$key]['tanggal'] = 'TOTAL';
				$dataExport[$key]['kode_penjualan'] = '';
				$dataExport[$key]['nama_pembeli'] = '';
				$dataExport[$key]['metode_pembayaran'] = '';
				$dataExport[$key]['nama_toko'] = '';
				$dataExport[$key]['payment_status'] = '';
				$dataExport[$key]['total_pembayaran'] =$total_pembayaran;
				$dataExport[$key]['dp_payment'] = $total_dp;
				$dataExport[$key]['sisa'] =$total_sisa;
				$dataExport[$key]['harga_beli'] =$total_hrg_beli;
				$dataExport[$key]['keuntungan'] =$total_keuntungan;
				$dataExport[$key]['status_name'] = '';
				$dataExport[$key]['nama_project'] = '';
        else:
				$dataExport[$key]['no'] = '';
				$dataExport[$key]['tanggal'] = 'TOTAL';
				$dataExport[$key]['no_ref'] = '';
				$dataExport[$key]['nama_product'] = '';
				$dataExport[$key]['serial_number'] = '';
				$dataExport[$key]['nama_toko'] = '';
				$dataExport[$key]['harga_beli'] =  $total_hrg_beli;
				$dataExport[$key]['harga_jual'] = $total_hrg_jual;
				$dataExport[$key]['keuntungan'] = $total_keuntungan;
					
        endif;
        
        $data_array = array_merge($column, $dataExport);
        $export = new ReportExport($data_array);
        return Excel::download($export, 'Report Laba Rugi - '.$sub_judul . '.xlsx');
    }
    
    public function barang_po()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Report", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            return view('report.po', compact('nama_toko'));
        }else{
            return abort(403);
        }
    }
    
    public function get_barang_po(Request $request)
    {
		$cek_barang = false;
		$supplier = $request->nama_supplier;
        $from = date('Y-m-d', strtotime($request->tanggalAwal));
        $to = date('Y-m-d', strtotime($request->tanggalAkhir));
        $nama_toko = $request->nama_toko;
        //DB::enableQueryLog();
        
        $query = Po::with('toko','supplier');
        $query->whereBetween('date', [$from, $to]);
        if($request->jatuh_tempo_awal != '' AND $request->jatuh_tempo_akhir != ''):
			$tempo_awal = date('Y-m-d', strtotime($request->jatuh_tempo_awal));
			$tempo_akhir = date('Y-m-d', strtotime($request->jatuh_tempo_akhir));
			$query->whereBetween('jatuh_tempo', [$tempo_awal, $tempo_akhir ]);
        endif;
        if($request->nama_toko != null){
            $query->whereIn('toko_id',$nama_toko);
        }
        if($request->nama_supplier != null){
            $query->whereIn('supplier_id', $supplier);
        }
        if(auth()->user()->status != 1){
            $query->where('toko_id', auth()->user()->toko_id);
        }
        if($request->status_terima != 2 AND $request->status_terima != null){
            $query->where('status_terima', $request->status_terima );
        }
        if($request->status_bayar != 3 AND $request->status_bayar != null){
            if ($request->status_bayar == 0 OR $request->status_bayar == 1) {
                $query->where('status_bayar', $request->status_bayar);
                $query->where('po_dp', '0');
            }else{
                $query->where('po_dp', '>' , 0);
            }
            
        }
        if($request->status_po != 'semua' AND $request->status_terima != null){
            $query->where('status', $request->status_po );
        }
        
        $query->orderBy('date', 'desc');
        $pos = $query->get();
        $data = [];
        $total = 0 ;
        foreach ($pos as $key => $po) {
            $data[$key] = $po;
            $data[$key]['tanggal'] = date('d-F-Y', strtotime($po->date));
            if($po->jatuh_tempo != null AND $po->jatuh_tempo != ''):
				$data[$key]['tanggal_jtempo'] = date('d-F-Y', strtotime($po->jatuh_tempo));
            else:
				$data[$key]['tanggal_jtempo'] = '';
            endif;
            //$data[$key]['tanggal_jtempo'] = $po->jatuh_tempo;
            $data[$key]['total_pembayaran'] = number_format($po->subtotal);
            if(!empty($po->supplier)) {
				$data[$key]['nama_supplier'] = $po->supplier['nama_supplier'];
			}else{
				$data[$key]['nama_supplier'] = '';
			}
			if(!empty($po->supplier)) {
				$data[$key]['alamat'] = $po->supplier['alamat'];
			}else{
				$data[$key]['alamat'] = '';
			}
            if(!empty($po->telepon)) {
				$data[$key]['telepon'] = $po->telepon;
			}else{
				$data[$key]['telepon'] = '';
			}
			if(!empty($po->nama_purchase)) {
				$data[$key]['nama_purchase'] = $po->nama_purchase;
			}else{
				$data[$key]['nama_purchase'] = '';
			}
            if ($po->status == 2) {
                $data[$key]['status_name'] = 'Draft';
            } else {
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
            $sub_total = 0;
            if ($po->ppn != 0) {
                $sub_total = $po->subtotal + ($po->subtotal * 11 /100);
            } else {
                $sub_total = $po->subtotal;
            }
            $data[$key]['sub_total'] = number_format( $sub_total );
            if($sub_total > 0){
				$total +=  $sub_total ;
			}
           
            $data[$key]['nama_toko'] =  $po->toko['nama_toko'];
        }
        $key_last = count($data);
        $data[$key_last]['id'] = '';
        $data[$key_last]['tanggal'] = '';
        $data[$key_last]['kode_po']  = '';
        $data[$key_last]['nama_purchase'] = '';
        $data[$key_last]['nama_supplier'] ='';
        $data[$key_last]['alamat'] = ' ';
        $data[$key_last]['telepon'] = '';
        $data[$key_last]['total_pembayaran'] = '';
        $data[$key_last]['status_name'] = ''; 
        $data[$key_last]['nama_toko'] ='';
        $data[$key_last]['status_terima'] ='';
        $data[$key_last]['status_bayar'] = '';
        $data[$key_last]['tanggal_jtempo'] = '';
        $data[$key_last]['sub_total'] = number_format( $total);
        return response()->json($data);
    }

    public function export_barang_po(Request $request)
    {
		
        $column = array(
            array(
                    "Id",
                    "Tanggal",
                    "Kode PO",
                    "Nama Purchasing",
                    "Nama Supplier",
                    "Alamat",
                    "Telepon",
                    "Total Pembayaran",
                    "Status PO",
                    "Toko",
                    "Status Barang",
                    "Status Bayar",
                    "Jatuh Tempo",
                    "Keterangan",
                )
        );
        $supplier = $request->nama_supplier;
        $from = date('Y-m-d', strtotime($request->tanggalAwal));
        $to = date('Y-m-d', strtotime($request->tanggalAkhir));
        $nama_toko = $request->nama_toko;
        //DB::enableQueryLog();
        
        $query = Po::with('toko','supplier');
        $query->whereBetween('date', [$from, $to]);
         if($request->jatuh_tempo_awal != '' AND $request->jatuh_tempo_akhir != ''):
			$tempo_awal = date('Y-m-d', strtotime($request->jatuh_tempo_awal));
			$tempo_akhir = date('Y-m-d', strtotime($request->jatuh_tempo_akhir));
			$query->whereBetween('jatuh_tempo', [$tempo_awal, $tempo_akhir ]);
        endif;
        if($request->nama_toko != null){
            $query->whereIn('toko_id',$nama_toko);
        }
        if($request->nama_supplier != null){
            $query->whereIn('supplier_id', $supplier);
        }
        if(auth()->user()->status != 1){
            $query->where('toko_id', auth()->user()->toko_id);
        }
        if($request->status_terima != 2 AND $request->status_terima != null){
            $query->where('status_terima', $request->status_terima );
        }
        if($request->status_bayar != 2 AND $request->status_terima != null){
            $query->where('status_bayar', $request->status_bayar );
        }
        if($request->status_po != 'semua' AND $request->status_terima != null){
            $query->where('status', $request->status_po );
        }
        $query->orderBy('date', 'desc');
        $pos = $query->get();
        $data = [];
        $total = 0;
        foreach ($pos as $key => $po) {
            $data[$key]['id'] = $po->id;
            $data[$key]['tanggal'] = date('d-F-Y', strtotime($po->date));
            $data[$key]['kode_po'] = $po->kode_po;
			if(!empty($po->nama_purchase)) {
				$data[$key]['nama_purchase'] = $po->nama_purchase;
			}else{
				$data[$key]['nama_purchase'] = '';
			}
			if(!empty($po->supplier)) {
				$data[$key]['nama_supplier'] = $po->supplier['nama_supplier'];
			}else{
				$data[$key]['nama_supplier'] = '';
			}
			if(!empty($po->supplier)) {
				$data[$key]['alamat'] = $po->supplier['alamat'];
			}else{
				$data[$key]['alamat'] = '';
			}
			if(!empty($po->telepon)) {
				$data[$key]['telepon'] = $po->telepon;
			}else{
				$data[$key]['telepon'] = '';
			}
			$total_pembayaran = 0 ;
			if ($po->ppn != 0) {
                $total_pembayaran  = $po->subtotal + ($po->subtotal * 11 /100);
            } else {
                $total_pembayaran = $po->subtotal;
            }
            if($total_pembayaran > 0):
				$total += $total_pembayaran;
            endif;
            $data[$key]['total_pembayaran'] = number_format($total_pembayaran);
			//$data[$key]['total_pembayaran'] = number_format($po->subtotal);
			 if ($po->status == 2) {
                $data[$key]['status_name'] = 'Draft';
            } else {
                $data[$key]['status_name'] = 'Dikirim';
            }
			if(!empty($po->toko)) {
				$data[$key]['nama_toko'] =  $po->toko['nama_toko'];
			}else{
				$data[$key]['nama_toko'] = '';
			}
			
            if ($po->status_terima == 1) {
                $data[$key]['status_terima'] = 'Sudah DiTerima';
            } else {
                $data[$key]['status_terima'] = 'Belum DiTerima';
            }
            if ($po->status_bayar == 1) {
                $data[$key]['status_bayar'] = 'Lunas';
            } else {
                $data[$key]['status_bayar'] = 'Hutang';
            }
            if ($po->jatuh_tempo != null AND $po->jatuh_tempo != ''):
				$data[$key]['tanggal_jtempo'] = date('d-F-Y', strtotime($po->jatuh_tempo));
            else:
				$data[$key]['tanggal_jtempo'] = '';
            endif;
            $data[$key]['keterangan'] = $po->keterangan;
        }
        $key_last = count($data);
        $data[$key_last]['id'] = '';
        $data[$key_last]['tanggal'] = '';
        $data[$key_last]['kode_po']  = '';
        $data[$key_last]['nama_purchase'] = '';
        $data[$key_last]['nama_supplier'] ='';
        $data[$key_last]['alamat'] = ' ';
        $data[$key_last]['telepon'] = '';
        $data[$key_last]['total_pembayaran'] = number_format( $total);
        $data[$key_last]['status_name'] = ''; 
        $data[$key_last]['nama_toko'] ='';
        $data[$key_last]['status_terima'] ='';
        $data[$key_last]['status_bayar'] = '';
        $data[$key_last]['tanggal_jtempo'] = '';
        //$data[$key_last]['sub_total'] = number_format( $total);
        $data_array = array_merge($column, $data);
        $export = new ReportExport($data_array);
        return Excel::download($export, 'Report Purchase Order' . '.xlsx');
    }


}
