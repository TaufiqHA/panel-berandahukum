<?php

namespace App\Http\Controllers;

use App\Models\DetailBarangKeluar;
use App\Models\PindahGudang;
use App\Models\GudangBarang;
use App\Models\SerialNumber;
use App\Models\StockIn;
use App\Models\Toko;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use PDF;

class PindahTokoController extends Controller
{
    public function in()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Pindah Toko", $menu)){
            return view('pindah-toko.barang-masuk.in');
        }else{
            return abort(403);
        }
    }

    public function out()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Pindah Toko", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            return view('pindah-toko.barang-keluar.list');
        }else{
            return abort(403);
        }
    }

    public function edit($id)
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Pindah Toko", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            $pindahGudang = PindahGudang::with(['toko', 'toko_to', 'data_detail_barang', 'data_detail_serial_number', 'detail_barang_keluar'])->find($id);
            return view('pindah-toko.barang-keluar.edit', compact('pindahGudang', 'nama_toko'));
        }else{
            return abort(403);
        }
    }

    public function create()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Pindah Toko", $menu)){
            $nama_toko = '';
            if (Auth::user()->status == 2){
                $get = Toko::select('nama_toko')->where('id',Auth::user()->toko_id)->first();
                $nama_toko = $get['nama_toko'];
            }
            $pindahGudang = PindahGudang::orderBy('id', 'DESC')->first();
            return view('pindah-toko.barang-keluar.create', compact('pindahGudang', 'nama_toko'));
        }else{
            return abort(403);
        }
    }

    public function store(Request $request)
    {
        $create = PindahGudang::create([
            'date' => date('Y-m-d', strtotime($request->date)),
            'no_ref' => $request->ref_number,
            'from' => $request->nama_toko_from,
            'to' => $request->nama_toko_to,
            'status' => 1,
        ]);
        return response()->json($create);
    }

    public function barang(Request $request, $id)
    {
        $user = Auth::user();
        $pindahGudang = PindahGudang::where('id', $id)->first();
        $data_barang = array_chunk($request->all(),6);
        foreach ($data_barang as $key => $barangs) {
            if(count($barangs[4]) == 0){
                $query = GudangBarang::where('barang_id', $barangs[0])->where('toko_id',$pindahGudang->from)->where('status','=', 1)->limit($barangs[3]);
                if($user->status != 1){
                    $query = $query->where('toko_id',Auth::user()->toko_id);
                }
                $dataBarang = $query->get();
                foreach ($dataBarang as $key => $barang) {
                    $create[] = DetailBarangKeluar::create([
                        'pindah_gudang_id' => $id,
                        'barang_id' => $barangs[0],
                        'gudang_barang_id' => $barang->id,
                        'status' => 1,
                        'keterangan' => $barangs[5],
                    ]);
                    GudangBarang::where('id', $barang->id)->update(['status' => 3]);
                }
            }else{
                foreach ($barangs[4] as $k => $barang) {
                    $create[] = DetailBarangKeluar::create([
                        'pindah_gudang_id' => $id,
                        'barang_id' => $barangs[0],
                        'gudang_barang_id' => $barang,
                        'status' => 1,
                        'keterangan' => $barangs[5],
                    ]);    
                    GudangBarang::where('id', $barang)->update(['status' => 3]);
                }
            }
        }
        return response()->json($create);
    }

    public function dataOut(Request $request)
    {
        $user = Auth::user();
        $data = [];
        $query = PindahGudang::with(['toko', 'toko_to']);
        if($user->status != 1){
            $query = $query->where('from',Auth::user()->toko_id);
        }
        $query->orderBy('date', 'desc');    
        $stocks = $query->get();
        foreach ($stocks as $key => $stock) {
            if($stock['status'] == 1){
                $stock['status_name'] = 'Dikirim';
            }else{
                $stock['status_name'] = 'Diterima';
            }
            $stock['tanggal'] = date('d F Y', strtotime($stock['date']));
            $data[$key] = $stock;
        }
        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
            $edit = '';
            $delete = '';
            if($row->status == 1){
                $edit = '<a href="/pindah-toko/out/edit/'.$row->id.'" class="btn btn-icon btn-info btn-edit" data-id="'.$row->id.'"><i class="far fa-edit"></i></a> <br>';
                $delete = '<button data-id="'.$row->id.'" href="#" class="btn btn-icon btn-danger btn-delete"><i class="far fas fa-trash"></i></button>';

            }
            $btn = '<div class="button">'.$edit.'<a class="btn btn-icon btn-dark btn-print" target="_blank" href="/pindah-toko/out/print/'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Print"><i class="fas fa-print"></i></a>'.$delete.'</div>';
            return $btn;
        })->rawColumns(['action'])->make(true);
    }

    public function dataIn(Request $request)
    {
        $data = [];
        $query = PindahGudang::with(['toko', 'toko_to']);
        if(Auth::user()->status == 2){
            $query->where('to',Auth::user()->toko_id);
        }
        $query->orderBy('date', 'desc');
        $stocks = $query->get();
        foreach ($stocks as $key => $stock) {
            if($stock['status'] == 1){
                $stock['status_name'] = 'Dikirim';
            }else{
                $stock['status_name'] = 'Diterima';
            }
            $stock['tanggal'] = date('d F Y', strtotime($stock['date']));
            $data[$key] = $stock;
        }
        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
            $detail = '';
            $btn = '';
            if($row->status == 1){
                $detail = '<button data-id="'.$row->id.'" href="#" class="btn btn-icon btn-success btn-edit">
                                <i class="far fas fa-th"></i>
                            </button>';
            }
            $btn = '<div class="button">
                        '.$detail.'
                        <a class="btn btn-icon btn-dark btn-print" target="_blank" href="/pindah-toko/in/print/'.$row->id.'" data-toggle="tooltip" data-placement="top" title="Print"><i class="fas fa-print"></i></a>
                    </div>';
            return $btn;
        })->rawColumns(['action'])->make(true);
    }

    public function destroy(Request $request)
    {
        $gudangBarang = DetailBarangKeluar::where('pindah_gudang_id',$request->id)->get();
        foreach ($gudangBarang as $key => $barang) {
            $update[] = GudangBarang::where('id', $barang->gudang_barang_id)->update(['status' => 1 ]);
        }
        $delete = PindahGudang::destroy($request->id);
        DetailBarangKeluar::where('pindah_gudang_id',$request->id)->delete();
        return response()->json($delete);
    }

    public function show(Request $request, $id)
    {
        $data = PindahGudang::with(['toko', 'toko_to', 'data_detail_barang', 'data_detail_serial_number', 'detail_barang_keluar.gudang_barang.barang'])->find($id);
        return response()->json($data);
    }

    public function destroy_detail(Request $request, $id)
    {
        $gudangBarang = DetailBarangKeluar::where('id',$id)->first();
        GudangBarang::where('id', $gudangBarang->gudang_barang_id)->update(['status' => 1 ]);
        $delete = DetailBarangKeluar::destroy($id);
        return response()->json($delete);
    }

    public function update(Request $request, $id)
    {
        $update = PindahGudang::where('id', $id)->update([
            'date' => date('Y-m-d', strtotime($request->date)),
            'no_ref' => $request->ref_number,
            'from' => $request->nama_toko_from,
            'to' => $request->nama_toko_to,
            'status' => 1,
        ]);
        $data_barang_edit = array_chunk($request->databarangedit,7);
        $dataBarangBef = DetailBarangKeluar::where('pindah_gudang_id', $id)->get();
        foreach ($dataBarangBef as $key => $barang) {
            GudangBarang::where('id', $barang->gudang_barang_id)->update(['status' => 1 ]);
        }
        foreach ($data_barang_edit as $key => $barang_edit) {
            $create_edit[] = DetailBarangKeluar::where('id', $barang_edit[0])->update([
                'pindah_gudang_id' => $id,
                'barang_id' => $barang_edit[1],
                'gudang_barang_id' => $barang_edit[5],
                'status' => 1,
                'keterangan' => $barang_edit[6],
            ]);    
            GudangBarang::where('id', $barang_edit[5])->update(['status' => 3 ]);
        }

        return response()->json($update);
    }

    public function terima(Request $request)
    {
        $data = array(
            'status' => 2
        );
        $terima = PindahGudang::where('id', $request->id)->update($data);
        $barangKeluar = DetailBarangKeluar::where('pindah_gudang_id', $request->id)->update($data);
        $pindahGudang = PindahGudang::find($request->id);
        $dataBarangKeluars = DetailBarangKeluar::where('pindah_gudang_id', $request->id)->get();
        foreach ($dataBarangKeluars as $key => $barang) {
            GudangBarang::where('id', $barang->gudang_barang_id)->update([
                'toko_id' => $pindahGudang->to,
                'status' =>1
            ]);
        }

        return response()->json($terima);
    }

    public function outDownload($id)
    {
        $pindahGudang = PindahGudang::with(['toko', 'toko_to', 'data_detail_barang', 'data_detail_serial_number', 'detail_barang_keluar', 'barang_pindah'])->where('id',$id)->first();
        for ($i=0; $i <sizeof($pindahGudang->barang_pindah); $i++) { 
            $data_serial_number[$i] = DetailBarangKeluar::with('gudang_barang')->select('gudang_barang_id')->where('barang_id', $pindahGudang->barang_pindah[$i]->pivot->barang_id)->where('pindah_gudang_id', $pindahGudang->barang_pindah[$i]->pivot->pindah_gudang_id)->get()->toArray();
            for ($j=0; $j <sizeof($data_serial_number[$i]) ; $j++) { 
                $data_sn[$i][$j] = $data_serial_number[$i][$j]['gudang_barang']['serial_number'];
            }
            $pindahGudang->barang_pindah[$i]->pivot['serial_number'] = implode(", ", array_filter($data_sn[$i]));
        }
        $pindahGudang['nama_pengirim'] = Auth::user()->name;
        $pdf = PDF::loadView('pindah-toko.barang-keluar.print', $pindahGudang);
        return $pdf->stream();
    }

    public function inDownload($id)
    {
        $pindahGudang = PindahGudang::with(['toko', 'toko_to', 'data_detail_barang', 'data_detail_serial_number', 'detail_barang_keluar', 'barang_pindah'])->where('id',$id)->first();

        for ($i=0; $i <sizeof($pindahGudang->barang_pindah); $i++) { 
            $data_serial_number[$i] = DetailBarangKeluar::with('gudang_barang')->select('gudang_barang_id')->where('barang_id', $pindahGudang->barang_pindah[$i]->pivot->barang_id)->where('pindah_gudang_id', $pindahGudang->barang_pindah[$i]->pivot->pindah_gudang_id)->get()->toArray();
            for ($j=0; $j <sizeof($data_serial_number[$i]) ; $j++) { 
                $data_sn[$i][$j] = $data_serial_number[$i][$j]['gudang_barang']['serial_number'];
            }
            $pindahGudang->barang_pindah[$i]->pivot['serial_number'] = implode(", ", array_filter($data_sn[$i]));
        }
        $pindahGudang['nama_pengirim'] = Auth::user()->name;
        $pdf = PDF::loadView('pindah-toko.barang-keluar.print', $pindahGudang);
        return $pdf->stream();
    }
}
