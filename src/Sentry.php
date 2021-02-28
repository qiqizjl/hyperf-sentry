<?php

declare(strict_types=1);
/**
 *
 *
 * @author    耐小心 <i@naixiaoixn.com>
 * @time      2020/9/20 9:00 下午
 *
 * @copyright 2020 耐小心
 */

namespace Naixiaoxin\HyperfSentry;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;
use Sentry\ClientBuilderInterface;
use Sentry\FlushableClientInterface;

/**
 * Class Sentry
 *
 * @package Naixiaoxin\HyperfSentry
 */
class Sentry
{

    public static function captureException(\Throwable $exception)
    {
        $container     = ApplicationContext::getContainer();
        $clientBuilder = $container->get(ClientBuilderInterface::class);
        $config        = $container->get(ConfigInterface::class);

        SentryContext::getHub()->captureException($exception);


        //刷新flush
        if (($client = $clientBuilder->getClient()) instanceof FlushableClientInterface) {
            $client->flush((int)$config->get('sentry.flush_timeout', 2));
        }
    }
}