<?php

namespace App\Http\Controllers\Management;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Response\JsonResponse;

class TagController extends Controller
{
    public function index() {
        $records = Tag::latest()->paginate(50);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function create(Request $request) {
        $tag = Tag::Create($request->only('name'));
        if ($tag) {
            return JsonResponse::send([], trans('messages.records.addSuccess'));
        }
        return JsonResponse::send([], trans('messages.records.addFailed'), -1);
    }

    public function edit(Request $request, $id) {
        $tag = Tag::findOrFail($id);

        if ($tag->update($request->only('name'))) {
            return JsonResponse::send([], trans('messages.records.editSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function show($id) {
        $record = Tag::findOrFail($id);

        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }

    public function delete($id) {
        $tag = Tag::findOrFail($id);

        if ($tag->delete()) {
            return JsonResponse::send([], trans('messages.records.deleteSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }
}
