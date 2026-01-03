<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
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
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // 验证错误
        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $exception->errors()
            ], 422);
        }

        // 模型未找到
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => '资源未找到'
            ], 404);
        }

        // HTTP异常
        if ($exception instanceof HttpException) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage() ?: 'HTTP错误'
            ], $exception->getStatusCode());
        }

        // 其他异常
        if (env('APP_DEBUG')) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ], 500);
        }

        return response()->json([
            'success' => false,
            'message' => '服务器错误'
        ], 500);
    }
}


