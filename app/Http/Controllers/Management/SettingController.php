<?php

namespace App\Http\Controllers\Management;

use App\Models\GatewayConfig;
use App\Models\SiteInfo;
use App\Models\SmsConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\General\UploadTrait;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\SliderImage;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Response\JsonResponse;
use Carbon\Carbon;

class SettingController extends Controller
{
    use UploadTrait;

    public function getGeneralSetting() {
        $info = SiteInfo::findOrFail(1);

        return JsonResponse::send(['record' => $info],  trans('messages.records.getSuccess'));
    }

    public function setGeneralSetting(Request $request) {
        $info = SiteInfo::findOrFail(1);

        $licenses = [];

        if (!empty($request->get('licenses'))) {
            foreach ($request->get('licenses') as $license) {
                $licenses[$license['key']] = $license['value'];
            }
        }

        $params = $request->only([
            'site_name',
            'site_description',
            'logo_url',
            'favicon_url',
            'gateway',
            'about_us',
            'contact_us',
            'terms_and_conditions',
            'site_percent'
        ]);

        $params['licenses'] = $licenses;

        $info->update($params);

        return JsonResponse::send(['record' => $info],  trans('messages.records.getSuccess'));
    }

    public function getSmsSetting() {
        $info = SmsConfig::findOrFail(1);

        return JsonResponse::send(['record' => $info],  trans('messages.records.getSuccess'));
    }

    public function setSmsSetting(Request $request) {
        $info = SmsConfig::findOrFail(1);

        $params = $request->only([
            'api_key',
            'api_secret',
            'api_number',
            'api_url'
        ]);

        $info->update($params);

        return JsonResponse::send(['record' => $info],  trans('messages.records.getSuccess'));
    }

    public function getGatewaySetting() {
        $info = GatewayConfig::findOrFail(1);

        return JsonResponse::send(['record' => $info],  trans('messages.records.getSuccess'));
    }

    public function setGatewaySetting(Request $request) {
        $info = GatewayConfig::findOrFail(1);

        $params = $request->only([
            'merchantId',
            'gateway_key',
            'gateway_name',
            'gateway_base_url',
            'min_withdrawal_amount',
        ]);

        $info->update($params);

        return JsonResponse::send(['record' => $info],  trans('messages.records.getSuccess'));
    }

    public function slidesList() {
        $slide = SliderImage::get();

        return JsonResponse::send(['slide' => $slide],  trans('messages.records.getSuccess'));
    }

    public function setSlidesList(Request $request)
    {
        $images = SliderImage::get();
        if ($request->has('images')) {

            $should_be_deleted = [];

            foreach ($images as $image) {
                if (!in_array($image->id, $request->get('images'))) {
                    $should_be_deleted[] = $image->id;
                }
            }
            SliderImage::whereIn('id', $should_be_deleted)->delete();
            return JsonResponse::send([],  trans('messages.records.getSuccess'));
        }
    }

    public function deleteImageSlider($id)
    {
        $image = SliderImage::where('id',$id)->first();
        if($image) {
            $image->delete();
            return JsonResponse::send([],  trans('messages.records.deleteSuccess'));
        }else{
            return JsonResponse::send([],  trans('messages.records.deleteFailed'));
        }
    }

    public function slidesUpload(Request $request) {
        return $this->doUpload($request,'slider_image');
    }

    public function getDashbordInfo() {
        $ret = [];
        for($i=29;$i>-1;$i--) {
            $siteCommission = Transaction::where('type','site')
                ->where('status','success')
                ->whereBetween('created_at',[
                    Carbon::today()->subDays($i)->toDateString(),
                    Carbon::today()->subDays($i-1)->toDateString()
                ])
                ->get()
                ->sum('amount');
            $salePrice = Order::where('status','ok')
                ->whereBetween('updated_at',[
                    Carbon::today()->subDays($i)->toDateString(),
                    Carbon::today()->subDays($i-1)->toDateString()
                ])
                ->get()
                ->sum('total_price');
            $ret[] = [
                'time' => Carbon::today()->subDays($i)->toDateString(),
                'sale_price' => $salePrice,
                'site_commission' =>  $siteCommission,
            ];
        }
        return JsonResponse::send(['records' => [
            'chart_info'        => $ret,
            'total_products'    => Product::count(),
            'total_posts'       => Post::count(),
            'total_orders'      => Order::count(),
            'success_orders'    => Order::where('status','ok')->count(),
            'total_tickets'     => Ticket::count(),
            'total_comments'    => Comment::count(),
            'total_users'       => User::count(),
        ]],  trans('messages.records.getSuccess'));
    }
}
