<?php

declare(strict_types=1);
/**
 *
 *
 * @author    耐小心 <i@naixiaoixn.com>
 * @time      2020/9/7 2:12 上午
 *
 * @copyright 2020 耐小心
 */

namespace Naixiaoxin\HyperfSentry\Sentry;

use Hyperf\Contract\ConfigInterface;
use Naixiaoxin\HyperfSentry\RequestFetcher;
use Naixiaoxin\HyperfSentry\RequestIntegration;
use Naixiaoxin\HyperfSentry\Version;
use Psr\Container\ContainerInterface;
use Sentry\ClientBuilderInterface;

class ClientBuilder
{

    public function __invoke(ContainerInterface $container)
    {
        $config       = $container->get(ConfigInterface::class);
        $sentryConfig = $config->get("sentry", []);

        unset(
            // We do not want this setting to hit our main client because it's Laravel specific
            $sentryConfig['breadcrumbs'],
            // We resolve the integrations through the container later, so we initially do not pass it to the SDK yet
            $sentryConfig['integrations'],
            // This is kept for backwards compatibility and can be dropped in a future breaking release
            $sentryConfig['breadcrumbs.sql_bindings']
        );
        $path = BASE_PATH;

        $options = array_merge([
            'default_integrations' => false,
            'prefixes'             => [$path],
            "integrations"         => [
                new RequestIntegration()
            ],
            'in_app_exclude' => ["{$path}/vendor"],
        ], $sentryConfig);


        $clientBuilder = \Sentry\ClientBuilder::create($options);

        // Set the Hyperf SDK identifier and version
        $clientBuilder->setSdkIdentifier(Version::SDK_IDENTIFIER);
        $clientBuilder->setSdkVersion(Version::SDK_VERSION);

        return $clientBuilder;


    }
}