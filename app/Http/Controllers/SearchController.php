<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;

use Illuminate\Http\Request;
use App\Models\Toko;
use App\Models\GudangBarang;
use App\Models\Barang;
use DataTables;
use Storage;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Search", $menu)){
            return view('search.list');
        }else{
            return abort(404);
        }
    }

    public function select(Request $request)
    {
        $search = $request->term;
        //GudangBarang::withTrashed()
        $data = GudangBarang::select("id", "serial_number as text")
            ->where('serial_number', 'LIKE', "%$search%")
            // ->where('deleted_at', NULL)
            ->withTrashed()->get();
        $results = array(
            "results" => $data,
        );
        return response()->json($results);
    }

    public function search(Request $request)
    {
        //$datas = GudangBarang::withTrashed()->with('detail_barang_masuk.stock_in.po','detail_penjualan.penjualan', 'barang', 'toko')->whereIn('id', $request->all())->get();
        $datas = GudangBarang::with('detail_barang_masuk.stock_in.po','detail_penjualan.penjualan', 'barang', 'toko')->whereIn('id', $request->all())->withTrashed()->get();
        return response()->json($datas);
    }
    
    public function selectnama(Request $request)
    {
        $search = $request->term;
        $data = Barang::withTrashed()
            ->select("id", DB::raw("CONCAT(nama_product,' ',warna,' - ID -',id) as text"))
            ->where('nama_product', 'LIKE', "%$search%")
            ->where('deleted_at', '=',null)
            ->get();
        $results = array(
            "results" => $data,
        );
        return response()->json($results);
    }
    
     public function searchnama(Request $request)
    {
		$arr_data = array();
		// DB::enableQueryLog();
		$stock = DB::table('stock_ins')
			->join('barangs', 'stock_ins.barang_id', '=', 'barangs.id')
			->join('detail_barang_masuks', 'stock_ins.id', '=', 'detail_barang_masuks.stock_in_id')
			->join('gudang_barangs', 'detail_barang_masuks.gudang_barang_id', '=', 'gudang_barangs.id')
			->join('tokos', 'gudang_barangs.toko_id', '=', 'tokos.id')
			 ->select('detail_barang_masuks.id','gudang_barangs.serial_number','stock_ins.barang_id','stock_ins.tanggal_masuk','barangs.warna' , 'tokos.nama_toko', 'stock_ins.harga_beli','stock_ins.harga_jual', 'stock_ins.price_list','barangs.nama_product','detail_barang_masuks.gudang_barang_id')
			->whereIn('stock_ins.barang_id', $request->all())
			// ->where('gudang_barangs.deleted_at', null)
			->OrderBy('detail_barang_masuks.id', 'DESC')
			->get();
			
		//	dd(DB::getQueryLog());exit;
		$total_jumlah = 0; $total_harga_jual = 0; $total_harga_beli = 0; 
		$total_harga_pricelist = 0; $total_harga_terjual = 0; 
		if ($stock->count() > 0):
			foreach($stock as $k=>$rs):
				$arr_data[$k]['nama_product'] = $rs->nama_product;
				$arr_data[$k]['id'] = $rs->id;
				$arr_data[$k]['barang_id'] = $rs->barang_id;
				$arr_data[$k]['serial_number'] = $rs->serial_number;
				$arr_data[$k]['warna'] = $rs->warna;
				$arr_data[$k]['jumlah'] = 1;
				$arr_data[$k]['toko'] = $rs->nama_toko;
				$total_jumlah += 1;
				$str_tanggal_masuk = null;
				if($rs->tanggal_masuk != null):
					$str_tanggal_masuk =  date('d M Y', strtotime($rs->tanggal_masuk));
				endif;
				$arr_data[$k]['tanggal_masuk'] = $str_tanggal_masuk;
				if($rs->harga_beli != null):
					$arr_data[$k]['harga_beli'] = number_format($rs->harga_beli,0,",",".");
					$total_harga_beli += $rs->harga_beli; 
				else:
					$arr_data[$k]['harga_beli'] = null;
				endif;
				if($rs->harga_jual != null):
					$arr_data[$k]['harga_jual'] = number_format($rs->harga_jual,0,",",".");
					$total_harga_jual += $rs->harga_jual;
				else:
					$arr_data[$k]['harga_jual'] = null;
				endif;
				if($rs->price_list != null):
					$arr_data[$k]['price_list'] = number_format($rs->price_list,0,",",".");
					$total_harga_pricelist += $rs->price_list;
				else:
					$arr_data[$k]['price_list'] = null;
				endif;
				
				//cek po
 				$datapo = DB::table('po_details')
				->join('barangs', 'po_details.barang_id', '=', 'barangs.id')
				->join('pos', 'po_details.po_id', '=', 'pos.id')
				->select('pos.kode_po','po_details.jumlah as jml_po','po_details.price as hrg_beli')
				->where('po_details.barang_id', '=', $rs->barang_id )
				->where('pos.date', '=', $rs->tanggal_masuk )
				->limit(1)
				->get();
				if ($datapo->count() > 0):
					//$stock[$k]->datapo = $datapo;
					$arr_data[$k]['kode_po'] = $datapo[0]->kode_po;
					$arr_data[$k]['jml_po'] = $datapo[0]->jml_po;
					
				else:	
					//$stock[$k]->datapo = null;
					$arr_data[$k]['kode_po'] = null;
					$arr_data[$k]['jml_po'] = null;
				endif;
				// cek stock 
				$stock_barang = $this->stock($rs->barang_id, $rs->serial_number);
				if ($stock_barang == 0) {
					//get data barang keluar
					$arr_data[$k]['status'] = '';
					$barangkeluar = DB::table('detail_barang_keluars')
					->leftjoin('pindah_gudangs', 'detail_barang_keluars.pindah_gudang_id', '=', 'pindah_gudangs.id')
					->where('detail_barang_keluars.barang_id', '=', $rs->barang_id )
					->where('detail_barang_keluars.gudang_barang_id', '=',  $rs->gudang_barang_id)
					->select('pindah_gudangs.date as tgl_keluar')
					->limit(1)
					->get();
					if ($barangkeluar->count() >  0):
						$str_tanggal_keluar = null;
						if($barangkeluar[0]->tgl_keluar != null):
							$str_tanggal_keluar =  date('d M Y', strtotime($barangkeluar[0]->tgl_keluar));
						endif;
						$arr_data[$k]['status'] = 'Barang Keluar';
						$arr_data[$k]['tgl_keluar'] = $str_tanggal_keluar;
						
						//echo var_dump($barangkeluar);
					else:
						$arr_data[$k]['tgl_keluar'] = null;
					endif;
					$penjualan = DB::table('detail_penjualans')
					->leftjoin('penjualans', 'detail_penjualans.penjualan_id', '=', 'penjualans.id')
					->where('detail_penjualans.barang_id', '=', $rs->barang_id )
					->where('detail_penjualans.gudang_barang_id', '=', $rs->gudang_barang_id)
					->select('detail_penjualans.penjualan_id','detail_penjualans.barang_id as barang_id_penjualan', 
					'detail_penjualans.price as hrg_terjual', 'penjualans.nama_pembeli', 'penjualans.date')
					->limit(1)
					->get();
					if ($penjualan->count() >  0):
						$arr_data[$k]['penjualan_id'] = $penjualan[0]->penjualan_id;
						$arr_data[$k]['date_jual'] = $penjualan[0]->date;

						if($penjualan[0]->hrg_terjual != null):
							$arr_data[$k]['status'] = 'Terjual';
							$arr_data[$k]['hrg_terjual'] = number_format($penjualan[0]->hrg_terjual,0,",",".");
							$total_harga_terjual += $penjualan[0]->hrg_terjual;
						else:
							$arr_data[$k]['hrg_terjual'] = null;
						endif;
						$arr_data[$k]['nama_pembeli'] = $penjualan[0]->nama_pembeli;
					else:
						$arr_data[$k]['penjualan_id'] = null;
						$arr_data[$k]['hrg_terjual'] = null;
						$arr_data[$k]['nama_pembeli'] = null;
						$arr_data[$k]['date_jual'] = null;
					endif;
				
					
				}else {
					$arr_data[$k]['penjualan_id'] = null;
					$arr_data[$k]['hrg_terjual'] = null;
					$arr_data[$k]['nama_pembeli'] = null;
					$arr_data[$k]['date_jual'] = null;
					$arr_data[$k]['status'] = 'Stock';
				}
			
				
			endforeach;
		endif;
		
		$result['data'] = $arr_data;
		$result['summary'] = array('total_jumlah' => number_format($total_jumlah,0,",",".") , 
		'total_harga_jual' => number_format($total_harga_jual,0,",","."), 
		'total_harga_beli' => number_format($total_harga_beli,0,",","."), 
		'total_harga_pricelist' => number_format($total_harga_pricelist,0,",","."), 
		'total_harga_terjual' => number_format($total_harga_terjual,0,",",".") );
		
		
        //$datas = GudangBarang::withTrashed()->with('detail_barang_masuk.stock_in.po','detail_penjualan.penjualan', 'barang', 'toko')->whereIn('id', $request->all())->get();
        return response()->json($result);
    }
	public function stock($id, $serial_number)
    {
        $query = GudangBarang::where('barang_id', $id)->where('serial_number', $serial_number);   
        return $query->count();
    }
    
    
     public function export_search_nama(Request $request)
    {
		$arr_data = array();
		// DB::enableQueryLog();
		$stock = DB::table('stock_ins')
			->join('barangs', 'stock_ins.barang_id', '=', 'barangs.id')
			->join('detail_barang_masuks', 'stock_ins.id', '=', 'detail_barang_masuks.stock_in_id')
			->join('gudang_barangs', 'detail_barang_masuks.gudang_barang_id', '=', 'gudang_barangs.id')
			 ->select('detail_barang_masuks.id','gudang_barangs.serial_number','stock_ins.barang_id','stock_ins.tanggal_masuk','barangs.warna', 'stock_ins.harga_beli','stock_ins.harga_jual', 'stock_ins.price_list','barangs.nama_product','detail_barang_masuks.gudang_barang_id')
			->whereIn('stock_ins.barang_id', $request->all())
			->get();
			
		//	dd(DB::getQueryLog());exit;
		$total_jumlah = 0; $total_harga_jual = 0; $total_harga_beli = 0; 
		$total_harga_pricelist = 0; $total_harga_terjual = 0; 
		if ($stock->count() > 0):
			foreach($stock as $k=>$rs):
				$arr_data[$k]['id'] = $rs->id;
				//cek po
 				$datapo = DB::table('po_details')
				->join('barangs', 'po_details.barang_id', '=', 'barangs.id')
				->join('pos', 'po_details.po_id', '=', 'pos.id')
				->select('pos.kode_po','po_details.jumlah as jml_po','po_details.price as hrg_beli')
				->where('po_details.barang_id', '=', $rs->barang_id )
				->where('pos.date', '=', $rs->tanggal_masuk )
				->limit(1)
				->get();
				if ($datapo->count() > 0):
					//$stock[$k]->datapo = $datapo;
					$arr_data[$k]['kode_po'] = $datapo[0]->kode_po;
				else:	
					$arr_data[$k]['kode_po'] = null;
				endif;
				$arr_data[$k]['nama_product'] = $rs->nama_product;
				$arr_data[$k]['warna'] = $rs->warna;
				$arr_data[$k]['jumlah'] = 1;
				$arr_data[$k]['serial_number'] = $rs->serial_number;
				
				$total_jumlah += 1;
				$str_tanggal_masuk = null;
				if($rs->tanggal_masuk != null):
					$str_tanggal_masuk =  date('d F Y', strtotime($rs->tanggal_masuk));
				endif;
				$arr_data[$k]['tanggal_masuk'] = $str_tanggal_masuk;
				if($rs->harga_beli != null):
					$arr_data[$k]['harga_beli'] = number_format($rs->harga_beli,0,".",",");
					$total_harga_beli += $rs->harga_beli; 
				else:
					$arr_data[$k]['harga_beli'] = null;
				endif;
				if($rs->harga_jual != null):
					$arr_data[$k]['harga_jual'] = number_format($rs->harga_jual,0,".",",");
					$total_harga_jual += $rs->harga_jual;
				else:
					$arr_data[$k]['harga_jual'] = null;
				endif;
				if($rs->price_list != null):
					$arr_data[$k]['price_list'] = number_format($rs->price_list,0,".",",");
					$total_harga_pricelist += $rs->price_list;
				else:
					$arr_data[$k]['price_list'] = null;
				endif;
				$barangkeluar = DB::table('detail_barang_keluars')
				->leftjoin('pindah_gudangs', 'detail_barang_keluars.pindah_gudang_id', '=', 'pindah_gudangs.id')
				->where('detail_barang_keluars.barang_id', '=', $rs->barang_id )
				->where('detail_barang_keluars.gudang_barang_id', '=',  $rs->gudang_barang_id)
				->select('pindah_gudangs.date as tgl_keluar')
				->limit(1)
				->get();
				if ($barangkeluar->count() >  0):
					$str_tanggal_keluar = null;
					if($barangkeluar[0]->tgl_keluar != null):
						$str_tanggal_keluar =  date('d F Y', strtotime($barangkeluar[0]->tgl_keluar));
					endif;
					$arr_data[$k]['tgl_keluar'] = $str_tanggal_keluar;
				else:
					$arr_data[$k]['tgl_keluar'] = null;
				endif;
					
				//get data barang keluar
				$penjualan = DB::table('detail_penjualans')
				->leftjoin('penjualans', 'detail_penjualans.penjualan_id', '=', 'penjualans.id')
				->where('detail_penjualans.barang_id', '=', $rs->barang_id )
				->where('detail_penjualans.gudang_barang_id', '=', $rs->gudang_barang_id)
				->select('detail_penjualans.penjualan_id','detail_penjualans.barang_id as barang_id_penjualan', 
				 'detail_penjualans.price as hrg_terjual', 'penjualans.nama_pembeli')
				 ->limit(1)
				->get();
				if ($penjualan->count() >  0):
					if($penjualan[0]->hrg_terjual != null):
						$arr_data[$k]['hrg_terjual'] = number_format($penjualan[0]->hrg_terjual,0,".",",");
						$total_harga_terjual += $penjualan[0]->hrg_terjual;
					else:
						$arr_data[$k]['hrg_terjual'] = null;
					endif;
					$arr_data[$k]['nama_pembeli'] = $penjualan[0]->nama_pembeli;
				else:
					$arr_data[$k]['hrg_terjual'] = null;
					$arr_data[$k]['nama_pembeli'] = null;
				endif;
				
			endforeach;
		endif;
		
		
		$summary = array(
			array(
				'',
				'',
				'JUMLAH', 
				'',
				number_format($total_jumlah,0,".",","),
				'',
				'',
				number_format($total_harga_beli,0,".",","), 
				number_format($total_harga_jual,0,".",","),
				number_format($total_harga_pricelist,0,".",","), 
				'',
				number_format($total_harga_terjual,0,".",","),
				'' )
			);
		
	  $column = array(
            array(
                    "ID",
                    "No PO",
                    "Nama Barang",
                    "Warna",
                    "Jumlah",
                    "Serial Number",
                    "Tanggal Masuk",
                    "Harga Beli",
                    "Harga Jual",
                    "Price List",
                    "Tanggal Keluar",
                    "Harga Terjual",
                    "Nama Pembeli"
                )
        );
       
        $data_array = array_merge($column, $arr_data, $summary);
        $export = new ReportExport($data_array);
        return Excel::download($export, 'Search Nama Barang' . '.xlsx');
    }
}
