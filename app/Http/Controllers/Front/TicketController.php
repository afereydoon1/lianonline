<?php

namespace App\Http\Controllers\Front;

use App\Models\TicketItem;
use App\Services\Response\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function createTicket(Request $request) {
        $this->validate($request, [
            'title' => 'required',
            'text' => 'required',
            'type' => 'required'
        ]);

        $user = Auth::guard('user-api')->user();

        $params = $request->only(['title', 'product_id', 'order_id', 'receiver_id', 'receiver_type', 'type']);
        if(!($params['type'] == 'report_abuse' || $params['type'] == 'copy_right') ) {
            $params['type'] = 'support';
        }
        if (!(isset($params['receiver_id']) && !empty($params['receiver_id']))) {
            $params['receiver_id'] = 1;
        }
        if (!(isset($params['receiver_id']) && !empty($params['receiver_type']))) {
            $params['receiver_type'] = 'Admin';
        }
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

            $notifyUser = Auth::guard('user-api')->user();
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
            'text' => 'required',
            'status' => 'required'
        ]);

        $user = Auth::guard('user-api')->user();
        $ticket_id = $request->get('ticket_id');
        $ticket = $user->tickets()->find($ticket_id);

        if (!$ticket) {
            $ticket = $user->otherTickets()->findOrFail($ticket_id);
        }

        $item_params = [
            'text' => $request->get('text'),
            'ticket_id' => $ticket->id
        ];

        $item = $user->ticketItems()->create($item_params);

        if ($item) {
            $ticket->updated_at = Carbon::now();
            $ticket->status = $request->get('status');
            $ticket->save();
            $notifyUser = Auth::guard('user-api')->user();
            Notification::create([
                'text'  => '',
                'type'  => 'ReplayTicket',
                'user_id'   => $notifyUser->id,
                'user_type' => $notifyUser->type,
                'connect_id' => $ticket->id,
                'connect_type'=> Ticket::class,
            ]);
            return JsonResponse::send(['ticket_id' => $ticket->id], trans('messages.records.addSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.addFailed'), 500, 500);
    }

    public function getTicket(Request $request) {
        $this->validate($request, [
            'ticket_id' => 'required'
        ]);
        $user = Auth::guard('user-api')->user();

        $ticket_id = $request->get('ticket_id');
        $ticket = $user->tickets()->with('ticketItems')->with('creator:id,name,username,avatar,bio')->with('receiver:id,name,username,avatar,bio')->with('order')->with('product')->find($ticket_id);

        if (!$ticket) {
            $ticket = $user->otherTickets()->with('ticketItems')->with('creator:id,name,username,avatar,bio')->with('receiver:id,name,username,avatar,bio')->with('order')->with('product')->findOrFail($ticket_id);
        }

        return JsonResponse::send($ticket, trans('messages.records.getSuccess'));
    }

    public function getTickets(Request $request) {
        $user = Auth::guard('user-api')->user();
        $paged = $request->has('paged') && $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') && $request->get('count') < 100 ? $request->get('count') : 100;

        $myTickets = $user->tickets()->with('creator:id,name,username,avatar,bio')->with('receiver:id,name,username,avatar,bio')->with('order')->with('product')->latest()->get();

        $otherTickets = $user->otherTickets()->with('creator:id,name,username,avatar,bio')->with('receiver:id,name,username,avatar,bio')->with('order')->with('product')->latest()->get();

        $tickets = $myTickets->merge($otherTickets);
        $total = $tickets->count();
        $tickets = $tickets->sortByDesc('created_at')->forPage($paged, $count);

        return JsonResponse::send([
            'total' => $total,
            'items' => $tickets
        ], trans('messages.records.getSuccess'));
    }

    public function setContacts(Request $request) {
        $this->validate($request, [
            'contactable_id' => 'required',
            'contactable_type' => 'required',
        ]);

        $user = Auth::guard('user-api')->user();

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
        $user = Auth::guard('user-api')->user();

        $contacts = $user->contacts()->with('contactable:id,name,username')->get();

        if ($contacts) {
            return JsonResponse::send($contacts, trans('messages.records.getSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.addFailed'), 500, 500);
    }
}
