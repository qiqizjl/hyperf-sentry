<?php

declare(strict_types=1);
/**
 *
 *
 * @author    耐小心 <i@naixiaoixn.com>
 * @time      2020/9/3 4:25 下午
 *
 * @copyright 2020 耐小心
 */

namespace Naixiaoxin\HyperfSentry\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Naixiaoxin\HyperfSentry\Integration;
use Naixiaoxin\HyperfSentry\SentryContext;
use Sentry\Breadcrumb;

/**
 * Class DbQueryListener
 *
 * @package Naixiaoxin\HyperfSentry\Listener
 */
class DbQueryListener implements ListenerInterface
{


    protected $config;

    public function __construct(ConfigInterface  $config)
    {
        $this->config = $config;
    }


    /**
     * 监听事件
     *
     * @return array
     */
    public function listen(): array
    {
        return [
            QueryExecuted::class,
        ];
    }

    /**
     * SQL执行
     *
     * @param object $event
     */
    public function process(object $event)
    {
        if (!$this->config->get("sentry.breadcrumbs.mysql",false)){
            return $proceedingJoinPoint->process();
        }

        if ($event instanceof QueryExecuted) {
            $data["connectionName"] = $event->connectionName;
            $data["time"]           = $event->time;
            $data["bindings"]       = $event->bindings;
            Integration::addBreadcrumb(new Breadcrumb(
                Breadcrumb::LEVEL_INFO,
                Breadcrumb::TYPE_DEFAULT,
                'sql.query',
                $event->sql,
                $data
            ));
        }


    }

}