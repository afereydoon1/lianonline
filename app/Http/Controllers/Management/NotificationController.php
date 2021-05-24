<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function lists() {
        Notification::where('read',true)->where('created_at','<',Carbon::now()->subMonth()->format('Y-m-d H:i:s'))->delete();
        $records = Notification::orderBy('id','desc')->with('user','connect')->paginate(100);
        $count = Notification::where('read',false)->with('user','connect')->count();
        return JsonResponse::send([
            'records' => $records,
            'unReadNotification' => $count
        ], trans('messages.records.getSuccess'));
    }
    public function Show($id) {
        $record = Notification::where('id',$id)->with('user','connect')->first();
        if($record) {
            return JsonResponse::send([
                'record' => $record
            ], trans('messages.records.getSuccess'));
        }
        return JsonResponse::send([], trans('messages.records.editFailed'),400);
    }
    public function countUnread()
    {
        $count = Notification::where('read',false)->with('user','connect')->count();
        return JsonResponse::send([
            'unReadNotification' => $count
        ], trans('messages.records.getSuccess'));
    }
    public function read($id) {
        $record = Notification::where('id',$id)->with('user','connect')->first();
        if($record) {
            $record->read = true;
            $record->save();
            $count = Notification::where('read',false)->with('user','connect')->count();
            return JsonResponse::send([
            'unReadNotification' => $count
            ], trans('messages.records.getSuccess'));
        }
        return JsonResponse::send([], trans('messages.records.editFailed'),400);
    }


}
