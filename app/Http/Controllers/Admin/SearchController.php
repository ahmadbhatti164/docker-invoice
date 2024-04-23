<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GlobalSearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        if($request->ajax())
        {
            $value = $request['search'];
            $searchResult = GlobalSearchService::make()->search($value,3);
            if(!$searchResult->isEmpty()){
                foreach ($searchResult as $data)
                    if($data['type'] == 'invoice')
                        echo '<p><a title="See Detail" class="customA" href='.route("invoiceDetail", $data['id']).'>'.$data['name'].' <span class="searchNameSpan"><i class="fas fa-circle searchDot"></i> Invoice <i class="fas fa-circle searchDot"></i> '.$data['vendor'].'</span></a></p>';
                    elseif($data['type'] == 'vendor')
                        echo '<p><a title="See Detail" class="customA" href='.route("vendorDetail", $data['id']).'>'.$data['name'].' <span class="searchNameSpan"><i class="fas fa-circle searchDot"></i> Vendor </span></a></p>';
                    elseif($data['type'] == 'product')
                        echo '<p><a title="See Detail" class="customA" href='.route("productDetail", $data['id']).'>'.$data['name'].' <span class="searchNameSpan"><i class="fas fa-circle searchDot"></i> Product <i class="fas fa-circle searchDot"></i> '.$data['vendor'].' </span></a></p>';
            }
                echo '<p><a class="customA" href='.route("searchDetail",$value).'><button class="d-flex justify-content-center btn btn-light see-all-results" style="margin: 0 auto;background-color: #eeeeee;" data-value="'.$value.'">See All Results</button> </a></p>';
        }
    }
    public function searchDetail(Request $request)
    {
        $value = $request->value;
        $searchResult = GlobalSearchService::make()->search($value);

        return view('admin.search.list',compact('searchResult'));
    }
}
