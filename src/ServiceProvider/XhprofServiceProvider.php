<?php declare(strict_types=1);

namespace TinyFramework\Xhprof\ServiceProvider;

use TinyFramework\Core\ConfigInterface;
use TinyFramework\Http\HttpKernel;
use TinyFramework\Http\Request;
use TinyFramework\Http\Response;
use TinyFramework\Http\Router;
use TinyFramework\Template\Blade;
use TinyFramework\Xhprof\Http\Middleware\XhprofMiddleware;
use TinyFramework\ServiceProvider\ServiceProviderAwesome;
use TinyFramework\Xhprof\Http\Controllers\XhprofController;

class XhprofServiceProvider extends ServiceProviderAwesome
{

    public function register(): void
    {
        /** @var ConfigInterface $config */
        $config = $this->container->get(ConfigInterface::class);
        if ($config->get('xhprof.enabled') === null) {
            $config->load('xhprof', __DIR__ . '/../Config/xhprof.php');
        }

        /** @var Router $router */
        $router = $this->container->get(Router::class);
        $router->group(['middleware' => XhprofMiddleware::class], function (Router $router) {
            $router->get('__xhprof', XhprofController::class . '@index')->name('xhprof.index');
            $router->get('__xhprof/{id}', XhprofController::class . '@show')
                ->name('xhprof.show')->pattern('id', '[^/]+');
        });

        /** @var Blade $blade */
        $blade = $this->container->get(Blade::class);
        $blade->addNamespaceDirectory('xhprof', __DIR__ . '/../Resources/views');

        /** @var HttpKernel $kernel */
        $kernel = $this->container->get('kernel');
        if ($kernel->runningInConsole()) {
            return;
        }

        if (!function_exists('xhprof_enable')) {
            return;
        }

        $kernel->terminateRequestCallback(function (Request $request, Response $response) use ($config) {
            $dir = $config->get('xhprof.dir');
            $expire = time() - $config->get('xhprof.expire') ?? 60 * 60 * 24 * 7; // 7 days default
            if (function_exists('tideways_xhprof_disable')) {
                $data = tideways_xhprof_disable();
            } elseif (function_exists('xhprof_disable')) {
                $data = xhprof_disable();
            } else {
                return;
            }

            if (strpos($request->url()->path(), '/__xhprof') === 0) {
                return;
            }

            /**
             * @see https://github.com/bbc/programmes-xhprof/blob/master/Document/XhguiRuns.php#L51
             */
            $end = microtime(true);
            $data = [
                '_id' => $request->id() . '-' . $response->id(),
                'meta' => [
                    'url' => $request->url()->userInfo('', '')->fragment('')->__toString(),
                    'SERVER' => $request->server(),
                    'get' => $request->get(),
                    'env' => [],
                    'simple_url' => $request->url()->userInfo('', '')->fragment('')->__toString(),
                    'request_ts' => round(TINYFRAMEWORK_START),
                    'request_ts_micro' => [
                        'sec' => (int)TINYFRAMEWORK_START,
                        'usec' => (TINYFRAMEWORK_START - (int)TINYFRAMEWORK_START) * 10000
                    ],
                    'request_date' => date('Y-m-d\TH:i:sP', (int)TINYFRAMEWORK_START),
                    'request_duration' => $end - TINYFRAMEWORK_START,

                    'request_id' => $request->id(),
                    'request_method' => $request->method(),

                    'response_id' => $response->id(),
                    'response_code' => $response->code(),
                    'response_ts' => round($end),
                    'response_ts_micro' => [
                        'sec' => (int)$end,
                        'usec' => ($end - (int)$end) * 10000
                    ],
                    'response_date' => date('Y-m-d\TH:i:sP', (int)$end),
                ],
                'profile' => $data,
            ];
            $file = $request->id() . '-' . $response->id() . '.xhprof';
            if (!is_dir($dir)) {
                mkdir($dir, 0750, true);
            }
            file_put_contents($dir . '/' . $file, json_encode($data, JSON_PRETTY_PRINT));
            chmod($dir . '/' . $file, 0640);
            tmpreaper($dir, $expire);
        });

        \xhprof_enable(
            XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY,
            [
                'ignored_functions' => [
                    'call_user_func',
                    'call_user_func_array',
                    'Composer\Autoload\ClassLoader::loadClass',
                    'Composer\Autoload\includeFile',
                    'TinyFramework\Core\Container::resolveAlias',
                    'TinyFramework\Core\Container::call',
                    'TinyFramework\Core\Container::callConstruct',
                    'TinyFramework\Core\Container::callFunction',
                    'TinyFramework\Core\Container::callMethod',
                    'TinyFramework\Core\Container::buildArgumentsByParameters',
                ]
            ]
        );
    }

}
