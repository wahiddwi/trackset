<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $categories = Category::count();
        $asset = Asset::count();
        $tags = Tag::count();
        $brand = Brand::count();

        $asset_category = DB::table('inv_mstr as inv')
                          ->join('categories as cat', 'cat.id', '=', 'inv.inv_category')
                          ->select('inv_category', 'cat.cat_name', DB::raw('count(*) as total'))
                          ->groupBy('inv_category', 'cat.cat_name')
                          ->get();

        if ($asset_category->count() != 0) {
          # code...
          for ($i=0; $i < $asset_category->count() ; $i++) { 
            # code...
            $categoryName[] = $asset_category[$i]->cat_name;
            $totalAssetCategory[] = $asset_category[$i]->total;
          }
        } else {
          $categoryName[] = null;
          $totalAssetCategory[] = null;
        }

        $asset_tag = DB::table('inv_mstr as inv')
                      ->join('tag_mstr as tag', 'tag.id', '=', 'inv.inv_tag')
                      ->select('inv_tag', 'tag.tag_name', DB::raw('count(*) as total'))
                      ->groupBy('inv_tag', 'tag.tag_name')
                      ->get();

        if ($asset_tag->count() != 0) {
          # code...
          for ($i=0; $i < $asset_tag->count() ; $i++) { 
            # code...
            $tagName[] = $asset_tag[$i]->tag_name;
            $totalAssetTag[] = $asset_tag[$i]->total;
          }
        } else {
          $tagName[] = null;
          $totalAssetTag[] = null;
        }

        $asset_brand = DB::table('inv_mstr as inv')
                            ->join('brand_mstr as brand', 'brand.id', '=', 'inv.inv_merk')
                            ->select('inv_merk', 'brand.brand_name', DB::raw('count(*) as total'))
                            ->groupBy('inv_merk', 'brand.brand_name')
                            ->get();

        if ($asset_brand->count() != 0) {
          # code...
          for ($i=0; $i < $asset_brand->count() ; $i++) { 
            # code...
            $brandName[] = $asset_brand[$i]->brand_name;
            $totalAssetBrand[] = $asset_brand[$i]->total;
          }
        } else {
          $brandName[] = null;
          $totalAssetBrand[] = null;
        }
                              
        return view('dashboard', compact('asset', 'categories', 'tags', 'brand', 'categoryName', 'totalAssetCategory', 'tagName', 'totalAssetTag', 'brandName', 'totalAssetBrand'));
    }
}
