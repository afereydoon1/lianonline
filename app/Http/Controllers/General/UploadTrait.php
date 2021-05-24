<?php

namespace App\Http\Controllers\General;

use App\Models\PostImage;
use App\Models\ProductFile;
use App\Models\ProductImage;
use App\Models\SliderImage;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Response\JsonResponse;

trait UploadTrait
{
    private function doUpload(Request $request, $type='product_image') {
        $folder = date('Y/m').'/';
        if ($type === 'product_image') {
            $this->validate($request, [
                'file' => 'required|image|max:5000'
            ]);

            $ext = $request->file->extension();
            $filename = md5(mt_rand(0,15555555) . microtime() . time() . mt_rand(500,99999)) . '.' . $ext;
            $path = ($request->file->storeAs('./',$folder. $filename, 'product_image'));

            $product_image = new ProductImage();
            $product_image->name = $filename;
            $product_image->full_path = app_path('/public/') . '/uploads/product_images/' .$folder. $filename;;
            $product_image->download_path = '/uploads/product_images/' .$folder. $filename;
            $product_image->save();

            return JsonResponse::send([
                'file_id' => $product_image->id,
                'url' => 'https://core.lianonline.ir' . $product_image->download_path
            ], trans('messages.records.getSuccess'));
        } elseif ($type === 'product_file') {
            $this->validate($request, [
                'file' => 'required|mimes:zip|max:1000000'
            ]);

            $ext = $request->file->extension();
            $size = $request->file->getSize();
            $filename = md5(mt_rand(0, 15555555) . microtime() . time() . mt_rand(500, 99999)) . '.' . $ext;
            $path = ($request->file->storeAs('./',$folder. $filename, 'download_host'));

            $product_file = new ProductFile();
            $product_file->name = $filename;
            $product_file->extension = $ext;
            $product_file->size = $size;
            $product_file->full_path = 'https://dl.lianonline.ir/uploads/' .$folder. $filename;;
            $product_file->download_path = 'https://dl.lianonline.ir/uploads/' .$folder. $filename;
            $product_file->save();
            return JsonResponse::send([
                'file_id' => $product_file->id,
                'name' => $product_file->name,
                'size' => $product_file->size,
                'url' => $product_file->download_path
            ], trans('messages.records.getSuccess'));
        } elseif ($type === 'user-files') {
            // $this->validate($request, [
            //     'national_card_image' => 'image|mimes:jpeg,jpg,png|max:5000',
            //     'credit_card_image' => 'image|mimes:jpeg,jpg,png|max:5000',
            // ]);

            $files = [
                'national_card_image' => '',
                'credit_card_image' => ''
            ];

            if ($request->hasFile('national_card_image')) {
                $ext = $request->national_card_image->extension();
                $filename = md5(mt_rand(0,15555555) . microtime() . time() . mt_rand(500,99999)) . '.' . $ext;
                $path = ($request->national_card_image->storeAs('./', $folder.$filename, 'users_images'));
                $files['national_card_image'] = 'https://core.lianonline.ir' .'/uploads/users_images/' . $folder.$filename;
            }

            if ($request->hasFile('credit_card_image')) {
                $ext = $request->credit_card_image->extension();
                $filename = md5(mt_rand(0,15555555) . microtime() . time() . mt_rand(500,99999)) . '.' . $ext;
                $path = ($request->credit_card_image->storeAs('./', $folder.$filename, 'users_images'));
                $files['credit_card_image'] = 'https://core.lianonline.ir' .'/uploads/users_images/' . $folder.$filename;
            }

            return $files;
        } elseif ($type === 'post_image') {
            $this->validate($request, [
                'file' => 'required|image|max:5000'
            ]);

            $ext = $request->file->extension();
            $filename = md5(mt_rand(0,15555555) . microtime() . time() . mt_rand(500,99999)) . '.' . $ext;
            $path = ($request->file->storeAs('./', $folder.$filename, 'post_images'));

            $post_image = new PostImage();
            $post_image->name = $filename;
            $post_image->full_path = app_path('/public/') . '/uploads/post_images/' . $folder.$filename;;
            $post_image->download_path = '/uploads/post_images/' . $folder.$filename;
            $post_image->save();

            return JsonResponse::send([
                'file_id' => $post_image->id,
                'url' => 'https://core.lianonline.ir' .$post_image->download_path
            ], trans('messages.records.getSuccess'));
        } elseif ($type === 'slider_image') {
            $this->validate($request, [
                'file' => 'required|image|max:5000'
            ]);

            $ext = $request->file->extension();
            $filename = md5(mt_rand(0,15555555) . microtime() . time() . mt_rand(500,99999)) . '.' . $ext;
            $path = ($request->file->storeAs('./', $folder.$filename, 'slider_images'));

            $slider_image = new SliderImage();
            $slider_image->name = $filename;
            $slider_image->full_path = app_path('/public/') . '/uploads/slider_images/' . $folder.$filename;;
            $slider_image->download_path = '/uploads/slider_images/' . $folder.$filename;
            $slider_image->save();

            return JsonResponse::send([
                'file_id' => $slider_image->id,
                'url' => 'https://core.lianonline.ir' .$slider_image->download_path
            ], trans('messages.records.getSuccess'));
        } elseif ($type === 'user-file-avatar') {
            $this->validate($request, [
                'file' => 'image|mimes:jpeg,jpg,png|max:5000',
            ]);
            $address='';
            if ($request->hasFile('file')) {
                $ext = $request->file->extension();
                $filename = 'avatar_'.md5(mt_rand(0,15555555) . microtime() . time() . mt_rand(500,99999)) . '.' . $ext;
                $path = ($request->file->storeAs('./', $folder.$filename, 'users_images'));
                $address = '/uploads/users_images/' . $folder.$filename;
            }
            return JsonResponse::send([
                'avatar' => 'https://core.lianonline.ir' .$address
            ], trans('messages.records.addSuccess'));;
        } elseif ($type === 'user-file-national_card') {
            $this->validate($request, [
                'file' => 'image|mimes:jpeg,jpg,png|max:5000',
            ]);
            $address='';
            if ($request->hasFile('file')) {
                $ext = $request->file->extension();
                $filename ='national_'. md5(mt_rand(0,15555555) . microtime() . time() . mt_rand(500,99999)) . '.' . $ext;
                $path = ($request->file->storeAs('./', $folder.$filename, 'users_images'));
                $address = '/uploads/users_images/' . $folder.$filename;
            }
            return JsonResponse::send([
                'national_card' => 'https://core.lianonline.ir' .$address
            ], trans('messages.records.addSuccess'));;
        } elseif ($type === 'user-file-credit_card') {
            $this->validate($request, [
                'file' => 'image|mimes:jpeg,jpg,png|max:5000',
            ]);
            $address='';
            if ($request->hasFile('file')) {
                $ext = $request->file->extension();
                $filename = 'credit_'.md5(mt_rand(0,15555555) . microtime() . time() . mt_rand(500,99999)) . '.' . $ext;
                $path = ($request->file->storeAs('./', $folder.$filename, 'users_images'));
                $address = '/uploads/users_images/' . $folder.$filename;
            }
            return JsonResponse::send([
                'credit_card' => 'https://core.lianonline.ir' .$address
            ], trans('messages.records.addSuccess'));;
        }

        return JsonResponse::send([], trans('messages.records.getSuccess'), 500,500);
    }
}
