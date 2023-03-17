<?php

use App\Components\Log\Dump;
use App\Exception\BusinessException;
use Hyperf\Context\Context;
use Hyperf\Utils\Coroutine;
use Swoole\Websocket\Frame;
use Hyperf\Server\ServerFactory;
use Hyperf\Utils\ApplicationContext;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Server;
use Swoole\WebSocket\Server as WebSocketServer;

if (!function_exists('container')) {
    function container()
    {
        return ApplicationContext::getContainer();
    }
}

if (!function_exists('redis')) {
    //获取REDIS实例
    function redis($db_name = 'default')
    {
        return container()->get(\Hyperf\Redis\RedisFactory::class)->get($db_name);
    }
}

if (!function_exists('server')) {
    function server()
    {
        return container()->get(ServerFactory::class)->getServer()->getServer();
    }
}
if (!function_exists('frame')) {
    function frame()
    {
        return container()->get(Frame::class);
    }
}
if (!function_exists('websocket')) {
    function websocket()
    {
        return container()->get(WebSocketServer::class);
    }
}

if (!function_exists('localServerId')) {
    function localServerId($fd): string
    {
        return config('async_queue.local.channel') . '_' . $fd;
    }
}

if (!function_exists('response')) {
    function response()
    {
        return container()->get(\Hyperf\HttpServer\Contract\ResponseInterface::class);
    }
}

if (!function_exists('request')) {
    /**
     * Notes: 获取协程请求实例
     * User: 陈朋
     * DateTime: 2021/7/2 15:10
     * @return mixed|ServerRequestInterface
     */
    function request()
    {
        return container()->get(ServerRequestInterface::class);
    }
}

if (!function_exists('ip')) {
    /**
     * Notes: 获取客户端IP
     * User: 陈朋
     * DateTime: 2021/7/2 15:07
     * @return string
     */
    function ip(): string
    {
        return getHeader('x-forwarded-for');
    }
}

if (!function_exists('isProd')) {
    /**
     * Notes: 判断是否生产环境 关闭日志 去掉调试参数等等
     * User: Tom
     * DateTime: 2021/7/27 15:07
     * @return bool
     */
    function isProd(): bool
    {
        return config('app_env') === 'production';
    }
}

if (!function_exists('isLocal')) {
    /**
     * Notes: 判断是否是本地环境 关闭日志 去掉调试参数等等
     * User: Tom
     * DateTime: 2021/7/27 15:07
     * @return bool
     */
    function isLocal(): bool
    {
        return config('app_env') === 'local';
    }
}

if (!function_exists('getClientInfo')) {
    /**
     * Notes: 获取请求客户端信息，获取连接的信息
     * User: 陈朋
     * DateTime: 2021/7/2 15:07
     * @return array
     */
    function getClientInfo(): array
    {
        // 得从协程上下文取出请求
        $request = Context::get(ServerRequestInterface::class);
        $server = make(Server::class);
        return $server->getClientInfo($request->getSwooleRequest()->fd);
    }
}

if (!function_exists('getHeader')) {
    /**
     * Notes: 获取指定头部字段
     * User: 陈朋
     * DateTime: 2021/7/2 15:04
     * @param string $name
     * @param string $default
     * @return string
     */
    function getHeader(string $name, string $default = ''): string
    {
        try {
            $header = Context::get(Business::HEADERS, request()->getHeaders());
            $value = $header[strtolower($name)] ?? [];
            if ($value) {
                return $value[0];
            }
            return $default;
        } catch (\Throwable $exception) {
            return $default;
        }
    }
}

if (!function_exists('buildUniqId')) {
    function buildUniqId($prefix = '')
    {
        $coroutineId = Coroutine::id();
        $coroutineId = posix_getpid() . str_pad(substr($coroutineId, -4), 4, 0, STR_PAD_LEFT);
        $coroutineId = str_pad($coroutineId, 9, '0', STR_PAD_LEFT);
        $ticket = date('YmdHis') . mt_rand(1000, 9999);
//        $ticket = str_replace('.', '', uniqid((string)mt_rand(100000, 999999), true));
        $ticket = $prefix . $ticket . $coroutineId;
        return $ticket;
    }
}

if (!function_exists('dump')) {
    function dump(...$var): Dump
    {
        $dump = container()->get(Dump::class);
        if (!empty($var)) {
            $dump->info(...$var);
        }
        return $dump;
    }
}

if (!function_exists('highlight')) {
    function highlight(...$var): Dump
    {
        $dump = container()->get(Dump::class);
        if (!empty($var)) {
            $dump->highlight(...$var);
        }
        return $dump;
    }
}

/**
 * 将字符串转换成二进制
 * @param type $str
 * @return type
 */
if (!function_exists('strToBin')) {
    function strToBin($str)
    {
        //1.列出每个字符
        $arr = preg_split('/(?<!^)(?!$)/u', $str);
        //2.unpack字符
        foreach ($arr as &$v) {
            $temp = unpack('H*', $v);
            $v = base_convert($temp[1], 16, 2);
            unset($temp);
        }
        return join(' ', $arr);
        //return $arr;
    }
}
/**
 * 讲二进制转换成字符串
 * @param type $str
 * @return type
 */
