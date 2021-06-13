<?php
declare(strict_types=1);

namespace Vim\ApiSecurity\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Vim\ApiSecurity\Authenticator\JwtLoginAuthenticator;
use Vim\ApiSecurity\Service\JwtUserService;

class ApiSecurityExtension extends Extension
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container
            ->getDefinition(JwtLoginAuthenticator::class)
            ->setArgument('$routeName', $config['jwt_login_route_name']);

        $container
            ->getDefinition(JwtUserService::class)
            ->setArgument('$secretKey', $config['jwt_config']['secret_key'])
            ->setArgument('$leeway', $config['jwt_config']['leeway'])
            ->setArgument('$exp', $config['jwt_config']['exp']);
    }
}
