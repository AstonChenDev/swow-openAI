<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Context\Context;
use Hyperf\HttpMessage\Stream\SwooleFileStream;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Utils\Str;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResourceMiddleware implements MiddlewareInterface
{

    protected string $direction;

    public function __construct(protected ContainerInterface $container)
    {
        $this->direction = config('server.settings.document_root');
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Dispatched $dispatched */
        $dispatched = $request->getAttribute(Dispatched::class);
        var_dump($dispatched);
        if ($dispatched->status) {
            return $handler->handle($request);
        }
        $path = $request->getUri()->getPath();
        var_dump($path);
        if (Str::contains($path, '.')) {
            $file = $this->direction . $path;
            if (is_file($file)) {
                return Context::get(ResponseInterface::class)->withBody(new SwooleStream(file_get_contents($file)));
            }
        }
        return $handler->handle($request);
    }
}
