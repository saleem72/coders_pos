<?php

namespace App\Exceptions;

use Throwable;

use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
    public function register(): void
    {
        $this->renderable(function (Throwable $e, Request $request) {
            return $this->handleException($request, $e);
        });
    }

    protected function handleException($request, Throwable $exception)
    {
        if($exception instanceof NotFoundHttpException) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'status_code' => 404,
                    'message' => 'Record not found.',
                    'data' => Null
                ], 404);

            }
        }

        if($exception instanceof ValidationException) {
            if ($request->is('api/*')) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Validation error',
                        'data' => Null,
                        'status_code' => $exception->status,
                        'errors' => $exception->validator->errors()->all()
                    ],
                    $exception->status
                );

            }
        }
        //
        if($exception instanceof AccessDeniedHttpException) {
            if ($request->is('api/*')) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'This action is unauthorized for you.',
                        'data' => Null,
                        'status_code' => 403
                    ],
                    403
                );

            }
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            if ($request->is('api/*')) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => $exception->getMessage(),
                        'data' => Null,
                        'status_code' => 405
                    ],
                    405
                );

            }
        }
    }
}
