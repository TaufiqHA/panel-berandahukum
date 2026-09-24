<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Signature;
use Yajra\DataTables\Facades\DataTables;

class SignatureController extends Controller
{
    public function index(Request $request)
    {
        return view('master.signature');
    }

    public function show(Request $request)
    {
        $signatures = Signature::all();
        return Datatables::of($signatures)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-icon btn-info btn-edit" data-id="'.$row->id.'"><i class="far fa-edit"></i> Edit</button> 
                        <button class="btn btn-icon btn-danger btn-delete" data-id="'.$row->id.'"><i class="fas fa-trash"></i> Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function get($id)
    {
        $signature = Signature::find($id);
        return response()->json($signature);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'signature' => 'required',
        ]);

        $signature = Signature::updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->name,
                'signature' => $request->signature
            ]
        );

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Tanda tangan berhasil disimpan!']);
        }

        return redirect()->back()->with('success', 'Tanda tangan berhasil disimpan!');
    }

    public function destroy(Request $request)
    {
        $delete = Signature::destroy($request->id);
        return response()->json(['status' => 'success']);
    }
}
