<?php

declare(strict_types=1);

namespace App\Components\Log;

/**
 * Class Dump
 * User: caijw
 * DateTime: 2022/4/12 11:38
 * @method void debug(...$var)
 * @method void info(...$var)
 * @method void error(...$var)
 * @method void highlight(...$var)
 * @method void aliyun(...$var)
 * @method void socketOnOpen(...$var)
 * @method void socketOnMessage(...$var)
 * @method void socketPushMessage(...$var)
 * @method void socketOnClose(...$var)
 * @package App\Components\Log
 */
class Dump
{
    const DEBUG = 1;
    const INFO = 2;
    const ERROR = 3;
    const HIGHLIGHT = 4;

    public function __call($method, $arguments)
    {
        $func = [
            'debug' => self::DEBUG,
            'info' => self::INFO,
            'error' => self::ERROR,
            'highlight' => self::HIGHLIGHT,
            'aliyun' => self::DEBUG,
            'socketOnOpen' => self::DEBUG,
            'socketOnMessage' => self::DEBUG,
            'socketPushMessage' => self::DEBUG,
            'socketOnClose' => self::DEBUG,
        ];
        $level = $func[$method] ?? 0;
        if (empty($level)) {
            throw new \BadMethodCallException(sprintf(
                'Method %s::%s does not exist.', static::class, $method
            ));
        }
        if (isProd() && $level != self::ERROR) {
            return;
        }
        if (isLocal()) {
            ['color' => $color, 'back' => $back] = [
                self::DEBUG => ['color' => 33, 'back' => ""],
                self::INFO => ['color' => 36, 'back' => ""],
                self::ERROR => ['color' => 37, 'back' => "\033[41m"],
                self::HIGHLIGHT => ['color' => 31, 'back' => "\033[47m"],
            ][$level] ?? ['color' => 45, 'back' => ""];
            $title = "[\033[0;{$color}m{$back}{$method}\033[0m]";

        } else {
            $title = "[{$method}]";
        }

        $content = [];
        foreach ($arguments as $argument) {
            $content[] = is_string($argument) ? $argument : json_encode($argument, JSON_UNESCAPED_UNICODE);
        }
        printf("%s %s %s\n", date('Y/m/d H:i:s'), $title, implode("===", $content));
    }
}
