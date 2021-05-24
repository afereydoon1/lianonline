<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Controllers\General\UploadTrait;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\EditPostRequest;
use App\Models\Admin;
use App\Models\Assortment;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Models\PostImage;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use UploadTrait ;

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

        $records = Post::orderBy('id','desc')
        ->when($search_id,function($query) use($search_id) {
            $query->where('id', $search_id);
        })
        ->when($search_title,function($query) use($search_title) {
            $query->where('title','like','%'.$search_title.'%');
        })
        ->when($search_key,function($query) use($search_key) {
            $query->where('slug','like','%'.$search_key.'%');
        })
        ->when($search_show_status_isNotNull,function($query) use($search_show_status) {
            $query->where('show_status', $search_show_status);
        })
        ->with('creator','tags','assortments')->paginate(50);

        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function create(CreatePostRequest $request) {
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
        $post = Post::create($request->all());

        if ($post) {
            if ($request->has('image_id')) {
                PostImage::where('id', $request->get('image_id'))->update(['post_id' => $post->id]);
            }
            if ($request->has('assortments')) {
                $post->Assortments()->sync($request->get('assortments'));
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
            $post->tags()->sync($tags);
            $notifyUser = Auth::guard('admin-api')->user();
            Notification::create([
                'text'  => '',
                'type'  => 'CreatePost',
                'user_id'   => $notifyUser->id,
                'user_type' => $notifyUser->type,
                'connect_id' => $post->id,
                'connect_type'=> Post::class,
            ]);
            return JsonResponse::send([], trans('messages.records.addSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.addFailed'), -1);
    }

    public function edit(EditPostRequest $request, $id) {
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
        $post = Post::with('assortments','tags','image')->findOrFail($id);
        if ($post->update($request->only([
            'title',
            'text',
            'visit',
            'show_status',
            'comment_status',
            'creator_id',
            'creator_type',
            ]))) {

            if ($request->has('image_id')) {
                PostImage::where('id', $request->get('image_id'))->update(['post_id' => $post->id]);
                PostImage::where('post_id',$post->id)->where('id','<>',$request->get('image_id'))->delete();
            }else{
                PostImage::where('post_id',$post->id)->delete();
            }
            if ($request->has('assortments')) {
                $post->Assortments()->sync($request->get('assortments'));
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
            $post->tags()->sync($tags);
            $notifyUser = Auth::guard('admin-api')->user();
            Notification::create([
                'text'  => '',
                'type'  => 'EditPost',
                'user_id'   => $notifyUser->id,
                'user_type' => $notifyUser->type,
                'connect_id' => $post->id,
                'connect_type'=> Post::class,
            ]);
            return JsonResponse::send([], trans('messages.records.editSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function show($id) {
        $record = Post::with('assortments','tags','image','creator')->findOrFail($id);

        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }

    public function delete($id) {
        $post = Post::findOrFail($id);

        if ($post->delete()) {
            PostImage::where('post_id',$id)->delete();
            $notifyUser = Auth::guard('admin-api')->user();
            Notification::create([
                'text'  => '',
                'type'  => 'DelPost',
                'user_id'   => $notifyUser->id,
                'user_type' => $notifyUser->type,
                'connect_id' => $id,
                'connect_type'=> Post::class,
            ]);
            return JsonResponse::send([], trans('messages.records.deleteSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function comments() {
        $records = Comment::with('authorable')->with('commentable')->where('commentable_type', 'App\Models\Post')->latest()->paginate(50);

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

    public function getAssortments($id = null) {
        $records =  Assortment::where('parent_id',0)
            ->with('children')
            ->orderBy('order')->get()->toArray();
        $records = $this->prepareAssortmentList($records);

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

    private function prepareAssortmentList($records) {
        $categories = [];
        foreach ($records as $key => $record) {
            if (empty($categories[$key])) {
                $categories[$key] = [];
            }

            $categories[$key]['label'] = $record['title'];
            $categories[$key]['id'] = $record['id'];
            if ($records[$key]['children']) {
                $categories[$key]['children'] = $this->prepareAssortmentList($records[$key]['children']);
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
                'name' => $record['name'],
                'type' => $record['type']
            ];
        }

        return $users;
    }

    public function upload(Request $request) {
        return $this->doUpload($request,'post_image');
    }
}
