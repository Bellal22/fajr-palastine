<?php

namespace App\Exceptions;

use Dotenv\Exception\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register()
    {
        // معالجة الأخطاء غير المتوقعة
        $this->reportable(function (Throwable $e) {
            // تسجيل الخطأ في ملف اللوق
            logger()->error('خطأ غير متوقع: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        });

        // معالجة أخطاء 404
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'المورد المطلوب غير موجود',
                    'error_code' => 'NOT_FOUND'
                ], 404);
            }

            return response()->view('errors.404', [], 404);
        });
    }

    public function render($request, Throwable $exception)
    {
        // للطلبات من API
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    private function handleApiException(Request $request, Throwable $exception): JsonResponse
    {
        $statusCode = 500;
        $message = 'حدث خطأ في الخادم';
        $errorCode = 'SERVER_ERROR';

        // معالجة أنواع مختلفة من الأخطاء
        if ($exception instanceof ValidationException) {
            $statusCode = 422;
            $message = 'بيانات غير صحيحة';
            $errorCode = 'VALIDATION_ERROR';

            return response()->json([
                'success' => false,
                'message' => $message,
                'error_code' => $errorCode,
                'errors' => $exception->errors()
            ], $statusCode);
        }

        if ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = 'المورد غير موجود';
            $errorCode = 'NOT_FOUND';
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'error_code' => $errorCode,
            'debug' => config('app.debug') ? [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace()
            ] : null
        ], $statusCode);
    }
}
