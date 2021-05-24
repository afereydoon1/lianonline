<?php

namespace App\Http\Controllers\Management;


use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ReferralRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;

class FinanceController extends Controller
{
    public function getTransactionLists() {
        $records = Transaction::orderBy('id','desc')->with('user','order','wallet','product')->paginate(50);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }
    public function getWithdrawalLists() {
        $records = Withdrawal::orderBy('id','desc')->with('wallet','user')->paginate(50);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }
    public function getWithdrawal($id) {
        $record = Withdrawal::where('id',$id)->with('wallet','user','transaction')->first();

        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }

    public function updateWithdrawal(Request $request, $id)
    {
        $record = Withdrawal::where('id',$id)->with('wallet','user','transaction')->first();
        if($record) {
            if($record->transaction) {
                $record->transaction->status = $request->status;
                $record->transaction->text = $request->text;
                $record->transaction->save();
            }

            $record->update([
                'status' => $request->status
            ]);
            return JsonResponse::send([
                'record' => $record
            ], trans('messages.records.editSuccess'));
        }
        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.editFailed'),400,200);
    }

    public function getReferralList()
    {
        $records = ReferralRequest::orderBy('id','desc')->with('order','referralLink','referralLink.user')->paginate(50);
        return JsonResponse::send([
                'records' => $records
            ], trans('messages.records.getSuccess'));

    }

    public function createTransaction(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'type' => 'required',
            'amount' => 'required|numeric',
            // 'description' => '',
        ]);
        $user = User::with('wallet')->where('id',$request->user_id)->first();
        if(!$user) {
            return JsonResponse::send([], trans('messages.records.editFailed'),400,200);
        }
        Transaction::create([
            'user_id' => $user->id ,
            'user_type' => 'App\Models\User',
            'wallet_id' => $user->wallet->id,
            'type' =>  $request->type,
            'amount' => $request->amount,
            'text' =>  $request->description,
            'status' => 'success',
            'order_id' => null,
            'product_id' => null
        ]);
        if($request->type == 'charge') {
            $user->wallet->amount =  $user->wallet->amount + $request->amount;
        }else{
            if($user->wallet->amount >= $request->amount) {
                $user->wallet->amount =  $user->wallet->amount - $request->amount;
            }else{
                return JsonResponse::send([], "مقدار مبلغ کل کیف پول کاربر کمتر از مقدار فوق است",400,200);
            }
        }
        $user->wallet->save();
        return JsonResponse::send([], trans('messages.records.editSuccess'));

    }

}
