<?php

namespace App\Http\Controllers\Management;

use App\Models\Comment;
use App\Models\Tag;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\Response\JsonResponse;
use App\Http\Requests\EditProductRequest;
use App\Http\Requests\EditCategoryRequest;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Controllers\General\UploadTrait;
use App\Models\Notification;

class ProductController extends Controller
{
    use UploadTrait;

    public function index(Request $request) {
        $search_id = ($request->has('id') && $request->get('id') != null && !empty($request->get('id'))) ? $request->get('id') : null;
        $search_title = ($request->has('title') && $request->get('title') != null && !empty($request->get('title'))) ? $request->get('title') : null;
        $search_key = ($request->has('key') && $request->get('key') != null && !empty($request->get('key'))) ? $request->get('key') : null;
        $search_show_status_isNotNull = $request->has('show_status') ? true : false;
        if($search_show_status_isNotNull) {
            $search_show_status = $request->get('show_status');
            if($search_show_status == "2") {
                $search_show_status_isNotNull = false;
            }else{
                $search_show_status = $search_show_status == "1" ? true : false;
            }
        }

        $records = Product::orderBy('id','desc')
        ->when($search_id,function($query) use($search_id) {
            $query->where('id', $search_id);
        })
        ->when($search_title,function($query) use($search_title) {
            $query->where('title','like','%'.$search_title.'%');
        })
        ->when($search_key,function($query) use($search_key) {
            $query->where('key','like','%'.$search_key.'%');
        })
        ->when($search_show_status_isNotNull,function($query) use($search_show_status) {
            $query->where('show_status', $search_show_status);
        })
        ->with('images')->with('file')->with('creator')->paginate(50);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function create(CreateProductRequest $request) {
        if($request->has('creator')) {
            $request->merge([
                'creator_id' => $request->creator['id'],
                'creator_type' => $request->creator['type']
            ]);
        }else{
            $request->merge([
                'creator_id' => Auth::guard('admin-api')->user()->id,
                'creator_type' => Auth::guard('admin-api')->user()->type
            ]);
        }
		
		if ($request->has('file')) {
			$request->merge(['total' => 1]);
		}
		
        $product = Product::create($request->all());

        if ($product) {
            if ($request->has('categories')) {
                $product->categories()->sync($request->get('categories'));
            }

            $tags = [];
            if ($request->has('tags') && $request->get('tags') != null && !empty($request->get('tags'))) {
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

            if ($request->has('file')) {
                ProductFile::where('id', $request->get('file'))->update(['product_id' => $product->id]);
            }

            if ($request->has('images')) {
                ProductImage::whereIn('id', $request->get('images'))->update(['product_id' => $product->id]);
            }

            $notifyUser = Auth::guard('admin-api')->user();
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

    public function edit(EditProductRequest $request, $id) {
        if($request->get('has_discount') == 0) {
            $request->merge([
                'discounted_price' => 0,
            ]);
        }
        if($request->has('creator')) {
            $request->merge([
                'creator_id' => $request->creator['id'],
                'creator_type' => $request->creator['type'],
            ]);
        }else{
            $request->merge([
                'creator_id' => Auth::guard('admin-api')->user()->id,
                'creator_type' => Auth::guard('admin-api')->user()->type,
            ]);
        }
        $product = Product::with('images')->with('file')->with('categories')->with('tags')->findOrFail($id);
        $product->main_photo = $request->get('main_photo');
        $product->save();
        if ($product->update($request->all())) {
            if ($request->has('categories')) {
                $product->categories()->sync($request->get('categories'));
            }
            $tags = [];
            if ($request->has('tags') && $request->get('tags') != null && !empty($request->get('tags'))) {
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

            if ($request->has('file') && $request->get('file') != null && !empty($request->get('file'))) {
                if(substr($request->get('file'),0,1)=='{') {
                    $oldFile = $product->file;
                    if($oldFile != null && $request->get('file')->id !== $oldFile->id) {
                        $product->file()->delete();
                    }
                    ProductFile::where('id', $request->get('file'))->update(['product_id' => $product->id]);
                }else{
                    $oldFile = $product->file;
                    if($oldFile != null && $request->get('file') !== $oldFile->id) {
                        $product->file()->delete();
                    }else{
                    }
                    ProductFile::where('id', $request->get('file'))->update(['product_id' => $product->id]);
                }
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
            }

            $notifyUser = Auth::guard('admin-api')->user();
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

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function show($id) {
        $record = Product::with('images','file','categories','tags','creator')->findOrFail($id);

        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }

    public function delete($id) {
        $category = Product::findOrFail($id);

        if ($category->delete()) {
            $notifyUser = Auth::guard('admin-api')->user();
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

    public function comments() {
        $records = Comment::with('authorable')->with('commentable')->where('commentable_type', 'App\Models\Product')->latest()->paginate(50);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function deleteComment($id) {
        $comment = Comment::findOrFail($id);

        if ($comment->delete()) {
            return JsonResponse::send([], trans('messages.records.deleteSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function showComment($id) {
        $record = Comment::with('authorable')->with('commentable')->findOrFail($id);

        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }
    public function editComment(Request $request,$id) {
        $record = Comment::with('authorable')->with('commentable')->findOrFail($id);
        if(!$record) {
            return JsonResponse::send([], trans('messages.records.editFailed'), -1);
        }
        $record->update($request->all());
        return JsonResponse::send([], trans('messages.records.getSuccess'));
    }

    public function getCategories($id = null) {
        $records =  Category::where('parent_id',0)
            ->with('children')
            ->orderBy('order')->get()->toArray();
        $records = $this->prepareCategoryList($records);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function getTags() {
        $records =  Tag::get()->toArray();
        $records = $this->prepareTagsList($records);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function getUsers() {
        // $records =  Admin::get()->toArray();
        // $users
        $records = User::get()->toArray();
        // $records = array_merge($records, $users);
        //$records = $this->prepareUsersList($records);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function upload(Request $request, $type='product_image') {
        return $this->doUpload($request, $type);
    }

    private function prepareCategoryList($records) {
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

    private function prepareTagsList($records) {
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

    private function prepareUsersList($records) {
        $users = [];
        foreach ($records as $key => $record) {
            $users[] = [
                'id' => $record['id'],
                'name' => $record['name']
            ];
        }

        return $users;
    }
}
