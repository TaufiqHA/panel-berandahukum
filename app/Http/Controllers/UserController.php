<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = explode(',', auth()->user()->user_menu);
        if(auth()->user()->status == 1 || in_array("User", $menu) ){
            if (auth()->user()->status_admin != 1) {
                return view('master.user');
            }else{
                return abort(403);
            }
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
        if ($request->status == 3) {
            $create = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => bcrypt($request->password),
                "status" => 1,
                "status_admin" => 1,
                "toko_id" => $request->toko,
                "user_menu" => implode(',',$request->menu),
            ]);
        }else{
            $create = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => bcrypt($request->password),
                "status" => $request->status,
                "toko_id" => $request->toko,
                "user_menu" => implode(',',$request->menu),
            ]);
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
        $users = User::with('toko')->get();
        $data = [];
        foreach ($users as $key => $user) {
            $data[$key] = $user;
            if($user->status == 1){
                if ($user->status_admin == 1) {
                    $data[$key]['status_name'] = 'Admin Pusat';
                }else{
                    $data[$key]['status_name'] = 'Superuser';
                }
               
            }else{
                $data[$key]['status_name'] = 'Admin';
            }
            if($user->toko == null){
                $data[$key]['nama_toko'] = '';
            }else{
                $data[$key]['nama_toko'] = $user->toko->nama_toko;
            }

        }
        return Datatables::of($data)->addIndexColumn()->addColumn('action', function ($row) {
            $btn = '<div class="button"><button class="btn btn-icon btn-info btn-edit" data-id="'.$row->id.'"><i class="far fa-edit"></i></button> <button data-id="'.$row->id.'" href="#" class="btn btn-icon btn-danger btn-delete"><i class="far fas fa-trash"></i></button></div>';
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
        $edit = User::with('toko')->find($id);
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
        $data = array(
            'name' =>$request->edit_name,
            'email' =>$request->edit_email,
            'status' =>$request->edit_status,
            'toko_id' =>$request->edit_toko,
            "user_menu" => implode(',',$request->edit_menu),
        );
        if($request->edit_password != ''){
            $data['password'] = bcrypt($request->edit_password);
        }

        $update = User::where('id',$request->id)->update($data);
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
        $delete = User::destroy($request->id);
        return response()->json($delete);
    }

    public function select(Request $request)
    {
        $search = $request->term;
        $data = User::select("id", "name as text")
            ->where('name', 'LIKE', "%$search%")
            ->get();
        $results = array(
            "results" => $data,
        );
        return response()->json($results);
    }
}
