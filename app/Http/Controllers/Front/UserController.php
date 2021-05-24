<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\UploadTrait;
use App\Models\GatewayConfig;
use App\Models\Notification;
use App\Models\OrderItem;
use App\Models\Otp;
use App\Models\ReferralLink;
use App\Models\ReferralRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Services\Notification\Sms;
use App\Services\Response\JsonResponse;
use App\Services\SMS\SmsIr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use UploadTrait;

    public function register(Request $request) {
        $this->validate($request, [
            'mobile' => ['required', 'regex:/^09(\d){9}$/i']
        ]);

        if (env('OTP_PRETEND', false)) {
            return JsonResponse::send([], trans('messages.otp.success'));
        }
		
		Otp::checkAndRemoveMobile($request->get('mobile'));

        if (Otp::whereMobile($request->get('mobile'))->count() >= 5) {
            return JsonResponse::send(['errors' => [
                'mobile' => trans('messages.otp.blocked')
            ]], trans('messages.otp.blocked'), 422, 422);
        }

        $code = Otp::getNewOtp();

        if (Otp::create([
            'mobile' => $request->get('mobile'),
            'code' => $code,
            'expired_at' => Carbon::now()->addMinutes(5)->format('Y-m-d H:i:s')
        ])) {
            $smsSender = new Sms(new SmsIr());
            if ($smsSender->sendOtpCode(
                $code,
                $request->get('mobile'))) {
                return JsonResponse::send([], trans('messages.otp.success'));
            }
        }

        return JsonResponse::send([], trans('messages.otp.error'), 500, 500);
    }

    public function validateOtp(Request $request) {
        $this->validate($request, [
            'mobile' => ['required', 'regex:/^09(\d){9}$/i'],
            'code' => ['required', 'regex:/^(\d){6}$/i'],
        ]);

        if (!env('OTP_PRETEND', false)) {
            if (! Otp::checkAndRemoveOtp($request->get('mobile'), $request->get('code'))) {
                return JsonResponse::send(['errors' => [
                    'code' => trans('messages.otp.wrong')
                ]], trans('messages.otp.wrong'), 422, 422);
            }
        }

        return $this->userRegister($request->get('mobile'));
    }

    public function login(Request $request) {
        $this->validate($request, [
            'mobile' => 'required',
            'password' => 'required',
        ]);

        $mobile = $request->get('mobile');
        $password = $request->get('password');

        if (! $token = Auth::guard('user-api')->attempt([
            'mobile' => $mobile,
            'password' => $password
        ])) {
            return JsonResponse::send([], trans('messages.auth.loginError'), 401, 401);
        }

        $user = Auth::guard('user-api')->user();

        return JsonResponse::send([
            'user_id' => $user->id,
            'token' => $token
        ], trans('messages.auth.loginSuccess'));
    }

    private function userRegister($mobile) {
        $user = User::whereMobile($mobile)->first();
        $new_user = false;
        if (!$user) {
            if ($user = User::create([
                'mobile' => $mobile,
                'mobile_verified_at' => Carbon::now(),
                'password' => bcrypt($mobile)
            ])) {
                $new_user = true;
                Wallet::create(['user_id' => $user->id,'user_type' => 'App\Models\User', 'amount' => 0]);
                ReferralLink::create(['user_id' => $user->id, 'code' => ReferralLink::generateCode()]);
                Notification::create([
                    'text'  => '',
                    'type'  => 'CreateUser',
                    'user_id'   => $user->id,
                    'user_type' => $user->type,
                    'connect_id' => $user->id,
                    'connect_type'=> User::class,
                ]);
            } else {
                return JsonResponse::send([], trans('messages.general_errors.authorization'), 500, 500);
            }
        }

        if (! $token = Auth::guard('user-api')->attempt([
            'mobile' => $mobile,
            'password' => $mobile
        ])) {
            return JsonResponse::send([], trans('messages.auth.loginError'), 401, 401);
        }

        return JsonResponse::send([
            'new_user' => $new_user,
            'user_id' => $user->id,
            'token' => $token
        ], trans('messages.auth.loginSuccess'));
    }

    public function getUserInfo(Request $request) {
        $this->validate($request, [
            'user_id' => 'integer'
        ]);
        $user = User::where('id',$request->user_id)->first();
        if(!$user) {
            return JsonResponse::send([], trans('messages.records.editFailed'),-1, 500);
        }
        $user->makeHidden(
            'postal_code',
            'card_number',
            'sheba',
            'national_card_image',
            'credit_card_image',
            'bank',
            'bank_account_number',
            'address',
            'wallet_amount',
            'wallet',
            'email',
            'email_verified_at',
            'mobile',
            'mobile_verified_at',
            'created_at',
            'updated_at',
            'state',
            'city',
            'type'
                )->toArray();
        return $user;

        // return Auth::guard('user-api')->user();
    }
    public function getMyInfo(Request $request) {
        $user = Auth::guard('user-api')->user();
        if(!$user) {
            return JsonResponse::send([], trans('messages.records.editFailed'),-1, 500);
        }
        $record = User::where('id',$user->id)->with('wallet')->first();
        return $record;
    }

    public function setUserInfo(Request $request) {
        $user = Auth::guard('user-api')->user();

        $this->validate($request, [
            'email' => 'email|unique:users,email,' . $user->id,
            'username' => 'required|unique:users,username,' . $user->id,
            'mobile' => ['regex:/^09(\d){9}$/i', 'unique:users,mobile,' . $user->id],
            // 'national_card_image' => 'image|mimes:jpeg,jpg,png',
            // 'credit_card_image' => 'image|mimes:jpeg,jpg,png',
        ]);

        $files = $this->doUpload($request, 'user-files');

        $params = $request->only([
            'name',
            'username',
            'email',
            'mobile',
            'avatar',
            'state',
            'city',
            'address',
            'bio',
            'bank',
            'card_number',
            'sheba',
            'postal_code',
            'bank_account_number',
            // 'national_card_image',
            // 'credit_card_image',
        ]);

        if (!empty($files['national_card_image']))
        {
            $params['national_card_image'] = $files['national_card_image'];
        }else{
            $params['national_card_image'] = '';
        }

        if (!empty($files['credit_card_image']))
        {
            $params['credit_card_image'] = $files['credit_card_image'];
        }else{
            $params['credit_card_image'] = '';
        }

        if ($user->update($params)) {
            Notification::create([
                'text'  => '',
                'type'  => 'EditUser',
                'user_id'   => $user->id,
                'user_type' => $user->type,
                'connect_id' => $user->id,
                'connect_type'=> User::class,
            ]);
            return JsonResponse::send([], trans('messages.records.editSuccess'));
        } else {
            return JsonResponse::send([], trans('messages.records.editFailed'), 500, 500);
        }
    }

    public function changePassword(Request $request) {
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = Auth::guard('user-api')->user();

        if (! Hash::check($request->get('current_password'), $user->getAuthPassword())) {
            return JsonResponse::send([], trans('messages.general_errors.validation'), 422, 422);
        }

        $user->password = bcrypt($request->get('new_password'));
        $user->save();

        return JsonResponse::send([], trans('messages.records.editSuccess'));
    }

    public function getSalesList(Request $request) {
        $paged = $request->has('paged') && $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') && $request->get('count') < 100 ? $request->get('count') : 100;

        $user = Auth::guard('user-api')->user();
        $products_id = $user->products()->pluck('id')->toArray();
        $sales = OrderItem::with('product')->whereIn('product_id', $products_id)->latest()->paginate($count, ['*'], 'paged', $paged);

        return JsonResponse::send($sales, trans('messages.records.getSuccess'));
    }

    public function getOrdersList(Request $request) {
        $paged = $request->has('paged') && $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') && $request->get('count') < 100 ? $request->get('count') : 100;

        $user = Auth::guard('user-api')->user();

        $orders = $user->orders()->with('orderItems')->latest()->paginate($count, ['*'], 'paged', $paged);

        return JsonResponse::send($orders, trans('messages.records.getSuccess'));
    }

    public function getTransactions(Request $request) {
        $user = Auth::guard('user-api')->user();
        $paged = $request->has('paged') && $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') && $request->get('count') < 100 ? $request->get('count') : 100;

        $wallet = $user->wallet();

        $transactions = $user->transactions()->with('product')->latest()->paginate($count, ['*'], 'paged', $paged);
        $gatewayConfig = GatewayConfig::findOrFail(1);
        $minWithdrawalAmount = $gatewayConfig->min_withdrawal_amount;
        return JsonResponse::send([
            'min_amount' => $minWithdrawalAmount,
            'wallet' => $wallet,
            'transactions' => $transactions
        ], trans('messages.records.getSuccess'));
    }

    public function setWithdrawRequest(Request $request) {
        $user = Auth::guard('user-api')->user();

        $this->validate($request, [
            'amount' => 'required|numeric|min:1000',
        ]);
        $gatewayConfig = GatewayConfig::findOrFail(1);
        $minWithdrawalAmount = $gatewayConfig->min_withdrawal_amount;
        if ($request->get('amount') < $minWithdrawalAmount) {
            return JsonResponse::send(['errors' => [
                'amount' => [trans('validation.min_amount_error', ['price' => $minWithdrawalAmount])]
            ]], trans('messages.general_errors.validation'));
        }
        if (!(
            $user->card_number != null && !empty($user->card_number)
            && $user->bank != null && !empty($user->bank)
            && $user->national_card_image != null && !empty($user->national_card_image)
            && $user->credit_card_image != null && !empty($user->credit_card_image)
            )) {
            return JsonResponse::send(['errors' => [
                'amount' => [trans('validation.no_finance_info_in_profile')]
            ]], trans('messages.general_errors.validation'));
        }
        if ($user->wallet->amount < $request->get('amount')) {
            return JsonResponse::send(['errors' => [
                'amount' => [trans('validation.amount_not_enough')]
            ]], trans('messages.general_errors.validation'));
        }

        $params = [
            'amount' => $request->get('amount'),
            'card' => $user->card_number,
            'bank' => $user->bank,
            'sheba' => $user->sheba,
            'wallet_id' => $user->wallet->id,
        ];

        $req = $user->withdrawals()->create($params);


        if (!$req) {
            return JsonResponse::send([], trans('validation.withdrawal_not_register'));
        }
        $params2 = [
                'wallet_id'     =>  $user->wallet->id,
                'type'          =>  "withdrawal",
                'amount'        =>  $request->get('amount'),
                'status'        =>  'pending',
                'withdrawal_id' =>  $req->id,
                'text'          =>  'در انتظار بررسی و پرداخت',
            ];
        $req2 = $user->transactions()->create($params2);
        if (!$req2) {
            $req->delete();
            return JsonResponse::send([], trans('validation.transaction_not_register'));
        }
        $user->wallet->amount = $user->wallet->amount - $request->get('amount');
        $user->wallet->save();
        Notification::create([
            'text'  => '',
            'type'  => 'CreateWithdrawal',
            'user_id'   => $user->id,
            'user_type' => $user->type,
            'connect_id' => $req->id,
            'connect_type'=> Withdrawal::class,
        ]);
        return JsonResponse::send($req, trans('messages.records.getSuccess'));
    }

    public function getReferralRequests(Request $request) {
        $user = Auth::guard('user-api')->user();
        $paged = $request->has('paged') && $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') && $request->get('count') < 100 ? $request->get('count') : 100;

        $referralLink = $user->referralLinks()->first()->code;
        $referralLinksid = $user->referralLinks()->pluck('id');
        $requests = ReferralRequest::whereIn('referral_link_id', $referralLinksid)->with('order')->latest()->paginate($count, ['*'], 'paged', $paged);
        return JsonResponse::send([
            'referral_code' => $referralLink,
            'referral_requests' => $requests
        ], trans('messages.records.getSuccess'));
    }

    public function getUserStats(Request $request) {
        $this->validate($request, [
            'user_id' => 'integer'
        ]);
        $user = User::where('id',$request->user_id)->first();
        if(!$user) {
            return JsonResponse::send([], trans('messages.records.editFailed'),-1, 500);
        }

        $info = [
            'products' => 0,
            'posts' => 0,
            'tickets' => 0,
            'sales' => 0,
            'purchases' => 0,
            'balance' => 0,
            'referrals' => 0,
            'registered' => 0,
            'comments' => 0,
            'saleAmount' => 0,
            'referralAmount' => 0
        ];

        $info['products'] = $user->products()->where('show_status',true)->count();
        $info['posts'] = $user->posts()->where('show_status',true)->count();
        $info['tickets'] = $user->tickets()->count();
        $info['sales'] = Transaction::where('user_id',$user->id)->where('user_type',User::class)->where('type','sale')->count();
        $info['purchases'] = $user->orders()->count();
        $info['balance'] = $user->wallet->amount;
        $info['referrals'] = Transaction::where('user_id',$user->id)->where('user_type',User::class)->where('type','referral')->count();
        $info['registered'] = $user->created_at;
        $info['comments'] = $user->comments()->where('show_status',true)->count();
        $info['saleAmount'] = Transaction::where('user_id',$user->id)->where('user_type',User::class)->where('type','sale')->get()->sum('amount');
        $info['referralAmount'] = Transaction::where('user_id',$user->id)->where('user_type',User::class)->where('type','referral')->get()->sum('amount');

        return JsonResponse::send($info, trans('messages.records.getSuccess'));
    }
}
