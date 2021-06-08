<?php declare(strict_types=1);

namespace TinyFramework\Xhprof\Http\Controllers;

use TinyFramework\Xhprof\Supports\Xhprof;
use TinyFramework\Http\Response;

class XhprofController
{

    public function index()
    {
        $dumps = array_map(function ($dump) {
            return explode('.', basename($dump))[0];
        }, glob(config('xhprof.dir') . '/*.xhprof'));
        return view('xhprof.index', compact('dumps'));
    }

    public function show(string $id)
    {
        $file = config('xhprof.dir') . '/' . $id . '.xhprof';
        if (!file_exists($file)) {
            return Response::error(404);
        }
        $xhprof = new Xhprof(json_decode(file_get_contents($file), true));
        return view('xhprof.show', compact('xhprof'));
    }

}
