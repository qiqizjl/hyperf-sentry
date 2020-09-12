<?php

declare(strict_types=1);
/**
 *
 *
 * @author    耐小心 <i@naixiaoixn.com>
 * @time      2020/9/7 2:25 上午
 *
 * @copyright 2020 耐小心
 */

namespace Naixiaoxin\HyperfSentry\Sentry;

use Hyperf\Contract\ConfigInterface;
use Naixiaoxin\HyperfSentry\Integration;
use Naixiaoxin\HyperfSentry\RequestFetcher;
use Naixiaoxin\HyperfSentry\RequestIntegration;
use Naixiaoxin\HyperfSentry\SentryContext;
use Psr\Container\ContainerInterface;
use Sentry\ClientBuilderInterface;
use Sentry\State\Hub as SentryHub;
use Sentry\Integration as SdkIntegration;

class Hub
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var \Sentry\ClientBuilderInterface $clientBuilder */
        $clientBuilder = $container->get(ClientBuilderInterface::class);

        $options = $clientBuilder->getOptions();

        $userIntegrations = $this->resolveIntegrationsFromUserConfig($container);

        $options->setIntegrations(static function (array $integrations) use ($options, $userIntegrations) {
            $allIntegrations = array_merge($integrations, $userIntegrations);

            if (!$options->hasDefaultIntegrations()) {
                return $allIntegrations;
            }

            // Remove the default error and fatal exception listeners to let Laravel handle those
            // itself. These event are still bubbling up through the documented changes in the users
            // `ExceptionHandler` of their application or through the log channel integration to Sentry
            return array_filter($allIntegrations, static function (SdkIntegration\IntegrationInterface $integration): bool {
                if ($integration instanceof SdkIntegration\ErrorListenerIntegration) {
                    return false;
                }

                if ($integration instanceof SdkIntegration\ExceptionListenerIntegration) {
                    return false;
                }

                if ($integration instanceof SdkIntegration\FatalErrorListenerIntegration) {
                    return false;
                }

                return true;
            });
        });


        $hub = new SentryHub($clientBuilder->getClient());

        return $hub;

    }

    protected function resolveIntegrationsFromUserConfig(ContainerInterface $container)
    {
        $integrations = [new Integration(),new RequestIntegration()];

        $config = $container->get(ConfigInterface::class)->get("sentry",[]);

        $userIntegrations =$config['integrations'] ?? [];

        foreach ($userIntegrations as $userIntegration) {
            if ($userIntegration instanceof SdkIntegration\IntegrationInterface) {
                $integrations[] = $userIntegration;
            } elseif (\is_string($userIntegration)) {
                $resolvedIntegration = $this->app->make($userIntegration);

                if (!($resolvedIntegration instanceof SdkIntegration\IntegrationInterface)) {
                    throw new \RuntimeException('Sentry integrations should a instance of `\Sentry\Integration\IntegrationInterface`.');
                }

                $integrations[] = $resolvedIntegration;
            } else {
                throw new \RuntimeException('Sentry integrations should either be a container reference or a instance of `\Sentry\Integration\IntegrationInterface`.');
            }
        }

        return $integrations;
    }
}
