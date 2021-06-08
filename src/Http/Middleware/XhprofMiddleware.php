<?php declare(strict_types=1);

namespace TinyFramework\Xhprof\Http\Middleware;

use Closure;
use TinyFramework\Http\Request;
use TinyFramework\Http\Response;

class XhprofMiddleware
{

    public function handle(Request $request, Closure $next, ...$parameters): Response
    {
        $key = $request->get('key');
        if (!$key || !hash_equals(hash('sha512', config('app.secret')), $key)) {
            return Response::error(403);
        }
        return $next($request);
    }

}
