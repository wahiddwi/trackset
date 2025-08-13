<?php

namespace App\Http\Controllers\Journal;

use App\Models\Module;
use App\Models\JournalLogs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $model = JournalLogs::select('id', 'url', 'response_code', 'response', 'logs', 'updated_at')->get();
            return DataTables::of($model)->toJson();
        }

        $modules = Module::where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $count = JournalLogs::count();
        $menuId = $request->attributes->get('menuId');
        return view('journal.logs.list', compact('modules', 'count', 'menuId'));
    }
}
