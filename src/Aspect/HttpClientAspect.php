<?php

declare(strict_types=1);
/**
 *
 *
 * @author    耐小心 <i@naixiaoixn.com>
 * @time      2020/9/12 4:10 下午
 *
 * @copyright 2020 耐小心
 */

namespace Naixiaoxin\HyperfSentry\Aspect;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AroundInterface;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Naixiaoxin\HyperfSentry\Integration;
use Psr\Http\Message\ResponseInterface;
use Sentry\Breadcrumb;

/**
 * Class HttpClientAspect
 *
 * @Aspect()
 * @package Naixiaoxin\HyperfSentry\Aspect
 */
class HttpClientAspect implements AroundInterface
{


    public $classes
        = [
            Client::class . '::requestAsync',
            //Client::class . '::request',
        ];

    protected $config;

    public function __construct(ConfigInterface  $config)
    {
        $this->config = $config;
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        if (!$this->config->get("sentry.breadcrumbs.guzzle",false)){
            return $proceedingJoinPoint->process();
        }

        $options = $proceedingJoinPoint->arguments['keys']['options'];
        // 允许设置当前不被采集
        if (isset($options['no_aspect']) && $options['no_aspect'] === true) {
            return $proceedingJoinPoint->process();
        }
        $startTime = microtime(true);
        $arguments                  = $proceedingJoinPoint->arguments;
        $uri                        = $arguments['keys']['uri'] ?? '';
        $data["request"]["method"]  = $options['method'] ?? 'GET';
        $data["request"]["headers"] = $options["headers"] ?? [];
        $data["request"]["query"] = $options["query"] ?? [];
        /** @var PromiseInterface $result */
        $result                     = $proceedingJoinPoint->process();
        $data["time"] = (microtime(true)-$startTime)*1000;
        Integration::addBreadcrumb(new Breadcrumb(
            Breadcrumb::LEVEL_INFO,
            Breadcrumb::TYPE_DEFAULT,
            'guzzle',
            $uri,
            $data
        ));
        return $result;

    }

}