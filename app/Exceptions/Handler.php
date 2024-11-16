<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        // $this->renderable(function (Throwable $e, $request) {
        //     $accept = $request->header('Accept');
        //     $contentType = $request->header('Content-Type');

        //     dump($accept);
        //     dump($contentType);
        //     if ($request->is('api/*')) {

        //         $classString    = get_class($e);
        //         $classSplit     = explode("\\", $classString);
        //         $count          = count($classSplit);
        //         $class          = $classSplit[$count-1];
        //         $notify         = false;
        //         $statusCode     = 200;

        //         dump($class);
        //         switch ($class) {

        //             case 'AuthorizationException':
        //                 // $message    = __('Authorization exception');
        //                 $message    = 'Authorization exception';
        //                 $status     = 'error';
        //                 $data       = false;
        //                 $statusCode = 403;

        //                 break;

        //             case 'AccessDeniedHttpException':
        //                 // $message    = __('Access denied http exception');
        //                 $message    = 'Access denied';
        //                 $status     = 'error';
        //                 $data       = false;
        //                 $notify     = true;
        //                 $statusCode = 403;
                        
        //                 break;

        //             case 'AuthenticationException':

        //                 // $message    = __('Authentication exception');
        //                 $message    = 'Authentication exception';
        //                 $status     = 'error';
        //                 $data       = false;
        //                 $notify     = false;
        //                 $statusCode = 403;
                        
        //                 break;

        //             case 'NotFoundHttpException':

        //                 // $message    = __('Not found http exception');
        //                 $message    = 'Not found http exception';
        //                 $status     = 'error';
        //                 $data       = false;
        //                 $statusCode = 404;
                        
        //                 break;

        //             case 'Swift_TransportException':

        //                 // $message    = __("Email Transport Exception");
        //                 $message    = "Email Transport Exception";
        //                 $status     = 'error';
        //                 $data       = false;
        //                 $statusCode = 403;
                        
        //                 break;

        //             case 'ModelNotFoundException':

        //                 // $message    = __('Model not found exception');
        //                 $message    = 'Model not found exception';
        //                 $status     = 'error';
        //                 $data       = false;
        //                 $statusCode = 404;
                        
        //                 break;

        //             case 'MethodNotAllowedHttpException':

        //                 // $message    = __('Method not allowed http exception');
        //                 $message    = 'Method not allowed http exception';
        //                 $status     = 'error';
        //                 $data       = false;
        //                 $statusCode = 404;
                        
        //                 break;

        //             case 'QueryException':
        //                 // $message    = __('Query exception');
        //                 $message    = 'Query exception';
        //                 $status     = 'error';
        //                 $data       = false;
        //                 $statusCode = 403;
                        
        //                 break;

        //             case 'InvalidArgumentException':
        //                 // $message    = __('Invalid argument exception');
        //                 $message    = 'Invalid argument exception';
        //                 $status     = 'error';
        //                 $data       = false;
        //                 $statusCode = 403;
                        
        //                 break;

        //             case 'TokenMismatchException':
        //                 // $message    = __('Token mismatch exception');
        //                 $message    = 'Token mismatch exception';
        //                 $status     = 'error';
        //                 $data       = false;
        //                 $notify     = true;
        //                 $statusCode = 403;

        //                 break;

        //             case 'ErrorException':
        //                 // $message    = __('Error exception');
        //                 $message    = 'Error exception';
        //                 $status     = 'error';
        //                 $data       = false;
        //                 $statusCode = 403;
                        
        //                 break;

        //             case 'ValidationException':
        //                 $message    = 'Validation Exception';
        //                 $status     = $request->validator->fails();
        //                 $data       = $request;
        //                 $statusCode = 422;
                        
        //                 break;

        //             default:
        //                 $message = $class;
        //                 $status = 'error';
        //                 $data = false;
        //                 $statusCode = 403;

        //                 break;
        //         };

        //         return response([
        //             'data'      => $data,
        //             'status'    => $status,
        //             'notify'    => $notify,
        //             'message'   => $message
        //         ], $statusCode);

        //     }
            
        // });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
