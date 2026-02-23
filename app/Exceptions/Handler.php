<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

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
        $this->renderable(function (TokenMismatchException $e, $request) {

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session expired. Please login again.'
                ], 419);
            }

            return redirect()
                ->route('login') // make sure your login route name is correct
                ->with('error', 'Your session expired. Please login again.');
        });
    }
}
