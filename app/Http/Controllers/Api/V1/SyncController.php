<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\SyncCompanyRequest;
use App\Http\Requests\V1\SyncSiteRequest;
use App\Models\Company;
use App\Models\Site;

class SyncController extends Controller
{
    public function syncSite(SyncSiteRequest $request)
    {
        $bulk = collect($request->all());

        $count = Site::upsert($bulk->toArray(), ['si_site'], ['si_name', 'si_company', 'si_company_site', 'si_active']);

        if($count){
            return array("message" => "Success");
        }
        else return array("message" => "Error");
    }

    public function syncCompany(SyncCompanyRequest $request)
    {
        $bulk = collect($request->all());

        $count = Company::upsert($bulk->toArray(), ['co_company'], ['co_name', 'co_active']);

        if($count){
            return array("message" => "Success");
        }
        else return array("message" => "Error");
    }
}
