<?php

namespace App\Http\Controllers;

use App\Models\SerialNumber;
use App\Models\GudangBarang;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class SerialNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Serial Number", $menu)){
            return view('dashboard');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SerialNumber  $serialNumber
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id_toko, $id_barang)
    {
        $user = Auth::user();
        $search = $request->term;
        $query = GudangBarang::where('barang_id', $id_barang)->where('status', 1)->select('id', 'serial_number as text');

        $query->where('toko_id', $id_toko);

        if($search != ''){
            $query->where('serial_number', 'LIKE', "%$search%");
        }
        $data =  $query->get();
        $results = array(
            "results" => $data,
        );
        return response()->json($results);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SerialNumber  $serialNumber
     * @return \Illuminate\Http\Response
     */
    public function edit(SerialNumber $serialNumber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SerialNumber  $serialNumber
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SerialNumber $serialNumber)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SerialNumber  $serialNumber
     * @return \Illuminate\Http\Response
     */
    public function destroy(SerialNumber $serialNumber)
    {
        //
    }
}
