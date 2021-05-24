<?php

namespace App\Http\Controllers\Management;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Zarinpal\Laravel\Facade\Zarinpal;

class OrderController extends Controller
{
    public function lists() {
        $records = Order::orderBy('id','desc')->with('user')->with('payments')->with('orderItems')->paginate(50);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function failedLists() {
        $records = Order::orderBy('id','desc')->with('user')->with('payments')->with('orderItems')->where('orders.status', '<>', 'ok')->paginate(50);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function show($id) {
        $record = Order::with('user')->with('payments')->with('orderItems')->findOrFail($id);

        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }

    public function edit(Request $request) {
        $this->validate($request, [
            'id' => 'required',
            'status' => 'required',
        ]);
        $record = Order::with('user')->with('payments')->with('orderItems')->findOrFail($request->id);
        if(!$record) {
            return JsonResponse::send([],trans('messages.records.editFailed'),-1);
        }
        $record->status = $request->status;
        $record->update($request->all());
        if($request->has('order_items')) {
            $idsFisrt = $record->orderItems->pluck('id')->toArray();
            $idsSecound = [];
            foreach ($request->get('order_items') as $key => $order_item) {
                $orderItem = OrderItem::updateOrCreate([
                    'product_id'=>$order_item['product_id'],
                    'order_id'=>$order_item['order_id'],
                ],[
                    'product_title'=>$order_item['product_title'],
                    'product_price'=>$order_item['product_price'],
                    'product_total'=>$order_item['product_total'],
                    'product_weight'=>$order_item['product_weight'],
                    'type'=>$order_item['type'],
                    'marketer_percent'=>$order_item['marketer_percent'],
                ]);
                $idsSecound[] = $orderItem->id;
            }
            // return 'true';
            // return compact('idsFisrt','idsSecound');
            OrderItem::whereIn('id',$idsFisrt)->whereNotIn('id',$idsSecound)->delete();
        }
        return JsonResponse::send([
        ], trans('messages.records.getSuccess'));
    }

    public function delete(Request $request,$id) {
        $record = Order::findOrFail($id);
        if(!$record) {
            return JsonResponse::send([],trans('messages.records.deleteFailed'),-1);
        }
        $record->delete();
        return JsonResponse::send([
        ], trans('messages.records.getSuccess'));
    }

    public function searchProducts(Request $request)
    {
        $search_id = ($request->has('id') && $request->get('id') != null && !empty($request->get('id'))) ? $request->get('id') : null;
        $search_title = ($request->has('title') && $request->get('title') != null && !empty($request->get('title'))) ? $request->get('title') : null;
        $records = Product::orderBy('id','desc')
        ->when($search_id,function($query) use($search_id) {
            $query->where('id', $search_id);
        })
        ->when($search_title,function($query) use($search_title) {
            $query->where('title','like','%'.$search_title.'%');
        })
        ->limit(10)->get();
        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

}
