<?php

namespace App\Http\Controllers\Management\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Response\JsonResponse;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Auth\Notifications\ResetPassword;

class ForgetPasswordController extends Controller
{
    use CanResetPassword;

    public function send(Request $request) {
        $this->validate($request, [
            'email' => 'required|email|exists:admins,email',
        ]);

        ResetPassword::create([

        ]);
    }
}
