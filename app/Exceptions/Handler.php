<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;
use Illuminate\Support\Facades\Log;

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
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle API exceptions
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                return $this->handleApiException($e, $request);
            }
        });
    }

    /**
     * Handle API exceptions.
     */
    protected function handleApiException(Throwable $e, Request $request)
    {
        // Log the exception
        Log::error('API Exception', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => $request->user()?->id,
            'ip' => $request->ip(),
        ]);

        // Handle specific exceptions
        if ($e instanceof ValidationException) {
            return $this->handleValidationException($e);
        }

        if ($e instanceof AuthenticationException) {
            return $this->handleAuthenticationException($e);
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($e);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->handleNotFoundException($e);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->handleMethodNotAllowedException($e);
        }

        if ($e instanceof TooManyRequestsHttpException) {
            return $this->handleTooManyRequestsException($e);
        }

        // Handle general exceptions
        return $this->handleGeneralException($e);
    }

    /**
     * Handle validation exceptions.
     */
    protected function handleValidationException(ValidationException $e)
    {
        $errors = $e->errors();
        $firstError = collect($errors)->first();
        $firstErrorMessage = is_array($firstError) ? $firstError[0] : $firstError;

        return response()->json([
            'success' => false,
            'message' => $firstErrorMessage,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Handle authentication exceptions.
     */
    protected function handleAuthenticationException(AuthenticationException $e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated. Please login to continue.',
        ], 401);
    }

    /**
     * Handle model not found exceptions.
     */
    protected function handleModelNotFoundException(ModelNotFoundException $e)
    {
        return response()->json([
            'success' => false,
            'message' => 'The requested resource was not found.',
        ], 404);
    }

    /**
     * Handle not found exceptions.
     */
    protected function handleNotFoundException(NotFoundHttpException $e)
    {
        return response()->json([
            'success' => false,
            'message' => 'The requested endpoint was not found.',
        ], 404);
    }

    /**
     * Handle method not allowed exceptions.
     */
    protected function handleMethodNotAllowedException(MethodNotAllowedHttpException $e)
    {
        return response()->json([
            'success' => false,
            'message' => 'The HTTP method is not allowed for this endpoint.',
        ], 405);
    }

    /**
     * Handle too many requests exceptions.
     */
    protected function handleTooManyRequestsException(TooManyRequestsHttpException $e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Too many requests. Please try again later.',
        ], 429);
    }

    /**
     * Handle general exceptions.
     */
    protected function handleGeneralException(Throwable $e)
    {
        $statusCode = 500;
        $message = 'An unexpected error occurred.';

        // In development, show more details
        if (config('app.debug')) {
            $message = $e->getMessage();
        }

        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}
