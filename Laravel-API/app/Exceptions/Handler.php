<?php

namespace Rhf\Exceptions;

use Exception;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sentry\Laravel\Facade as Sentry;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [];

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
     * @param \Throwable $exception
     * @return void
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if (
            !empty($exception->getMessage()) &&
            ($exception->getCode() || method_exists($exception, 'hasErrors') && $exception->hasErrors())
        ) {
            $error = [
                'message' => $exception->getMessage()
            ];

            if (method_exists($exception, 'hasErrors') && $exception->hasErrors()) {
                $error['errors'] = $exception->getErrors();
            }

            Sentry::captureException($exception);
            if ($exception->getCode()) {
                return response()->json($error, $exception->getCode());
            }

            return response()->json($error);
        }

        return parent::render($request, $exception);
    }

    /**
     * Prepare exception for rendering.
     *
     * @param  \Throwable  $e
     * @return \Exception
     */
    protected function prepareException(Throwable $e)
    {
        // Overrides default ModelNotFoundException handling to remove additional detail that is returned
        if ($e instanceof ModelNotFoundException) {
            return new NotFoundHttpException('Model not found', $e);
        }
        return parent::prepareException($e);
    }
}
