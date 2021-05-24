<?php

namespace App\Http\Controllers\Front;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\EditPostRequest;
use App\Models\Assortment;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Services\Response\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\General\UploadTrait;
use App\Models\Admin;
use App\Models\Notification;
use App\Models\PostImage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use UploadTrait;

    public function getPosts(Request $request) {
        $paged = $request->has('paged') &&  $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') &&  $request->get('count') < 100 ? $request->get('count') : 100;
        $keyword = $request->has('keyword') ? $request->get('keyword') : null;
        $tag = $request->has('tag') ? $request->get('tag') : null;
        $assortment = $request->has('category') ? $request->get('category') : null;
        $user_id = $request->has('user_id') ? $request->get('user_id') : null;

        $result = Post::where('show_status',true)
            //->with('tags')
            //->with('assortments')
            ->with('creator:id,name,username,avatar,bio')
            ->with('image')
            ->when($keyword, function($query) use($keyword) {
                $query->where('title', 'LIKE', '%' . $keyword . '%');
            })
            ->when($user_id, function($query) use($user_id) {
                $query->where('creator_id', $user_id)->where('creator_type',User::class);
            })
            ->when($assortment, function($query) use($assortment) {
                $query->whereHas('assortments', function($query) use($assortment) {
                    $query->where('assortment_id', $assortment);
                });
            })
            ->when($tag, function($query) use($tag) {
                $query->whereHas('tags', function($query) use($tag) {
                    $query->where('tag_id', $tag);
                });
            })
            ->when($request->get('order')=='view',function ($query) {
                $query->orderBy('visit', 'desc');
            })
            ->when($request->get('order')=='new',function ($query) {
                $query->latest();
            })
            ->when($request->get('order')=='old',function ($query) {
                $query->oldest();
            })
            ->paginate($count, ['*'], 'paged', $paged);
        return JsonResponse::send($result, trans('messages.records.getSuccess'));
    }

    public function getPost($id) {
        $result = Post::with('tags')
            ->with('assortments')
            ->with('creator:id,name,username,avatar,bio')
            ->with('image')
            ->findOrFail($id);
        $result->visit = $result->visit + 1;
        $result->save();

        return JsonResponse::send($result, trans('messages.records.getSuccess'));
    }

    public function getPostComments(Request $request) {
        $this->validate($request, [
            'post_id' => 'required|numeric|min:1'
        ]);

        $paged = $request->has('paged') &&  $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') &&  $request->get('count') < 100 ? $request->get('count') : 100;

        $result = Comment::where('commentable_id',$request->get('post_id'))
                ->where('commentable_type',Post::class)
                ->where('show_status',true)
                ->with('authorable:id,name,username,avatar,bio')
                ->latest()
                ->paginate($count, ['*'], 'paged', $paged);

        return JsonResponse::send($result, trans('messages.records.getSuccess'));
    }

    public function getUserPostsComments(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|numeric|min:1'
        ]);

        $post = Post::where('creator_id',$request->get('user_id'))->where('creator_type',User::class)->get();
        $ids = $post->pluck('id')->toArray();

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

    public function setPostComments(Request $request) {
        $this->validate($request, [
            'post_id' => 'required|numeric|min:1',
            // 'name'=> 'required',
            // 'email'=> 'required|email',
            'comment'=> 'required',
        ]);
        $user = Auth::guard('user-api')->user();

        $post = Post::findOrFail($request->get('post_id'));
        $params = [
            'body' => $request->get('comment'),
            'name' => $user->name,
            'email' => $user->email,
            'authorable_id' => $user->id,
            'authorable_type' => User::class
        ];

        $result = $post->comments()->create($params);

        $notifyUser = Auth::guard('user-api')->user();
        Notification::create([
            'text'  => '',
            'type'  => 'CreatePostComment',
            'user_id'   => $notifyUser->id,
            'user_type' => $notifyUser->type,
            'connect_id' => $result->id,
            'connect_type'=> Comment::class,
        ]);

        return JsonResponse::send($result, trans('messages.records.getSuccess'));
    }

    /**************************************/

    public function index(Request $request) {
        $paged = $request->has('paged') && $request->get('paged') > 1 ? $request->get('paged') : 1;
        $count = $request->has('count') && $request->get('count') < 100 ? $request->get('count') : 100;
        $records = Auth::guard('user-api')->user()->posts()->with('creator')->with('image')->latest()->paginate($count, ['*'], 'paged', $paged);
        return JsonResponse::send([
            'records' => $records
        ], trans('messages.records.getSuccess'));
    }

    public function create(CreatePostRequest $request) {
        $post = Auth()->guard('user-api')->user()->posts()->Create($request->all());

        if ($post) {
            $post->show_status = false;
            $post->save();
            if ($request->has('image_id')) {
                PostImage::where('id', $request->get('image_id'))->update(['post_id' => $post->id]);
            }
            if ($request->has('assortments') && $request->get('assortments') != null && !empty($request->get('assortments')) && $request->get('assortments') != [] && $request->get('assortments') != [] && $request->get('assortments') != [null]) {
                $post->Assortments()->sync($request->get('assortments'));
            }
            $tags = [];
            if ($request->has('tags') && $request->get('tags') != null && !empty($request->get('tags')) && $request->get('tags') != [] && $request->get('tags') != [] && $request->get('tags') != [null]) {
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
            // $post->tags()->sync($request->get('tags'));


            $notifyUser = Auth::guard('user-api')->user();
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

    public function edit(EditPostRequest $request) {
        $id = $request->get('post_id');
        $post = Auth::guard('user-api')->user()->posts()->with('image')->findOrFail($id);
        if($post) {
            $showStatus = $post->show_status;
            if ($post->update($request->only([
                'title',
                'text',
                'visit',
                'comment_status'
                ]))) {

                if ($request->has('image_id')) {
                    PostImage::where('id', $request->get('image_id'))->update(['post_id' => $post->id]);
                    PostImage::where('post_id',$post->id)->where('id','<>',$request->get('image_id'))->delete();
                }else{
                    PostImage::where('post_id',$post->id)->delete();
                }
                $post->show_status = false;
                $post->save();
                // if ($request->has('assortments') && $request->get('assortments') != null && !empty($request->get('assortments')) && $request->get('assortments') != [] && $request->get('assortments') != [] && $request->get('assortments') != [null]) {
                //     $post->Assortments()->sync($request->get('assortments'));
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
                // $post->tags()->sync($tags);
                // $post->tags()->sync($request->get('tags'));

                $notifyUser = Auth::guard('user-api')->user();
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
        }


        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function show(Request $request) {
        $id = $request->get('post_id');
        $record = Auth::guard('user-api')->user()->posts()->with('image')->with('assortments')->with('tags')->findOrFail($id);

        return JsonResponse::send([
            'record' => $record
        ], trans('messages.records.getSuccess'));
    }

    public function delete(Request $request) {
        $id = $request->get('post_id');
        $post = Auth::guard('user-api')->user()->posts()->findOrFail($id);

        if ($post->delete()) {
            $notifyUser = Auth::guard('user-api')->user();
            Notification::create([
                'text'  => '',
                'type'  => 'DelPost',
                'user_id'   => $notifyUser->id,
                'user_type' => $notifyUser->type,
                'connect_id' => $id,
                'connect_type'=> Post::class,
            ]);
            PostImage::where('post_id',$id)->delete();
            return JsonResponse::send([], trans('messages.records.deleteSuccess'));
        }

        return JsonResponse::send([], trans('messages.records.editFailed'), -1);
    }

    public function getAssortments($id = 0) {
        Assortment::whereNull('parent_id')->update(['parent_id'=>0]);
        $records =  Assortment::where('parent_id',$id)
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

    public function upload(Request $request) {
        return $this->doUpload($request,'post_image');
    }

}
