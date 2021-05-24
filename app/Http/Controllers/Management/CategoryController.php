<?php

namespace App\Http\Controllers\Management;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\EditCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Response\JsonResponse;

class CategoryController extends Controller
{
    public function index() {
        $records = Category::with('parent')->orderBy('parent_id')->orderBy('order')->paginate(50);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function create(CreateCategoryRequest $request) {
        if(!$request->has('parent_id')) {
            $request->merge(['parent_id'=>0]);
        }
        $category = Category::create($request->all());

        if ($category) {
            return JsonResponse::send([], trans('messages.records.addSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.addFailed'), -1);
    }

    public function edit(EditCategoryRequest $request, $id) {
        if(!$request->has('parent_id')) {
            $request->merge(['parent_id'=>0]);
        }
        $category = Category::findOrFail($id);

        if ($category->update($request->all())) {
            return JsonResponse::send([], trans('messages.records.editSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function show($id) {
        $record = Category::findOrFail($id);

        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }

    public function delete($id) {
        $category = Category::findOrFail($id);

        if ($category->delete()) {
            return JsonResponse::send([], trans('messages.records.deleteSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function getMainCategories($id = 0) {
        // $records =  Category::when($id, function($query) use ($id) {
        //         $query->where('id', '<>', $id);
        //     })
        //     ->orderBy('order')->pluck('id', 'title')->toArray();
        Category::whereNull('parent_id')->update(['parent_id'=>0]);
        $records =  Category::where('parent_id',$id)
            ->with('children')
            ->orderBy('order')->get()->toArray();
        $records = $this->prepareCategorieList($records);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));

    }
    private function prepareCategorieList($records) {
        $categories = [];
        foreach ($records as $key => $record) {
            if (empty($categories[$key])) {
                $categories[$key] = [];
            }

            $categories[$key]['label'] = $record['title'];
            $categories[$key]['id'] = $record['id'];
            if ($records[$key]['children']) {
                $categories[$key]['children'] = $this->prepareCategorieList($records[$key]['children']);
            }
        }

        return $categories;
    }
}
