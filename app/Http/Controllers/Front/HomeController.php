<?php

namespace App\Http\Controllers\Front;

use App\Models\Post;
use App\Models\Product;
use App\Models\ReferralLink;
use App\Models\SiteInfo;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;
use App\Http\Controllers\Controller;
use App\Http\Controllers\General\UploadTrait;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Searchable\ModelSearchAspect;

class HomeController extends Controller
{
    use UploadTrait;

    public function search(Request $request) {
        $this->validate($request, [
            'keyword' => 'required|min:3'
        ]);

        $paged = $request->has('paged') &&  $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') &&  $request->get('count') < 100 ? $request->get('count') : 100;
        $keyword = $request->has('keyword') ? $request->get('keyword') : null;
        if ($keyword) {
            $result = [];
            $resultPost = Post::with('image')->where('show_status', 1)->where('title','like','%'.$keyword.'%')
            ->orWhere('text','like','%'.$keyword.'%')->get();
            foreach ($resultPost as $post) {
                $result[] = [
                    'type' => 'posts',
                    'searchable' => $post,
                    'title' => $post->title,
                    'url' => null,
                ];
            }
            $resultProduct = Product::with('images','mainPhotoObj')->where('show_status', 1)->where('title','like','%'.$keyword.'%')
            ->orWhere('description','like','%'.$keyword.'%')->get();
            foreach ($resultProduct as $product) {
                $result[] = [
                    'type' => 'products',
                    'searchable' => $product,
                    'title' => $product->title,
                    'url' => null,
                ];
            }
            return JsonResponse::send([
                'total_items' => count($result),
                'items' => $this->paginate($result,$count,$paged)
            ], trans('messages.records.getSuccess'));
        }

        return JsonResponse::send([], trans('messages.general_errors.unknown'), 400, 400);
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ;//?: (Paginator::resolveCurrentPage() ?: 1);
        $items2 = Collection::make($items);//$items instanceof Collection ? $items :
        $itemsSlice = array_slice($items,($page-1)*$perPage, $perPage);
        return new LengthAwarePaginator($itemsSlice, $items2->count(), $perPage, $page, $options);
    }

    public function createReferral(Request $request) {
        $this->validate($request, [
            'referral_code' => 'required',
            'landing' => 'required',
            'host' => 'required',
            'ip' => 'required',
        ]);
        $referral = ReferralLink::where('code', $request->get('referral_code'))->firstOrFail();

        $req = $referral->referralRequests()->create($request->all());

        if ($req) {
            return JsonResponse::send($req, trans('messages.records.addSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.addFailed'), 500, 500);
    }
    public function getSiteInfo() {
        $info = SiteInfo::findOrFail(1);

        $info->stats = [
            'total_products'    => Product::where('show_status',true)->count(),
            'total_posts'       => Post::where('show_status',true)->count(),
            'total_orders'      => Order::count(),
            'success_orders'    => Order::where('status','ok')->count(),
            'total_tickets'     => Ticket::count(),
            'total_comments'    => Comment::where('show_status',true)->count(),
            'total_users'       => User::count(),
        ];
        return $info;
    }
}
