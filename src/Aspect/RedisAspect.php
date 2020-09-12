<?php

declare(strict_types=1);
/**
 *
 *
 * @author    耐小心 <i@naixiaoixn.com>
 * @time      2020/9/12 4:06 下午
 *
 * @copyright 2020 耐小心
 */
namespace Naixiaoxin\HyperfSentry\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AroundInterface;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Redis\Redis;
use Naixiaoxin\HyperfSentry\Integration;
use Sentry\Breadcrumb;

/**
 * Class RedisAspect
 *
 * @Aspect()
 * @package Naixiaoxin\HyperfSentry\Aspect
 */
class RedisAspect implements AroundInterface{


    /**
     * @var array
     */
    public $classes
        = [
            Redis::class . '::__call',
        ];

    /**
     * @var array
     */
    public $annotations = [];

    /**
     * @return mixed return the value from process method of ProceedingJoinPoint, or the value that you handled
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {

        $arguments = $proceedingJoinPoint->arguments['keys'];
        $startTime = microtime(true);
        $result = $proceedingJoinPoint->process();

        $data["result"] = $result;
        $data["arguments"] =  $arguments["arguments"];
        $data["time"] = (microtime(true)-$startTime)*1000;
        Integration::addBreadcrumb(new Breadcrumb(
            Breadcrumb::LEVEL_INFO,
            Breadcrumb::TYPE_DEFAULT,
            'redis' ,
            $arguments['name'],
            $data
        ));
        return $result;
    }
}