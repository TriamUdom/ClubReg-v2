<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler {
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = array(
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    );
    
    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry etc.
     *
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception) {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception               $exception
     * @return \Illuminate\Http\Response|Response
     */
    public function render($request, Exception $exception) {
        if ($exception instanceof TokenMismatchException) {
            // CSRF Token Mismatch
            return response()->view('errors.custom', array(
                'title' => 'Form token mismatch',
                'description' => 'ดูเหมือนว่าคุณไม่ได้กดส่งฟอร์มเป็นเวลานานเกินไป กรุณาลองใหม่',
                'button' => '<a href="/" class="waves-effect waves-light btn indigo darken-3 tooltipped center-align"
       style="width:80%;max-width:350px;margin-top:20px">ไปยังหน้าหลัก</a>'
            ));
        } else {
            $response = parent::render($request, $exception);
            
            if (!config('app.debug')) {
                // Debug is off, Try to return nice exception
                if ($response->getStatusCode() == Response::HTTP_INTERNAL_SERVER_ERROR) {
                    if ($exception instanceof UserFriendlyException) {
                        if ($exception->getDescription()) {
                            return response()->view('errors.exception', array('title' => $exception->getMessage(), 'description' => $exception->getDescription()));
                        }
                        
                        return response()->view('errors.exception', array('title' => $exception->getMessage(), 'code' => date(\DateTime::ISO8601)));
                    } elseif ($exception instanceof \PDOException) {
                        return response()->view('errors.exception', array('title' => 'Database Failure', 'description' => 'ประสบปัญหาในการติดต่อฐานข้อมูล โปรดรอแล้วลองใหม่'));
                    }
                    
                    return response()->view('errors.exception', array('title' => get_class($exception), 'code' => date(\DateTime::ISO8601)));
                }
            }
            
            return $response;
        }
    }
    
    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request                 $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception) {
        if ($request->expectsJson()) {
            return response()->json(array('error' => 'Unauthenticated.'), 401);
        }
        
        return redirect()->guest('login');
    }
}
