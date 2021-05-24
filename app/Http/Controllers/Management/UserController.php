<?php

namespace App\Http\Controllers\Management;

use App\Models\ReferralLink;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\General\UploadTrait;
use App\Services\Response\JsonResponse;
use App\Http\Requests\Management\EditUserRequest;
use App\Http\Requests\Management\CreateUserRequest;
use App\Models\Admin;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use UploadTrait;
    public function index() {
        $records = User::orderBy('id','desc')->with('wallet')->paginate(50);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }
    public function indexAdmin() {
        $records = Admin::orderBy('id','desc')->with('wallet')->paginate(50);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }


    public function create(CreateUserRequest $request) {
        $user = User::Create($request->only([
            'name',
            'username',
            'email',
            'mobile',
            'state',
            'city',
            'address',
            'bio',
            'bank',
            'card_number',
            'sheba',
            'postal_code',
            'status',
            'bank_account_number',
            'national_card_image',
            'credit_card_image',
            'avatar',
        ]));

        if ($user) {
            $user->mobile_verified_at = Carbon::now();
            $user->email_verified_at = Carbon::now();
            $user->password = bcrypt($request->get('password'));

            $user->save();

            Wallet::create(['user_id' => $user->id,'user_type' => 'App\Models\User', 'amount' => 0]);
            ReferralLink::create(['user_id' => $user->id, 'code' => ReferralLink::generateCode()]);
            $notifyUser = Auth::guard('admin-api')->user();
            Notification::create([
                'text'  => '',
                'type'  => 'CreateUser',
                'user_id'   => $notifyUser->id,
                'user_type' => $notifyUser->type,
                'connect_id' => $user->id,
                'connect_type'=> User::class,
            ]);
            return JsonResponse::send([], trans('messages.records.addSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.addFailed'), -1);
    }

    public function edit(EditUserRequest $request, $id) {
        $user = User::findOrFail($id);

        $params = $request->only([
            'name',
            'username',
            'email',
            'mobile',
            'state',
            'city',
            'address',
            'bio',
            'bank',
            'card_number',
            'sheba',
            'postal_code',
            'status',
            'bank_account_number',
            'national_card_image',
            'credit_card_image',
            'avatar',
        ]);

        if (!empty($request->get('password'))) {
            $params['password'] = bcrypt($request->get('password'));
        }

        if ($user->update($params)) {
            $notifyUser = Auth::guard('admin-api')->user();
            Notification::create([
                'text'  => '',
                'type'  => 'EditUser',
                'user_id'   => $notifyUser->id,
                'user_type' => $notifyUser->type,
                'connect_id' => $user->id,
                'connect_type'=> User::class,
            ]);
            return JsonResponse::send([], trans('messages.records.editSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function editAdmin(Request $request, $id) {
        $user = Admin::findOrFail($id);
        if(!$user) {
            return JsonResponse::send([], trans('messages.records.editFailed'), -1);
        }
        $params = $request->only([
            'name',
            'username',
            'email',
            'mobile',
            'status',
        ]);

        if($request->has('password') && !empty($request->get('password'))) {
            $params['password'] = bcrypt($request->get('password'));
        }

        if ($user->update($params)) {
            $notifyUser = Auth::guard('admin-api')->user();
            // Notification::create([
            //     'text'  => '',
            //     'type'  => 'EditUser',
            //     'user_id'   => $notifyUser->id,
            //     'user_type' => $notifyUser->type,
            //     'connect_id' => $user->id,
            //     'connect_type'=> Admin::class,
            // ]);
            return JsonResponse::send([], trans('messages.records.editSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function show($id) {
        $record = User::findOrFail($id);

        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }
    public function showAdmin($id) {
        $record = Admin::findOrFail($id);
        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }

    public function delete($id) {
        $user = User::findOrFail($id);

        if ($user->delete()) {
            return JsonResponse::send([], trans('messages.records.deleteSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }
    public function deleteAdmin($id) {
        $user = Admin::findOrFail($id);

        if ($user->delete()) {
            return JsonResponse::send([], trans('messages.records.deleteSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function uploadImg(Request $request)
    {
        if($request->has('type')) {
            return $this->doUpload($request,$request->get('type'));
        }else{
            return JsonResponse::send([], trans('messages.records.editFailed'), -1);
        }
    }
}
