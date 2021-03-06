<?php namespace Bantenprov\DaftarRetribusi\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bantenprov\DaftarRetribusi\Facades\DaftarRetribusi;
use Bantenprov\DaftarRetribusi\Models\DaftarRetribusiModel;
use Ramsey\Uuid\Uuid;
use Bantenprov\LaravelOpd\Models\LaravelOpdModel;

/**
 * The DaftarRetribusiController class.
 *
 * @package Bantenprov\DaftarRetribusi
 * @author  bantenporv <developer.bantenprov@gmail.com>
 */
class DaftarRetribusiController extends Controller
{    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $daftar_retribusies = DaftarRetribusiModel::with('getOpd')->get();
        
        
        return view('daftar-retribusi::index', compact('daftar_retribusies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $opds = LaravelOpdModel::where('levelunker', '=', 1)->get();
        
        return view('daftar-retribusi::create', compact('opds'));
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
            'nama'          => 'required',
            'opd_id'        => 'required',
        ]);

        $opd = LaravelOpdModel::find($request->opd_id);

        if(is_null($opd)){
            return redirect()->back()->withErrors('Can\'t find opd.');
        }
        

        $daftar_retribusi = DaftarRetribusiModel::create(
                            [
                                'uuid'          => Uuid::uuid5(Uuid::NAMESPACE_DNS, 'bantenprov.go.id'.date('YmdHis')),
                                'nama'          => $request->nama,
                                'opd_id'        => $request->opd_id,
                                'opd_uuid'      => $opd->uuid,
                                'user_id'       => \Auth::user()->id,
                                'user_update'   => \Auth::user()->id,
                            ]);
        
                            
        return redirect()->route('daftar-retribusi.index')->with('message', 'Success add new data.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $daftar_retribusi = DaftarRetribusiModel::find($id);

        return view('daftar-retribusi::show', compact('daftar_retribusi'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $daftar_retribusi = DaftarRetribusiModel::with('getOpd')->find($id);

        $opds = LaravelOpdModel::where('levelunker', '=', 1)->get();

        return view('daftar-retribusi::edit', compact('daftar_retribusi','opds'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'          => 'required',
            'opd_id'        => 'required',
        ]);

        $opd = LaravelOpdModel::find($request->opd_id);

        if(is_null($opd)){
            return redirect()->back()->withErrors('Can\'t find opd.');
        }
        
        $daftar_retribusi = DaftarRetribusiModel::find($id);

        $daftar_retribusi->nama          = $request->nama;
        $daftar_retribusi->opd_id        = $request->opd_id;
        $daftar_retribusi->opd_uuid      = $opd->uuid;
        $daftar_retribusi->user_update   = \Auth::user()->id;
        $daftar_retribusi->save();
        
        return redirect()->route('daftar-retribusi.index')->with('message', 'Success update data.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $daftar_retribusi = DaftarRetribusiModel::find($id);
        $daftar_retribusi->delete();

        return redirect()->route('daftar-retribusi.index')->with('message', 'Success deleted data.');

    }
}
