<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Contract\ChatServiceInterface;
use App\Middleware\ResponseHandlerMiddleware;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class IndexController extends AbstractController
{
    #[Inject]
    protected ChatServiceInterface $chatService;

    #[GetMapping(path: "/")]
    public function index(RenderInterface $render): ResponseInterface
    {
        return $render->render('index');
    }

    #[Middleware(ResponseHandlerMiddleware::class)]
    #[GetMapping(path: "/init")]
    public function init(): array
    {
        return $this->chatService->getInitContext();
    }

    #[Middleware(ResponseHandlerMiddleware::class)]
    #[PostMapping(path: "/chat")]
    public function chat(): array
    {
        return $this->chatService->chat($this->request->post('context'), $this->request->post('temperature', 1));
    }
}
