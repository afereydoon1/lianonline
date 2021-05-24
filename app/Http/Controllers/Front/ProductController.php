<?php

namespace App\Http\Controllers\Front;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\EditProductRequest;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\ProductImage;
use App\Models\Tag;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function getProducts(Request $request)
    {
        $paged = $request->has('paged') && $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') && $request->get('count') < 100 ? $request->get('count') : 100;
        $keyword = $request->has('keyword') ? $request->get('keyword') : null;
        $tag = $request->has('tag') ? $request->get('tag') : null;
        $category = $request->has('category') ? $request->get('category') : null;
        $user_id = $request->has('user_id') ? $request->get('user_id') : null;

        $result = Product::where('show_status',true)
            //->with('tags')
            //->with('categories')
            ->with('creator')
            ->with('images','mainPhotoObj')
            // ->with('file')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('title', 'LIKE', '%' . $keyword . '%');
            })
            ->when($user_id, function ($query) use ($user_id) {
                $query->where('creator_id', $user_id)->where('creator_type', User::class);
            })
            ->when($category, function ($query) use ($category) {
                $query->whereHas('categories', function ($query) use ($category) {
                    $query->where('category_id', $category);
                });
            })
            ->when($tag, function ($query) use ($tag) {
                $query->whereHas('tags', function ($query) use ($tag) {
                    $query->where('tag_id', $tag);
                });
            })
            ->when($request->get('order')=='view',function ($query) {
                $query->orderBy('visit', 'desc');
            })
            ->when($request->get('order')=='sale',function ($query) {
                $query->orderBy('sale', 'desc');
            })
            ->when($request->get('order')=='new',function ($query) {
                $query->latest();
            })
            ->when($request->get('order')=='old',function ($query) {
                $query->oldest();
            })
            ->when($request->get('order')=='max_price',function ($query) {
                $query->orderBy('price', 'desc');
            })
            ->when($request->get('order')=='min_price',function ($query) {
                $query->orderBy('price', 'asc');
            })
            ->paginate($count, ['*'], 'paged', $paged);
            if($result) {
               return JsonResponse::send($result, trans('messages.records.getSuccess'));
            }
            return JsonResponse::send([], trans('messages.records.editFailed'),-1);


    }

    public function getProduct($id)
    {
        $result = Product::with('tags')
            ->with('categories')
            ->with('creator:id,name,username,avatar,bio')
            ->with('images','mainPhotoObj')
            // ->with('file')
            ->findOrFail($id);
        if($result) {
            $result->visit = $result->visit + 1;
            $result->save();
            return JsonResponse::send($result, trans('messages.records.getSuccess'));
        }
        return JsonResponse::send([], trans('messages.records.editFailed'),-1);
    }

    public function getProductComments(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|numeric|min:1'
        ]);

        $paged = $request->has('paged') && $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') && $request->get('count') < 100 ? $request->get('count') : 100;


        $result = Comment::where('commentable_id',$request->get('product_id'))
            ->where('commentable_type',Product::class)
            ->where('show_status',true)
            ->with('authorable:id,name,username,avatar,bio')
            ->latest()
            ->paginate($count, ['*'], 'paged', $paged);
            return JsonResponse::send($result, trans('messages.records.getSuccess'));
    }

    public function getUserProductsComments(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|numeric|min:1'
        ]);

        $product = Product::where('creator_id',$request->get('user_id'))->where('creator_type',User::class)->get();
        $ids = $product->pluck('id')->toArray();

        $paged = $request->has('paged') && $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') && $request->get('count') < 100 ? $request->get('count') : 100;


        $result = Comment::whereIn('authorable_id',$ids)
            ->where('authorable_type',User::class)
            ->where('show_status',true)
            ->with('authorable:id,name,username,avatar,bio')
            ->latest()
            ->paginate($count, ['*'], 'paged', $paged);
            return JsonResponse::send($result, trans('messages.records.getSuccess'));
    }

    public function setProductComments(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|numeric|min:1',
            // 'name' => 'required',
            // 'email' => 'required|email',
            'comment' => 'required',
        ]);
        $user = Auth::guard('user-api')->user();

        $product = Product::findOrFail($request->get('product_id'));
        if(!$product) {
            return JsonResponse::send([], trans('messages.records.editFailed'),-1);
        }
        $params = [
            'body' => $request->get('comment'),
            'name' => $user->name,
            'email' => $user->email,
            'authorable_id' => $user->id,
            'authorable_type' => User::class
        ];

        $result = $product->comments()->create($params);
        if(!$result) {
            return JsonResponse::send([], trans('messages.records.editFailed'),-1);
        }

        $notifyUser = Auth::guard('user-api')->user();
        Notification::create([
            'text'  => '',
            'type'  => 'CreateProductComment',
            'user_id'   => $notifyUser->id,
            'user_type' => $notifyUser->type,
            'connect_id' => $result->id,
            'connect_type'=> Comment::class,
        ]);

        return JsonResponse::send($result, trans('messages.records.getSuccess'));
    }

    /*************/

    public function index(Request $request)
    {
        $paged = $request->has('paged') && $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') && $request->get('count') < 100 ? $request->get('count') : 100;
        $records = Auth::guard('user-api')->user()->products()
            ->with('images','mainPhotoObj')->with('file')->with('categories')
            ->latest()
            ->paginate($count, ['*'], 'paged', $paged);
        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function create(CreateProductRequest $request)
    {
        $product = Auth::guard('user-api')->user()->products()->create($request->all());

        if ($product) {
            $product->show_status = false;
            $product->save();
            if ($request->has('categories') && $request->get('categories') != null && !empty($request->get('categories')) && $request->get('categories') != [] && $request->get('categories') != [] && $request->get('categories') != [null]) {
                $product->categories()->sync($request->get('categories'));
            }


            $tags = [];
            if ($request->has('tags') && $request->get('tags') != null && !empty($request->get('tags')) && $request->get('tags') != [] && $request->get('tags') != [] && $request->get('tags') != [null]) {
                return $request->get('tags');
                foreach ($request->get('tags') as $tag) {
                    $findTag = Tag::where('name',$tag)->first();
                    if($findTag) {
                        $tags[] = $findTag->id;
                    }else{
                        $createdTag = Tag::create([
                            'name' => $tag
                        ]);
                        $tags[] = $createdTag->id;
                    }
                }
            }
            $product->tags()->sync($tags);
            // $product->tags()->sync($request->get('tags'));

            if ($request->has('file')) {
                ProductFile::where('id', $request->get('file'))->update(['product_id' => $product->id]);
            }

            if ($request->has('images')) {
                ProductImage::whereIn('id', $request->get('images'))->update(['product_id' => $product->id]);
            }
            $notifyUser = Auth::guard('user-api')->user();
            Notification::create([
                'text'  => '',
                'type'  => 'CreateProduct',
                'user_id'   => $notifyUser->id,
                'user_type' => $notifyUser->type,
                'connect_id' => $product->id,
                'connect_type'=> Product::class,
            ]);
            return JsonResponse::send([], trans('messages.records.addSuccess'));
        }
        return JsonResponse::send([], trans('messages.records.addFailed'), -1);
    }

    public function edit(EditProductRequest $request)
    {
        $id = $request->get('product_id');
        $product = Auth::guard('user-api')->user()->products()->with('images')->with('file')->with('categories')->with('tags')->where('id',$id)->first();
        if($product) {
            $showStatus = $product->show_status;
            if ($product->update($request->all())) {
                // if ($request->has('categories') && $request->get('categories') != null && !empty($request->get('categories')) && $request->get('categories') != [] && $request->get('categories') != [] && $request->get('categories') != [null]) {
                //     $product->categories()->sync($request->get('categories'));
                // }
                // $tags = [];
                // if ($request->has('tags') && $request->get('tags') != null && !empty($request->get('tags')) && $request->get('tags') != [] && $request->get('tags') != [] && $request->get('tags') != [null]) {
                //     foreach ($request->get('tags') as $tag) {
                //         $findTag = Tag::where('name',$tag)->first();
                //         if($findTag) {
                //             $tags[] = $findTag->id;
                //         }else{
                //             $createdTag = Tag::create([
                //                 'name' => $tag
                //             ]);
                //             $tags[] = $createdTag->id;
                //         }
                //     }
                // }
                // $product->tags()->sync($tags);
                // $product->tags()->sync($request->get('tags'));
                $product->show_status = false;
                $product->save();
                if ($request->has('file') && $request->get('file') != null && !empty($request->get('file'))) {
                    ProductFile::where('id', $request->get('file'))->update(['product_id' => $product->id]);
                }else{
                    $product->file()->delete();
                }

                if ($request->has('images')) {

                    $should_be_deleted = [];

                    foreach ($product->images as $image) {
                        if (!in_array($image->id, $request->get('images'))) {
                            $should_be_deleted[] = $image->id;
                        }
                    }
                    ProductImage::whereIn('id', $should_be_deleted)->delete();

                    ProductImage::whereIn('id', $request->get('images'))->update(['product_id' => $product->id]);
                }else{
                    $product->images()->delete();
                }
                $notifyUser = Auth::guard('user-api')->user();
                Notification::create([
                    'text'  => '',
                    'type'  => 'EditProduct',
                    'user_id'   => $notifyUser->id,
                    'user_type' => $notifyUser->type,
                    'connect_id' => $product->id,
                    'connect_type'=> Product::class,
                ]);

                return JsonResponse::send([], trans('messages.records.editSuccess'));
            }
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function show(Request $request)
    {
        $id = $request->get('product_id');
        $record = Auth::guard('user-api')->user()->products()->with('images','mainPhotoObj')->with('file')->with('categories')->with('tags')->where('id',$id)->first();
        if(!$record) {
            return JsonResponse::send([], trans('messages.records.editFailed'),-1);
        }
        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }

    public function delete(Request $request)
    {
        $id = $request->get('product_id');
        $category = Auth::guard('user-api')->user()->products()->findOrFail($id);
        if(!$category) {
            return JsonResponse::send([], trans('messages.records.editFailed'),-1);
        }
        if ($category->delete()) {
            $notifyUser = Auth::guard('user-api')->user();
            Notification::create([
                'text'  => '',
                'type'  => 'DelProduct',
                'user_id'   => $notifyUser->id,
                'user_type' => $notifyUser->type,
                'connect_id' => $id,
                'connect_type'=> Product::class,
            ]);
            return JsonResponse::send([], trans('messages.records.deleteSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function getCategories(Request $request)
    {
        $id = 0;
        if($request->has('category_id')) {
            $id = $request->get('category_id');
        }
        Category::whereNull('parent_id')->update(['parent_id'=>0]);
        $records = Category::where('parent_id',$id)
            ->with('children')
            ->orderBy('order')->get()->toArray();
        if(!$records) {
            return JsonResponse::send([], trans('messages.records.editFailed'),-1);
        }
        $records = $this->prepareCategoryList($records);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function getTags()
    {
        $records = Tag::get()->toArray();
        $records = $this->prepareTagsList($records);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    private function prepareCategoryList($records)
    {
        $categories = [];
        foreach ($records as $key => $record) {
            if (empty($categories[$key])) {
                $categories[$key] = [];
            }

            $categories[$key]['label'] = $record['title'];
            $categories[$key]['id'] = $record['id'];
            if ($records[$key]['children']) {
                $categories[$key]['children'] = $this->prepareCategoryList($records[$key]['children']);
            }
        }

        return $categories;
    }

    private function prepareTagsList($records)
    {
        $tags = [];
        foreach ($records as $key => $record) {
            $tags[] = [
                'value' => $record['id'],
                'label' => $record['name'],
                'selected' => false
            ];
        }

        return $tags;
    }
}
