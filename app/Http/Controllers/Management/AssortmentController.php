<?php

namespace App\Http\Controllers\Management;

use App\Http\Requests\CreateAssortmentRequest;
use App\Http\Requests\EditAssortmentRequest;
use App\Models\Assortment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Response\JsonResponse;

class AssortmentController extends Controller
{
    public function index() {
        $records = Assortment::with('parent')->orderBy('parent_id')->orderBy('order')->paginate(50);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function create(CreateAssortmentRequest $request) {
        if(!$request->has('parent_id')) {
            $request->merge(['parent_id'=>0]);
        }
        $assortment = Assortment::create($request->all());

        if ($assortment) {
            return JsonResponse::send([], trans('messages.records.addSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.addFailed'), -1);
    }

    public function edit(EditAssortmentRequest $request, $id) {
        if(!$request->has('parent_id')) {
            $request->merge(['parent_id'=>0]);
        }
        $assortment = Assortment::findOrFail($id);

        if ($assortment->update($request->all())) {
            return JsonResponse::send([], trans('messages.records.editSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function show($id) {
        $record = Assortment::findOrFail($id);

        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }

    public function delete($id) {
        $assortment = Assortment::findOrFail($id);

        if ($assortment->delete()) {
            return JsonResponse::send([], trans('messages.records.deleteSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function getMainAssortments($id = 0) {
        // $records =  Assortment::when($id, function($query) use ($id) {
        //         $query->where('id', '<>', $id);
        //     })
        //     ->orderBy('order')->pluck('id', 'title')->toArray();
        Assortment::whereNull('parent_id')->update(['parent_id'=>0]);
        $records =  Assortment::where('parent_id',$id)
            ->with('children')
            ->orderBy('order')->get()->toArray();
        $records = $this->prepareAssortmentList($records);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }
    private function prepareAssortmentList($records) {
        $categories = [];
        foreach ($records as $key => $record) {
            if (empty($categories[$key])) {
                $categories[$key] = [];
            }

            $categories[$key]['label'] = $record['title'];
            $categories[$key]['id'] = $record['id'];
            if ($records[$key]['children']) {
                $categories[$key]['children'] = $this->prepareAssortmentList($records[$key]['children']);
            }
        }

        return $categories;
    }
}
