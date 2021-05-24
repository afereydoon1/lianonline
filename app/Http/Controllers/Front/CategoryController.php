<?php

namespace App\Http\Controllers\Front;

use App\Models\Assortment;
use App\Models\Category;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function getCategories(Request $request) {
        $type = $request->has('type') ? $request->get('type') : 'product';
        Assortment::whereNull('parent_id')->update(['parent_id'=>0]);
        Category::whereNull('parent_id')->update(['parent_id'=>0]);
        $id = $request->has('id') ? $request->get('id') : 0;
        $result = [];

        if ($type === 'post') {
            $result = Assortment::oldest('order')
                ->with('children')
                ->when($id != 0, function($query) use($id) {
                    $query->where('id', $id);
                })
                ->when($id == 0, function($query) use($id) {
                    $query->where('parent_id',$id);
                })
                ->orderBy('order','asc')
                ->get();
        } else {
            $result = Category::oldest('order')
                ->with('children')
                ->when($id != 0, function($query) use($id) {
                    $query->where('id', $id);
                })
                ->when($id == 0, function($query) use($id) {
                    $query->where('parent_id',$id);
                })
                ->orderBy('order','asc')
                ->get();
        }

        return JsonResponse::send($result, trans('messages.records.getSuccess'));
    }
}
