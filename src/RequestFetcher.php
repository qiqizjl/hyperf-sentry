<?php

declare(strict_types=1);
/**
 *
 *
 * @author    耐小心 <i@naixiaoixn.com>
 * @time      2020/9/7 6:33 下午
 *
 * @copyright 2020 耐小心
 */

namespace Naixiaoxin\HyperfSentry;

use Hyperf\Utils\ApplicationContext;
use Psr\Http\Message\ServerRequestInterface;
use Sentry\Integration\RequestFetcherInterface;

class RequestFetcher implements RequestFetcherInterface
{
    public function fetchRequest(): ?ServerRequestInterface
    {
        if (ApplicationContext::hasContainer()) {
            return ApplicationContext::getContainer()->get(ServerRequestInterface::class);
        }
        return null;
    }
}