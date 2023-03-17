<?php
declare(strict_types=1);

namespace App\Common;

use App\Constants\ErrorCode;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Class Res 封装统一返回
 * User: caijw
 * DateTime: 2021/9/18 10:23
 * @package App\Common
 */
class Res
{
    private static function respond(int $code, string $msg, $data): PsrResponseInterface
    {
        return response()->json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ]);
    }

    public static function respondNotFound(): PsrResponseInterface
    {
        return response()->raw('Page Not Found')->withStatus(404);
    }

    public static function respondRepeat(): PsrResponseInterface
    {
        return response()->raw('Try again later')->withStatus(503);
    }

    public static function respondError(int $code, string $msg = '', $data = []): PsrResponseInterface
    {
        return self::respond($code, $msg, $data);
    }

    public static function respondSuccess($data): PsrResponseInterface
    {
        return self::respond(ErrorCode::SUCCESS, ErrorCode::getMessage(ErrorCode::SUCCESS), $data);
    }
}

