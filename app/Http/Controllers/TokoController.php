<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Toko;
use DataTables;
use Storage;

class TokoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("Toko", $menu)){
            return view('master.toko');
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
        if (auth()->user()->status != 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $image = $request->image; // image base64 encoded
        if ($image) {
            $imageData = preg_replace('/data:image\/(.*?);base64,/','',$image);
            $decodedImage = base64_decode($imageData);
            
            // Basic size check (2MB)
            if (strlen($decodedImage) > 2048 * 1024) {
                return response()->json(['error' => 'Image too large (max 2MB)'], 422);
            }

            // Verify it is actually an image
            if (!getimagesizefromstring($decodedImage)) {
                return response()->json(['error' => 'Invalid image format'], 422);
            }

            $imageName = 'image_' . time() . '.' . 'png';
            Storage::disk('public')->put($imageName, $decodedImage);
        } else {
            $imageName = null;
        }

        $create = Toko::create([
            'nama_toko' => $request->nama_toko,
            'alamat_toko' => $request->alamat_toko,
            'image' => $imageName
        ]);

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
        return Datatables::of(Toko::all())->addIndexColumn()->addColumn('action', function ($row) {
            $btn = '<div class="button"><button class="btn btn-icon btn-info btn-edit" data-id="'.$row->id.'" data-name="'.$row->nama_toko.'" data-alamat="'.$row->alamat_toko.'" data-image="'.$row->image.'"><i class="far fa-edit"></i></button> <button data-id="'.$row->id.'" href="#" class="btn btn-icon btn-danger btn-delete"><i class="far fas fa-trash"></i></button></div>';
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
        //
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
        if (auth()->user()->status != 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $data = array(
            'nama_toko' => $request->edit_nama_toko,
            'alamat_toko' => $request->edit_alamat_toko,
        );
        if($request->image){
            $image = $request->image;
            $imageData = preg_replace('/data:image\/(.*?);base64,/','',$image);
            $decodedImage = base64_decode($imageData);

            // Basic size check (2MB)
            if (strlen($decodedImage) > 2048 * 1024) {
                return response()->json(['error' => 'Image too large (max 2MB)'], 422);
            }

            // Verify it is actually an image
            if (!getimagesizefromstring($decodedImage)) {
                return response()->json(['error' => 'Invalid image format'], 422);
            }

            $imageName = 'image_' . time() . '.' . 'png';
            Storage::disk('public')->put($imageName, $decodedImage);
            $data['image'] = $imageName;
        }
        $update = Toko::where('id',$request->id)->update($data);
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
        if (auth()->user()->status != 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $delete = Toko::destroy($request->id);
        return response()->json($delete);
    }

    public function select(Request $request)
    {
        $search = $request->term;
        $data = Toko::select("id", "nama_toko as text")
            ->where('nama_toko', 'LIKE', "%$search%")
            ->get();
        $results = array(
            "results" => $data,
        );
        return response()->json($results);
    }

    public function except(Request $request, $id)
    {
        $search = $request->term;
        $data = Toko::select("id", "nama_toko as text")
            ->where('nama_toko', 'LIKE', "%$search%")
            ->where('id', '!=', $id)
            ->get();
        $results = array(
            "results" => $data,
        );
        return response()->json($results);
    }
}
