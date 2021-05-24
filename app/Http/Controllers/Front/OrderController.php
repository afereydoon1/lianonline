<?php

namespace App\Http\Controllers\Front;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\GatewayConfig;
use App\Models\Notification;
use App\Models\ReferralRequest;
use App\Models\SiteInfo;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Zarinpal\Laravel\Facade\Zarinpal;

class OrderController extends Controller
{
    public function getOrderInfo(Request $request) {
        $this->validate($request, [
            'order_id' => 'required'
        ]);

        $user = Auth::guard('user-api')->user();
        $record = Order::where('user_id',$user->id)->where('id',$request->order_id)->with('orderItems','orderItems.product')->first();
        if(!$record) {
            return JsonResponse::send([],trans('messages.records.editFailed'),-1);
        }
        if($record->status == 'ok') {
            $record = Order::where('user_id',$user->id)->where('id',$request->order_id)->with('orderItems','orderItems.product','orderItems.product.file')->first();
        }
        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }

    public function createOrder(Request $request) {
//        $this->validate($request, [
//            'order_id' => 'required'
//        ]);

        $user = Auth::guard('user-api')->user();

        $products = [];

        foreach ($request->get('cart_items') as $item) {
            $products[$item['id']] = Product::with('file')->findOrFail($item['id']);

            if ($products[$item['id']]->type === 'physical') {
                $products[$item['id']]->total = $item['count'];
                if ($item['count'] > $products[$item['id']]->total) {
                    unset($products[$item['id']]);
                }
            }
        }

        if (count($products)) {
            $order = $user->orders()->create($request->all());

            if ($order) {
                $total_price = 0;
                foreach ($products as $product) {
                    $item = new OrderItem();
                    $item->product_id = $product->id;
                    $item->order_id = $order->id;
                    $item->product_title = $product->title;

                    $price = $product->price;

                    if (!empty($product->discounted_price)) {
                        $price = $product->discounted_price;
                    }

                    $total_price += ($price * $product->total);
                    $item->product_price = $price;
                    $item->product_total = $product->total;
                    $item->marketer_percent = $product->marketer_percent;
                    $item->product_weight = $product->weight;
                    $item->type = $product->type;
                    if ( $product->type === 'file') {
                        $item->file_url = 'url';
                    }
                    $item->save();
                }

                $order->total_price = $total_price;
                $order->save();

                $notifyUser = Auth::guard('user-api')->user();
                Notification::create([
                    'text'  => '',
                    'type'  => 'CreateOrder',
                    'user_id'   => $notifyUser->id,
                    'user_type' => $notifyUser->type,
                    'connect_id' => $order->id,
                    'connect_type'=> Order::class,
                ]);

                return JsonResponse::send([
                    'order_id' => $order->id,
                    'payment_url' => route('redirectToBank', ['order_id' => $order->id])
                ], trans('messages.records.addSuccess'));
            }
        }


        return JsonResponse::send([], trans('messages.records.addFailed'), -1);
    }
    public function redirectToBank(Request $request, $order_id) {
        $order = Order::findOrFail($order_id);
        $original_sign = Payment::getSign();
        $sign = md5(intval($order->total_price) . intval($original_sign) . intval($order->id));
        $client = new \nusoap_client('https://www.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
        $client->soap_defencoding = 'UTF-8';
        $result = $client->call('PaymentRequest', [
            [
                'MerchantID'     => GatewayConfig::findOrFail(1)->merchantId,
                'Amount'         => $order->total_price,
                'Description'    => 'فاکتور #'.$order->id,
                'CallbackURL'    => url('orders/verify?h=' . $sign . '&o=' . $order->id),
            ],
        ]);
        if ($result['Status'] == 100) {
            $payment = new Payment();
            $payment->order_id = $order->id;
            $payment->status = 'send_to_bank';
            $payment->sign = $original_sign;
            $payment->total_price = $order->total_price;
            $payment->authority = $result['Authority'];
            $payment->save();

            // $order->status = 'send_to_bank';
            // $order->save();
            return redirect('https://www.zarinpal.com/pg/StartPay/'.$result['Authority']);
        } else {
            // echo'ERR: '.$result['Status'];
            return redirect('http://www.lianOnline.ir/payment-result/'.$order->id );
        }
    }

    public function verify(Request $request) {
        $order = Order::where('id',$request->get('o'))->with('orderItems')->first();
        if ($order) {
            $sign = $request->get('h');
            $payment = $order->payments->last();

            if ($payment) {
                $mySign = md5(intval($order->total_price) . intval($payment->sign) . intval($order->id));
                if ($mySign === $sign) {
                    $client = new \nusoap_client('https://www.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
                    $client->soap_defencoding = 'UTF-8';
                    $result = $client->call('PaymentVerification', [
                        [
                            'MerchantID'     => GatewayConfig::findOrFail(1)->merchantId,
                            'Authority'      => $payment->authority,
                            'Amount'         => $order->total_price,
                        ],
                    ]);

                    if ($result['Status'] == 100 || $result['Status'] == 101) {
                        $order->status = 'ok';
                        $payment->status = 'ok';
                        $payment->response = $result['Status'];
                        $order->save();
                        $payment->save();

                        $sitePercent = SiteInfo::first()->site_percent;
                        $marketerUser = null;
                        $ReferralRequestID = $order->referrer_id;
                        if($ReferralRequestID) {
                            $referralRequest = ReferralRequest::where('id',$ReferralRequestID)->first();
                            if($referralRequest) {
                                $marketerUser = $referralRequest->referralLink->user;
                            }
                        }
                        foreach ($order->orderItems as $item) {
                            $itemNumber = 1;
                            if($item->type == "physical") {
                                $itemNumber = $item->product_total;
                            }
                            $product = $item->product;
                            if($product) {
                                $product->sale = $product->sale + $itemNumber;
                                if($product->type == 'physical') {
                                    $product->total = $product->total - $itemNumber;
                                }
                                $product->save();

                                $productMarketerPercent = $product->marketer_percent;
                                $productDiscountedPrice = $product->discounted_price;
                                $productPrice           = $product->price;
                                if($productDiscountedPrice && $productDiscountedPrice > 0 && $productDiscountedPrice < $productPrice) {
                                    $productPrice = $productDiscountedPrice;
                                }
                                $siteCommission = (int) ($sitePercent * $productPrice * $itemNumber / 100);
                                $mainAdmin = Admin::first();
                                Transaction::create([
                                    'user_id' => $mainAdmin->id ,
                                    'user_type' => 'App\Models\Admin',
                                    'wallet_id' => $mainAdmin->wallet->id,
                                    'type' => 'site',
                                    'amount' => $siteCommission,
                                    'text' => 'پورسانت سایت',
                                    'status' => 'success',
                                    'order_id' => $order->id,
                                    'product_id' => $product->id
                                ]);
                                $mainAdmin->wallet->amount =  $mainAdmin->wallet->amount + $siteCommission;
                                $mainAdmin->wallet->save();
                                $marketerUserCommission = 0;
                                if($productMarketerPercent > 0 && $marketerUser) {
                                    $marketerUserCommission = (int) ($productMarketerPercent * $productPrice * $itemNumber / 100);
                                    Transaction::create([
                                        'user_id' => $marketerUser->id,
                                        'user_type' => 'App\Models\User',
                                        'wallet_id' => $marketerUser->wallet->id,
                                        'type' => 'referral',
                                        'amount' => $marketerUserCommission,
                                        'text' => 'پورسانت بازاریاب',
                                        'status' => 'success',
                                        'order_id' => $order->id,
                                        'product_id' => $product->id
                                    ]);
                                    $marketerUser->wallet->amount =  $marketerUser->wallet->amount + $marketerUserCommission;
                                    $marketerUser->wallet->save();
                                }else{
                                    $marketerUserCommission = 0;
                                }
                                $creatorCommission = $productPrice - ($siteCommission + $marketerUserCommission);

                                    // if($product->creator_type == 'App\Models\Admin') {
                                    // }else{
                                    // }


                                Transaction::create([
                                    'user_id' => $product->creator_id,
                                    'user_type' => $product->creator_type,
                                    'wallet_id' => $product->creator->wallet->id,
                                    'type' => 'sale',
                                    'amount' => $creatorCommission,
                                    'text' => 'پورسانت فروش محصول',
                                    'status' => 'success',
                                    'order_id' => $order->id,
                                    'product_id' => $product->id
                                ]);
                                $product->creator->wallet->amount =  $product->creator->wallet->amount + $creatorCommission;
                                $product->creator->wallet->save();
                            }

                        }
                        $notifyUser = $order->user;
                        Notification::create([
                            'text'  => '',
                            'type'  => 'OrderSuccess',
                            'user_id'   => $notifyUser->id,
                            'user_type' => $notifyUser->type,
                            'connect_id' => $order->id,
                            'connect_type'=> Order::class,
                        ]);
                        // return view('Front.Payment.verify-ok');
                        return redirect('http://www.lianOnline.ir/payment-result/'.$order->id );//.'/payment/' . $payment->id);
                    } elseif ($result['Status'] == -51 ) {
                        $order->status = 'canceled';
                        $payment->status = 'canceled';
                        $payment->response = $result['Status'];
                        $order->save();
                        $payment->save();
                        // return view('Front.Payment.verify-canceled');
                        return redirect('http://www.lianOnline.ir/payment-result/'.$order->id );//.'/payment/' . $payment->id);
                    } else {
                        $order->status = 'failed';
                        $payment->status = 'failed';
                        $payment->response = $result['Status'];
                        $order->save();
                        $payment->save();
                        return redirect('http://www.lianOnline.ir/payment-result/'.$order->id );
                    }
                }
            }
        }

        // return view('Front.Payment.verify-failed');
        return redirect('http://www.lianOnline.ir/payment-result/'.'verify-failed');



    }
}
