<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GlobalController extends Controller
{
    public function changeSite($site){
        $user = Auth::user();

        if(SiteUser::where('su_user', $user->usr_id)->where('su_site', $site)->exists()){
            $siteData = Site::find($site);
            session(['selected_site' => $siteData]);
            session()->flash('notification', array('type' => 'success', 'title' => 'Berhasil', 'msg' => 'Outlet berhasil berubah ke ' . $siteData->si_name . ' (' . $siteData->si_site . ')'));
            return back();
        }
        else{
            session()->flash('notification', array('type' => 'error', 'title' => 'Error', 'msg' => 'Anda tidak memiliki akses untuk outlet ini'));
            return back();
        }

    }
}
