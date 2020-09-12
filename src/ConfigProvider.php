<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Naixiaoxin\HyperfSentry;

use Naixiaoxin\HyperfSentry\Listener\DbQueryListener;
use Naixiaoxin\HyperfSentry\Sentry\ClientBuilder;
use Naixiaoxin\HyperfSentry\Sentry\Hub;
use Sentry\ClientBuilderInterface;
use Sentry\State\HubInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                ClientBuilderInterface::class => ClientBuilder::class,
                HubInterface::class           => Hub::class,

            ],
            'commands'     => [
            ],
            'listeners' => [
                DbQueryListener::class
            ],
            'annotations'  => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
        ];
    }
}
