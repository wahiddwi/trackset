<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Module;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
  private $menuId;
  public function __construct() {
    $this->middleware(['permission:update'])->only(['edit', 'update', 'toggleState']);
    $this->middleware(['permission:create'])->only(['create', 'store']);
    $this->middleware(['permission']);
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if ($request->ajax()) {
        $model = Tag::select('id', 'tag_name', 'tag_status', 'updated_at')
                    ->with('asset');
        return DataTables::of($model)->toJson();
      }

      $modules = Module::isSuper()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
      $count = Tag::count();
      $menuId = $request->attributes->get('menuId');

      return view('master.tags.list', compact('modules', 'count', 'menuId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('master.tags.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $request->validate([
        //   'tag_name' => 'required',
        // ]);

        // Tag::create([
        //   'tag_name' => $request->tag_name,
        //   'tag_status' => $request->tag_status ? true : false,
        // ]);

        // $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Tag berhasil ditambahkan!'));

        // return redirect()->route('tag.index');

      $request['tag_name'] = Str::upper($request->tag_name);

      $validator = Validator::make($request->all(), [
        'tag_name' => ['required', 'string', 'unique:tag_mstr']
      ]);

      if ($validator->fails()) {
        $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Tag sudah terdaftar dengan nama tersebut.'));
        return response(back()->withInput()->withErrors($validator));
      }

      $validated = $validator->validated();

      DB::transaction(function () use ($request, $validated) {
        Tag::create([
          'tag_name' => $validated['tag_name'],
          'tag_status' => $request->tag_status? true : false,
        ]);
      });

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Tag berhasil ditambahkan'));
      return redirect()->route('tag.index');

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
      $tag = Tag::find($id);

      return view('master.tags.edit', compact('tag'));
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
      // $request->validate([
      //   'tag_name' => 'required'
      // ]);

      // $tag = Tag::find($id);
      // $tag->tag_name = $request->tag_name;
      // $tag->save();

      // $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Tag berhasil diubah!'));

      // return redirect()->route('tag.index');

      $request['tag_name'] = Str::upper($request->tag_name);

      $validator = Validator::make($request->all(), [
          'tag_name' => ['required', 'string', 'unique:tag_mstr']
      ]);

      if ($validator->fails()) {
        $request->session()->flash('notification', array('type' => 'error', 'title' => 'Error!', 'msg' => 'Tag sudah terdaftar dengan nama tersebut.'));
        return response(back()->withInput()->withErrors($validator));
      }

      $validated = $validator->validated();

      DB::transaction(function () use ($request, $validated, $id) {
        $tag = Tag::find($id);
        $tag->tag_name = $validated['tag_name'];
        $tag->save();
      });

      $request->session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Tag berhasil diubah'));
      return redirect()->route('tag.index');
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

    public function toggleState($id)
    {
      $tag = Tag::find($id);
      $tag->tag_status = !$tag->tag_status;
      $tag->save();

      return array('res' => true);
    }
}
