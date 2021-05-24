<?php

namespace App\Exceptions;

use App\Services\Response\JsonResponse;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, $exception)
    {
        if (method_exists($exception, 'render')) {
            return $exception->render($request);
        } elseif ($exception instanceof Responsable) {
            return $exception->toResponse($request);
        }

        if ($exception instanceof HttpResponseException) {
            return JsonResponse::send($this->getDataField($exception), trans('messages.general_errors.server'), 500, 500);
        } elseif ($exception instanceof ModelNotFoundException){
            return JsonResponse::send($this->getDataField($exception), trans('messages.general_errors.recordNotFound'), 404, 404);
        } elseif ($exception instanceof NotFoundHttpException) {
            return JsonResponse::send($this->getDataField($exception), trans('messages.general_errors.urlNotFound'), 404, 404);
        } elseif ($exception instanceof AuthorizationException) {
            return JsonResponse::send($this->getDataField($exception), trans('messages.general_errors.authorization'), 403, 403);
        } elseif ($exception instanceof ValidationException) {
            return JsonResponse::send(['errors' => $exception->errors()], trans('messages.general_errors.validation'), 422, 422);
        }
		
        return JsonResponse::send($this->getDataField($exception), trans('messages.general_errors.unknown'), 500, 500);
    }

    /**
     * @param Exception $exception
     * @return array
     */
    private function getDataField(\Exception $exception)
    {
        if ($exception->getMessage())
        {
            return [
                'message' => $exception->getMessage()
            ];
        }

        return [];
    }
}
