<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InsuranceHist;
use Illuminate\Support\Facades\Storage;

class InsuranceController extends Controller
{
  protected $storage_path;
  // public function __construct() {
  //     $this->middleware(['permission']);
  //     $this->middleware('permission:create')->only(['create', 'store']);
  // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store($data)
    {
        // $this->storage_path = $data['filepath'];
        
        $insurance = InsuranceHist::create([
          'inshist_asset' => $data['inshist_asset'],
          'inshist_vendor' => $data['inshist_vendor'],
          'inshist_vehicle' => $data['inshist_vehicle'],
          'inshist_polishno' => $data['inshist_polishno'],
          'inshist_startdate' => $data['inshist_startdate'],
          'inshist_enddate' => $data['inshist_enddate'],
          'inshist_cover' => $data['inshist_cover'],
          'inshist_premi' => $data['inshist_premi'],
        ]);

        $storagePath = $data['filepath'] . $insurance->id;
        if (isset($data['fileInsurance'])) {
          foreach ($data['fileInsurance'] as $file) {
              Storage::putFile($storagePath, $file);
          }
        }

        return;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function file_upload(Request $request)
    {
      $path = Storage::putFile($this->storage_path . $request->match_id, $request->file('fileUpload'));
      $url = asset('storage/' . $path);
      $config = [
        'key' => $path,
        'size' => Storage::size($path),
        'downloadUrl' => $url,
      ];
      if ($request->file('fileUpload')->extension() == 'pdf') {
        $config['type'] = 'pdf';
      }

      $out = [
        'initialPreview' => [$url],
        'initialPreviewConfig' => [$config],
        'initialPreviewAsData' => true
      ];
      return $out;
    }

    public function file_delete(Request $request)
    {
      Storage::delete($request->key);
      return [];
    }
}
