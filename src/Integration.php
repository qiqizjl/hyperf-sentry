<?php

declare(strict_types=1);
/**
 *
 *
 * @author    耐小心 <i@naixiaoixn.com>
 * @time      2020/9/7 1:58 上午
 *
 * @copyright 2020 耐小心
 */

namespace Naixiaoxin\HyperfSentry;

use Sentry\Breadcrumb;
use Sentry\Event;
use Sentry\Integration\IntegrationInterface;
use Sentry\State\Scope;

class Integration implements IntegrationInterface
{


    public function setupOnce(): void
    {
        Scope::addGlobalEventProcessor(function (Event $event): Event {
            $self = SentryContext::getHub()->getIntegration(self::class);

            if (!$self instanceof self) {
                return $event;
            }

            $event->setContext("swoole",[
                "version"=>SWOOLE_VERSION,
            ]);

            return $event;
        });
    }

    /**
     * Adds a breadcrumb if the integration is enabled for Laravel.
     *
     * @param Breadcrumb $breadcrumb
     */
    public static function addBreadcrumb(Breadcrumb $breadcrumb): void
    {
        $self = SentryContext::getHub()->getIntegration(self::class);

        if (!$self instanceof self) {
            return;
        }

        $sentry = SentryContext::getHub();
        $sentry->addBreadcrumb($breadcrumb);
        SentryContext::setHub($sentry);
    }
}