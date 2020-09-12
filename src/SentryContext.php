<?php

declare(strict_types=1);
/**
 *
 *
 * @author    耐小心 <i@naixiaoixn.com>
 * @time      2020/9/3 5:00 下午
 *
 * @copyright 2020 耐小心
 */

namespace Naixiaoxin\HyperfSentry;

use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use Sentry\ClientBuilderInterface;
use Sentry\State\HubInterface;

final class SentryContext
{
    const SENTRY_HUB = 'hyperf.sentry_hub';

    /**
     * @return HubInterface
     */
    public static function getHub(): HubInterface
    {
        if (!Context::has(static::SENTRY_HUB)) {
            $hub = self::getHubInstance();
            $hub->bindClient(self::getClientBuilder()->getClient());
            Context::set(static::SENTRY_HUB, $hub);
        }

        return Context::get(static::SENTRY_HUB);
    }

    /**
     * Set Hub
     *
     * @param HubInterface $hub
     */
    public static function setHub(HubInterface $hub)
    {
        Context::set(static::SENTRY_HUB,$hub);
    }

    private static function getHubInstance(): HubInterface
    {
        return make(HubInterface::class);
    }

    private static function getClientBuilder(): ClientBuilderInterface
    {
        if (!ApplicationContext::hasContainer()) {
            throw new \RuntimeException('no container');
        }
        return ApplicationContext::getContainer()->get(ClientBuilderInterface::class);
    }
}