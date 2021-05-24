<?php

namespace App\Http\Controllers\Front;

use App\Models\ProductFile;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Response\JsonResponse;
use App\Http\Controllers\General\UploadTrait;

class UploadController extends Controller
{
    use UploadTrait;
    public function uploadFile(Request $request) {
        return $this->doUpload($request, 'product_file');
    }

    public function uploadImage(Request $request) {
        return $this->doUpload($request, 'product_image');
    }


}
