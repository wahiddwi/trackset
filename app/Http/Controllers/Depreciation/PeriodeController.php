<?php

namespace App\Http\Controllers\Depreciation;

use App\Models\Module;
use App\Models\Periode;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class PeriodeController extends Controller
{
    public function __construct() {
        $this->middleware(['permission']);
        $this->middleware(['permission:create'])->only(['create', 'store']);
        $this->middleware(['permission:update'])->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $model = Periode::select('id', 'per_journal', 'journalno', 'updated_at')->get();
            return DataTables::of($model)->toJson();
        }

        $modules = Module::where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = Periode::count();
        $menuId = $request->attributes->get('menuId');

        return view('journal.periode.index', compact('modules', 'count', 'menuId'));
    }

    public function store($data)
    {
        Periode::create([
            'per_journal' => $data['per_journal'],
            'journalno' => $data['journalno']
        ]);

        return;
    }
}
