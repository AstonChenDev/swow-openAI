<?php
declare(strict_types=1);


namespace App\Middleware;

use App\Common\Res;
use App\Constants\ErrorCode;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Di\Annotation\Inject;

/**
 * 统一响应json中间件
 * Class ResponseHandlerMiddleware
 * @package App\Middleware
 */
class ResponseHandlerMiddleware implements MiddlewareInterface
{
    /**
     * @Inject()
     * @var HttpResponse
     */
    protected $response;

    /**
     * Notes: 获取控制器响应 做json处理
     * User: 陈朋
     * DateTime: 2021/6/30 18:45
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * request请求数据处理
         */
        try {
            $response = $handler->handle($request);
        } catch (\Throwable $throwable) {
            return Res::respondError(ErrorCode::SERVER_ERROR, ErrorCode::getMessage(ErrorCode::SERVER_ERROR), [
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'msg' => $throwable->getMessage(),
                'trace' => $throwable->getTrace(),
            ]);
        }
        $res = $response->getBody();
        $body = json_decode((string)$res);
        switch (true) {
            case is_null($body):
                $body = (string)$res;
                break;
        }
        /**
         * Response返回数据，封装处理
         */
        if (isset($body->error_code)) {
            abort($body->error_code);
        }
        return Res::respondSuccess($body);
    }
}
