<?php

namespace App\Http\Controllers;

use App\Models\GudangBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\StockIn;
use App\Models\SerialNumber;
use App\Models\DetailBarangMasuk;
use App\Models\DataBarangKeluar;
use App\Models\DetailPenjualan;
use App\Models\DetailBarangKeluar;
use App\Models\Toko;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Auth;

class StockInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Barang Masuk", $menu)){
            $nama_toko = '';
            $user = Auth::user();
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            return view('stock.in', compact('user','nama_toko'));
        }else{
            return abort(404);
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
        if(auth()->user()->status == 1 || in_array("Barang Masuk", $menu)){
            return view('stock.in_create');
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
        $harga_beli = $request->harga_beli;
        if(preg_match("/^[0-9,]+$/", $request->harga_beli)){
            $harga_beli = str_replace(',', '',$request->harga_beli);
        }
        $harga_jual = $request->harga_jual;
        if(preg_match("/^[0-9,]+$/", $request->harga_jual)){
            $harga_jual = str_replace(',', '',$request->harga_jual);
        }
        $price_list = $request->price_list;
        if(preg_match("/^[0-9,]+$/", $request->price_list)){
            $price_list = str_replace(',', '',$request->price_list);
        }

        $create = StockIn::create([
            "barang_id" => $request->nama_barang,
            "jumlah" => $request->jumlah,
            "tanggal_masuk" => date('Y-m-d', strtotime($request->tanggal_masuk)),
            "harga_beli" => $harga_beli,
            "harga_jual" => $harga_jual,
            "price_list" => $price_list,
            "made_in" => $request->made_in,
            "supplier" => $request->supplier,
            "toko_id" => $request->nama_toko,
            "type_serial_number" => $request->serial_number,
            "keterangan" => $request->keterangan,
        ]);
        if($request->serial_number == 2){
            $product_details = $request->product_detail;
        }else{
            $product_details = preg_split('/[\ \n\,]+/', $request->product_detail[0]);
        }
        for ($i=0; $i <$request->jumlah; $i++) { 
            if(isset($product_details[$i])){
                $data_gudang[$i]['serial_number'] = $product_details[$i];
            }else{
                $data_gudang[$i]['serial_number'] = '';
            }
            $data_gudang[$i]['toko_id'] = $request->nama_toko;
            $data_gudang[$i]['barang_id'] = $request->nama_barang;
            $data_gudang[$i]['status'] = 1;//1 masuk
            $insert_data_gudang[$i] = GudangBarang::create($data_gudang[$i]);

            $data_product_detail[$i]['stock_in_id'] = $create->id;
            $data_product_detail[$i]['gudang_barang_id'] = $insert_data_gudang[$i]->id;
            $serial_number[$i] = DetailBarangMasuk::create($data_product_detail[$i]);
            
        }

        return response()->json($create);
    }

    public function array_except($array, $keys){
        foreach($keys as $key){
            unset($array[$key]);
        }
        return $array;
    }

    public function insert_po(Request $request, $id)
    {
        foreach ($request->databarang as $key => $value) {
            $create[$key] = StockIn::create([
                "barang_id" => $value[0],
                "po_id" => $id,
                "jumlah" => $value[1],
                "tanggal_masuk" => date('Y-m-d', strtotime($request->date)),
                "harga_beli" => $value[2],
                "harga_jual" => $value[3],
                "price_list" => $value[4],
                "made_in" => $value[5],
                "supplier" => $request->nama_supplier,
                "toko_id" => $request->nama_toko,
                "type_serial_number" => $value[6],
                "keterangan" => $request->keterangan_pembayaran,
            ]);

            if(count($value) > 5){
                if($value[6] == 1){
                    $serial_number[$key] = explode(",",$value[7]);
                }else{
                    $array = array();
                    for ($i=0; $i < 7 ; $i++) {
                        $array[$i] = $i; 
                    }
                    $serial_number[$key] = array_values($this->array_except($value, $array));
                }
            }

            for ($i=0; $i <$value[1]-1; $i++) { 
                if(isset($serial_number[$key])){
                    $data_gudang[$i]['serial_number'] = $serial_number[$key][$i];
                }else{
                    $data_gudang[$i]['serial_number'] = '';
                }
                $data_gudang[$i]['toko_id'] = $request->nama_toko;
                $data_gudang[$i]['barang_id'] = $value[0];
                $data_gudang[$i]['status'] = 1;//1 masuk
                $insert_data_gudang[$i] = GudangBarang::create($data_gudang[$i]);

                $data_product_detail[$i]['stock_in_id'] = $create[$key]->id;
                $data_product_detail[$i]['gudang_barang_id'] = $insert_data_gudang[$i]->id;
                $serial_number[$i] = DetailBarangMasuk::create($data_product_detail[$i]);
            }            
        }

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
		$user = Auth::user();
		$query = StockIn::with(['barang', 'toko', 'po']);
        if($request->date_from != null){
            $from = date('Y-m-d', strtotime($request->date_from));
            $to = date('Y-m-d', strtotime($request->date_to));;
            $query->whereBetween('tanggal_masuk', [$from, $to]);
        }
        if($request->toko != null){
            $query->whereIn('toko_id', explode(",", $request->toko));
        }
        if($request->supplier != null){
            $query->whereIn('supplier_id', explode(",", $request->supplier));
        }
        if($user->status == 2){
            $query->where('toko_id', $user->toko_id);
        }
        $query->orderBy('tanggal_masuk', 'desc');
        $stocks = $query->get();
        $data = [];
        
        
        
        foreach ($stocks as $key => $stock) {
            $data[$key] = $stock;
            if($data[$key]['po'] != null){
                $data[$key]['kode_po'] = $stock['po']['kode_po'];
            }else{
                $data[$key]['kode_po'] = '';
            }
            $data[$key]['tanggal'] = date('d F Y', strtotime($data[$key]['tanggal_masuk']));
            $data[$key]['harga_beli'] = number_format($data[$key]['harga_beli']);
            $data[$key]['harga_jual'] = number_format($data[$key]['harga_jual']);
            $data[$key]['price_list'] = number_format($data[$key]['price_list']);
        }
        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
            $btn = '';
            $user = Auth::user();
            if($user->status == 1 || ($row->toko_id == $user->toko_id)){
                $btn = '<div class="button"><button class="btn btn-icon btn-info btn-edit" data-id="'.$row->id.'"><i class="far fa-edit"></i></button> <button data-id="'.$row->id.'" href="#" class="btn btn-icon btn-danger btn-delete"><i class="far fas fa-trash"></i></button></div>';
            }
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
        $edit = StockIn::with(['barang', 'toko', 'gudang_barang', 'detail_barang_masuk'])->find($id);
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
        $harga_beli = $request->edit_harga_beli;
        if(preg_match("/^[0-9,]+$/", $request->edit_harga_beli)){
            $harga_beli = str_replace(',', '',$request->edit_harga_beli);
        }
        $harga_jual = $request->edit_harga_jual;
        if(preg_match("/^[0-9,]+$/", $request->edit_harga_jual)){
            $harga_jual = str_replace(',', '',$request->edit_harga_jual);
        }
        $price_list = $request->edit_price_list;
        if(preg_match("/^[0-9,]+$/", $request->edit_price_list)){
            $price_list = str_replace(',', '',$request->edit_price_list);
        }
        $update = StockIn::where('id',$request->id)->update([
            "barang_id" => $request->edit_nama_barang,
            "jumlah" => $request->edit_jumlah,
            "tanggal_masuk" => date('Y-m-d', strtotime($request->edit_tanggal_masuk)),
            "harga_beli" => $harga_beli,
            "harga_jual" => $harga_jual,
            "price_list" => $price_list,
            "made_in" => $request->edit_made_in,
            "supplier" => $request->edit_supplier,
            "toko_id" => $request->edit_nama_toko,
            "type_serial_number" => $request->edit_type_serial_number,
            "keterangan" => $request->edit_keterangan,
        ]);

        $gudangBarangId = DetailBarangMasuk::select('gudang_barang_id')->where('stock_in_id',$request->id)->get()->toArray();

        $updateGudangBarang = GudangBarang::whereIn('id', $gudangBarangId)->update([
            'toko_id' => $request->edit_nama_toko
            
        ]);
        //start update serial number
        $datasn = $request->datasn;
        $datasn_id = $request->datasn_id;
        $data_gudang = array();
        $data_product_detail = array();
        $serial_number = array();
        $insert_data_gudang = array();
        
        for ($i=0; $i <count( $datasn); $i++) { 
			if(empty($datasn_id[$i])):
				//INSERT SERIAL NUMBER BARU
				$data_gudang[$i]['serial_number'] = $datasn[$i];
				$data_gudang[$i]['toko_id'] = $request->edit_nama_toko;
				$data_gudang[$i]['barang_id'] = $request->edit_nama_barang;
				$data_gudang[$i]['status'] = 1;//1 masuk
				$insert_data_gudang[$i] = GudangBarang::create($data_gudang[$i]);
				$data_product_detail[$i]['stock_in_id'] = $request->id;
				$data_product_detail[$i]['gudang_barang_id'] = $insert_data_gudang[$i]->id;
				$serial_number[$i] = DetailBarangMasuk::create($data_product_detail[$i]);
			else:
				///UPDATE SERIAL NUMBER
				if( $datasn[$i] == 'deleted'):
					$deleteGudangBarang = GudangBarang::where('id', $datasn_id[$i])->forceDelete();
					$deletebarangMasuk = DetailBarangMasuk::where('gudang_barang_id', $datasn_id[$i])->forceDelete();
                    $deleteDataBarangKeluar = DataBarangKeluar::where('gudang_barang_id', $datasn_id[$i])->forceDelete();
                    $deleteDataPenjualan = DetailPenjualan::where('gudang_barang_id', $datasn_id[$i])->forceDelete();
                    $deletedetailbarangkeluar = DetailBarangKeluar::where('gudang_barang_id', $datasn_id[$i])->forceDelete();
				else:
					//update
					$insert_data_gudang[$i] = GudangBarang::where('id', $datasn_id[$i])->update(['serial_number' => $datasn[$i], 'barang_id' => $request->edit_nama_barang ]);
				endif;
			endif;
          
        }
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
        $delete = StockIn::destroy($request->id);
        $getDetailBarangMasuk = DetailBarangMasuk::select('gudang_barang_id')->where('stock_in_id', $request->id)->get();
        $deleteGudangBarang = GudangBarang::whereIn('id', $getDetailBarangMasuk)->delete();
        $deletesn = DetailBarangMasuk::where('stock_in_id', $request->id)->delete();
        return response()->json($delete);
    }

    public function getSerialNumber($id)
    {
        $edit = ProductDetail::where('stock_in_id', $id)->get();
        return response()->json($edit);
    }

    public function getPriceListBarang(Request $request)
    {
        $price = StockIn::where('barang_id', $request->id)
            ->whereNotNull('price_list')
            ->where('price_list', '!=', '')
            ->where('price_list', '>', 0)
            ->get();

        if ($price->isEmpty()) {
            $barang = Barang::find($request->id);
            if ($barang) {
                return response()->json([
                    [
                        'price_list' => $barang->harga ?? 0,
                        'barang_id' => $barang->id,
                    ]
                ]);
            }
        }
        return response()->json($price);
    }

    public function duplicate(Request $request)
    {
        $serial_numbers = GudangBarang::where('barang_id', $request->barang_id)->get();
        $data_sn = array();
        foreach ($serial_numbers as $key => $value) {
            $data_sn[] = $value->serial_number;
        }
        return response()->json($data_sn);
    }
}
