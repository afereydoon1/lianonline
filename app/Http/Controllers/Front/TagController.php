<?php

namespace App\Http\Controllers\Front;

use App\Models\Tag;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function getTags(Request $request) {
        $type = $request->has('type') ? $request->get('type') : 'post';
        $id = $request->has('id') ? $request->get('id') : null;
        $result = [];

        if ($type === 'post') {
            $result = Tag::when($id, function($query) use($id) {
                    $query->where('id', $id);
                })
                ->get();
        } else {
            $result = Tag::when($id, function($query) use($id) {
                    $query->where('id', $id);
                })
                ->get();
        }

        return JsonResponse::send($result, trans('messages.records.getSuccess'));
    }
}
