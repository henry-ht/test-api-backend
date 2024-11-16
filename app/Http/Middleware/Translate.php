<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Translate
{
    const SESSION_KEY = 'locale';
    const LOCALES = ['es','en'];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Session::has(self::SESSION_KEY)) {
            App::setLocale(Session::get(self::SESSION_KEY));
        } else if(!empty($request->server('HTTP_ACCEPT_LANGUAGE'))){
            $userLangs = preg_split('/,|;/', $request->server('HTTP_ACCEPT_LANGUAGE'));

            foreach (self::LOCALES as $lang) {
                if(in_array($lang, $userLangs)) {
                    App::setLocale($lang);
                    Session::push(self::SESSION_KEY, $lang);
                    break;
                }
            }
        }

        return $next($request);
    }
}