if (!function_exists('binToStr')) {
    function binToStr($str)
    {
        $arr = explode(' ', $str);
        foreach ($arr as &$v) {
            $v = pack("H" . strlen(base_convert($v, 2, 16)), base_convert($v, 2, 16));
        }
        return join('', $arr);
    }
}

if (!function_exists('abort')) {
    /**
     * Notes: 抛出BusinessException异常
     * User: caijw
     * DateTime: 2021/9/18 10:10
     * @param int $code
     * @param array $param
     * @throws BusinessException
     */
    function abort(int $code): void
    {
        if ($code == 200) {
            return;
        }
        throw new BusinessException($code);
    }
}

if (!function_exists('arrOrderBy')) {
    /**
     * Notes: 二维数组排序 orderBy
     * User: 陈朋
     * DateTime: 2021/9/17 16:25
     * @param array $array 数组
     * @param string $field 排序字段
     * @param int $sort 排序方式
     * @return array
     */
    function arrOrderBy(array $array, string $field, int $sort = SORT_DESC): array
    {
        array_multisort(array_column($array, $field), $sort, $array);
        return $array;
    }
}

if (!function_exists('getClassName')) {
    /**
     * Notes: 从::class中获取当前class类名，不含命名空间
     * User: caijw
     * DateTime: 2021/12/17 18:09
     * @param string $class
     * @return string
     */
    function getClassName(string $class): string
    {
        $arr = explode("\\", $class);
        return end($arr);
    }
}

if (!function_exists('sumIsometricSequence')) {
    /**
     * Notes: 等差数列求和
     * User: 陈朋
     * DateTime: 2022/9/8 14:51
     * @param float $first 首项
     * @param float $d 公差
     * @param float $last 最大项
     * @return float
     */
    function sumIsometricSequence(float $first, float $d, float $last): float
    {    //$a1为首项,$d为公差,$an为最大项
        $sum = 0;
        $i = $first;
        a:
        $sum += $i;
        $i += $d;
        if ($i <= $last) goto a;
        return $sum;
    }
}

if (!function_exists('sequenceItemCount')) {
    /**
     * Notes: 给定数字 求出是等差数列几项的和
     * User: 陈朋
     * DateTime: 2022/9/8 14:51
     * @param float $first 首项
     * @param float $d 公差
     * @param int $sum 和
     * @return int
     */
    function sequenceItemCount(float $first, float $d, int $sum): int
    {
        $index = 1;
        while (sumIsometricSequence($first, $d, $index) <= $sum) {
            $index++;
        }
        return --$index;
    }
}

if (!function_exists('dumpException')) {
    /**
     * Notes:
     * User: 陈朋
     * DateTime: 2022/4/26 14:03
     * @param Throwable $error
     * @param string $tag
     * @param array $attach
     */
    function dumpException(\Throwable $error, string $tag = '', array $attach = []): void
    {
        dump()->error([
            'class' => get_class($error),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTrace(),
            'msg' => $error->getMessage(),
            'tag' => $tag,
            'attach' => $attach,
        ]);
    }
}
if (!function_exists('unzip_gz')) {
    /**
     * Notes: 解压GZ文件
     * User: 陈朋
     * DateTime: 2022/9/14 10:42
     * @param $gz_file
     * @return void
     */
    function unzip_gz($gz_file)
    {
        $buffer_size = 4096; // read 4kb at a time
        $out_file_name = str_replace('.gz', '', $gz_file);
        $file = gzopen($gz_file, 'rb');
        $out_file = fopen($out_file_name, 'wb');
        while (!gzeof($file)) {
            fwrite($out_file, gzread($file, $buffer_size));
        }
        fclose($out_file);
        gzclose($file);
    }
}

if (!function_exists('getCsvData')) {
    /**
     * Notes: 读取csv文件
     * User: 陈朋
     * DateTime: 2022/9/14 11:30
     * @param string $filePath
     * @return array
     */
    function getCsvData(string $filePath): array
    {
        $handle = fopen($filePath, "rb");
        $data = [];
        while (!feof($handle)) {
            $data[] = fgetcsv($handle);
        }
        fclose($handle);
        //字符转码操作
        return eval('return ' . iconv('gb2312', 'utf-8', var_export($data, true)) . ';');
    }
}

if (!function_exists('scToNum')) {
    /**
     * Notes: 科学计数法转数字
     * User: 陈朋
     * DateTime: 2022/9/21 16:36
     * @param $num
     * @param int $double
     * @return string
     */
    function scToNum($num, int $double = 5): string
    {
        if (false !== stripos($num, "e")) {
            $a = explode("e", strtolower($num));
            return bcmul($a[0], bcpow(10, $a[1], $double), $double);
        } else {
            return (string)$num;
        }
    }
}