<?php

namespace App\Http\Controllers\Management;

use App\Models\TicketItem;
use App\Services\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function getAllUsers() {
        $records = User::orderBy('name','asc')->get();

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }
    public function createTicket(Request $request) {
        $this->validate($request, [
            'title' => 'required',
            'text' => 'required',
            'type' => 'required'
        ]);

        $user = Auth::guard('admin-api')->user();

        $params = $request->only(['title', 'product_id', 'order_id', 'receiver_id', 'receiver_type', 'type']);
        if (!empty($params['receiver_type'])) {
            $params['receiver_type'] = 'App\Models\\' . $params['receiver_type'];
        }
        $ticket = $user->tickets()->create($params);

        if ($ticket) {
            $item_params = [
                'text' => $request->get('text'),
                'ticket_id' => $ticket->id
            ];
            $item = $user->ticketItems()->create($item_params);
            $notifyUser = Auth::guard('admin-api')->user();
            $ticketType = 'CreateTicket';
            if ($ticket->type == "report_abuse") {
                $ticketType = 'AbuseReportTicket';
            }elseif ($ticket->type == "copy_right") {
                $ticketType = 'CopyRightTicket';
            }
            Notification::create([
                'text'  => '',
                'type'  => $ticketType,
                'user_id'   => $notifyUser->id,
                'user_type' => $notifyUser->type,
                'connect_id' => $ticket->id,
                'connect_type'=> Ticket::class,
            ]);
            return JsonResponse::send(['ticket_id' => $ticket->id], trans('messages.records.addSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.addFailed'), 500, 500);
    }

    public function replyTicket(Request $request) {
        $this->validate($request, [
            'ticket_id' => 'required',
            // 'text' => 'required',
            'status' => 'required'
        ]);

        $user = Auth::guard('admin-api')->user();
        $ticket_id = $request->get('ticket_id');
        $ticket = Ticket::findOrFail($ticket_id);
        if($request->has('text') &&  !empty($request->get('text'))) {
           $item_params = [
                'text' => $request->get('text'),
                'creator_id' => $user->id,
                'creator_type' => $user->type,
                'ticket_id' => $ticket->id
            ];

            $item = $ticket->ticketItems()->create($item_params);
            if ($item) {
                // $ticket->updated_at = Carbon::now();
                $ticket->status = $request->get('status');
                $ticket->save();
                $notifyUser = Auth::guard('admin-api')->user();
                Notification::create([
                    'text'  => '',
                    'type'  => 'ReplayTicket',
                    'user_id'   => $notifyUser->id,
                    'user_type' => $notifyUser->type,
                    'connect_id' => $ticket->id,
                    'connect_type'=> Ticket::class,
                ]);
                return JsonResponse::send(['ticket_id' => $ticket->id], trans('messages.records.addSuccess'));
            }else{
               return JsonResponse::send([], trans('messages.records.addFailed'), 500, 500);
            }
        }else{
            $ticket->status = $request->get('status');
            $ticket->save();
            return JsonResponse::send(['ticket_id' => $ticket->id], trans('messages.records.editSuccess'));
        }


    }

    public function getTicket(Request $request) {
        $this->validate($request, [
            'ticket_id' => 'required'
        ]);
        $user = Auth::guard('admin-api')->user();
        $ticket = Ticket::with('creator','receiver','order','product','ticketItems','ticketItems.creator')->find($request->get('ticket_id'));
        // $ticket_id = $request->get('ticket_id');
        // $ticket = $user->tickets()->with('ticketItems')->with('creator')->with('receiver')->with('order')->with('product')->find($ticket_id);

        // if (!$ticket) {
        //     $ticket = $user->otherTickets()->with('ticketItems')->with('creator')->with('receiver')->with('order')->with('product')->findOrFail($ticket_id);
        // }

        // return JsonResponse::send($ticket, trans('messages.records.getSuccess'));
        return JsonResponse::send([
            'record' => $ticket
        ], trans('messages.records.getSuccess'));
    }

    public function delTicket(Request $request) {
        $this->validate($request, [
            'ticket_id' => 'required'
        ]);

        Ticket::where('id',$request->ticket_id)->delete();

        return JsonResponse::send([], trans('messages.records.deleteSuccess'));
    }

    public function getTickets(Request $request) {
        $type = null;
        if($request->has('type')) $type = $request->get('type');
        $Tickets = Ticket::when($type,function($query, $type) {
                        return $query->where('type',$type);
                })
                ->with('creator','receiver','order','product')
                ->orderBy('updated_at','desc')
                ->paginate(50);
        return JsonResponse::send([
            // 'total' => $total,
            'records' => $Tickets
        ], trans('messages.records.getSuccess'));
    }

    public function setContacts(Request $request) {
        $this->validate($request, [
            'contactable_id' => 'required',
            'contactable_type' => 'required',
        ]);

        $user = Auth::guard('admin-api')->user();

        $params = $request->only(['contactable_id', 'contactable_type']);

        $params['contactable_type'] = 'App\Models\\' . $params['contactable_type'];
        $params['owner_type'] = 'App\Models\User';
        $params['owner_id'] = $user->id;
        $contact = $user->contacts()->firstOrCreate($params);

        if ($contact) {
            return JsonResponse::send($contact, trans('messages.records.addSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.addFailed'), 500, 500);
    }

    public function getContacts(Request $request) {
        $user = Auth::guard('admin-api')->user();

        $contacts = $user->contacts()->with('contactable')->get();

        if ($contacts) {
            return JsonResponse::send($contacts, trans('messages.records.getSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.addFailed'), 500, 500);
    }
}
