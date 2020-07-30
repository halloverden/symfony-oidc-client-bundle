<?php


namespace HalloVerden\Oidc\ClientBundle\DependencyInjection;


use HalloVerden\Oidc\ClientBundle\Client\ClientConfiguration;
use HalloVerden\Oidc\ClientBundle\Factory\OauthAuthenticatorServiceFactory;
use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderServiceInterface;
use HalloVerden\Oidc\ClientBundle\Services\OpenIdProviderService;
use HalloVerden\Security\Interfaces\OauthAuthenticatorServiceInterface;
use HalloVerden\Security\Interfaces\OauthJwkSetProviderServiceInterface;
use HalloVerden\Security\Services\OauthAuthenticatorService;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class HalloVerdenOidcClientExtension extends Extension {

  /**
   * @inheritDoc
   */
  public function load(array $configs, ContainerBuilder $container) {
    $config = $this->processConfiguration(new Configuration(), $configs);

    $defaultClientConfiguration = null;

    foreach ($config['client_configurations'] as $key => $clientConfigurationArray) {
      $clientConfiguration = $this->registerClientConfiguration($clientConfigurationArray, $key, $config, $container);
      $openIdProviderService = $this->registerOpenIdProviderService($clientConfiguration, $key, $config, $container);

      if ($key === $config['default_client_configuration']) {
        $this->createOauthAuthenticatorService($openIdProviderService, $container);
      }
    }

    $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.yaml');
  }

  /**
   * @param array  $clientConfigurationArray
   * @param string $key
   *
   * @return Definition
   */
  private function createClientConfigurationDefinition(array $clientConfigurationArray, string $key): Definition {
    $clientConfiguration = new Definition(ClientConfiguration::class, [
      '$issuer' => $clientConfigurationArray['issuer'],
    ]);

    $clientConfiguration->setMethodCalls(
      [
        ['setClientId', [$clientConfigurationArray['client_id']]],
        ['setClientSecret', [$clientConfigurationArray['client_secret']]],
        ['setRedirectUri', [$clientConfigurationArray['redirect_uri']]],
        ['setOpenIdConfigurationEndpoint', [$clientConfigurationArray['openid_configuration_endpoint']]],
        ['setResponseType', [$clientConfigurationArray['response_type']]],
        ['setResponseMode', [$clientConfigurationArray['response_mode']]],
        ['setPkce', [$clientConfigurationArray['pkce']]],
        ['setScope', [$clientConfigurationArray['scope']]],
      ],
    );

    $clientConfiguration->addTag('hv.oidc.client_configuration', ['key' => $key]);

    return $clientConfiguration;
  }

  /**
   * @param array            $clientConfigurationArray
   * @param string           $key
   * @param array            $config
   * @param ContainerBuilder $container
   *
   * @return Definition
   */
  private function registerClientConfiguration(array $clientConfigurationArray, string $key, array $config, ContainerBuilder $container): Definition {
    $clientConfiguration = $this->createClientConfigurationDefinition($clientConfigurationArray, $key);
    $clientConfigurationId = 'hv.oidc.client_configuration.' . $key;
    $container->setDefinition($clientConfigurationId, $clientConfiguration);

    if ($key === $config['default_client_configuration']) {
      $clientConfigurationDefaultServiceId = 'hv.oidc.client_configuration.default';
      $container->setAlias($clientConfigurationDefaultServiceId, $clientConfigurationId);
      $container->setAlias(ClientConfiguration::class, $clientConfigurationDefaultServiceId);
    }

    return $clientConfiguration;
  }

  /**
   * @param Definition       $clientConfiguration
   * @param string           $key
   * @param array            $config
   * @param ContainerBuilder $container
   *
   * @return Definition
   */
  private function registerOpenIdProviderService(Definition $clientConfiguration, string $key, array $config, ContainerBuilder $container): Definition {
    $openIdProviderService = new Definition(OpenIdProviderService::class, [
      '$clientConfiguration' => $clientConfiguration,
      '$client' => new Reference('http_client'),
      '$serializer' => new Reference('jms_serializer'),
    ]);
    $openIdProviderService->addTag('hv.oidc.openid_provider_service', ['key' => $key]);
    $openIdProviderServiceId = 'hv.oidc.openid_provider.' . $key;
    $container->setDefinition($openIdProviderServiceId, $openIdProviderService);

    $container->registerAliasForArgument($openIdProviderServiceId, OpenIdProviderServiceInterface::class, $key .  '.open_id_provider_service');

    if ($key === $config['default_client_configuration']) {
      $defaultOpenIdProviderServiceId = 'hv.oidc.openid_provider.default';
      $container->setAlias($defaultOpenIdProviderServiceId, $openIdProviderServiceId);
      $container->setAlias(OpenIdProviderServiceInterface::class, $defaultOpenIdProviderServiceId);
    }

    return $openIdProviderService;
  }

  /**
   * @param Definition       $openIdProviderService
   * @param ContainerBuilder $container
   *
   * @return Definition
   */
  private function createOauthAuthenticatorService(Definition $openIdProviderService, ContainerBuilder $container): Definition {
    $oauthAuthenticatorService = new Definition(OauthAuthenticatorService::class);
    $oauthAuthenticatorService->setFactory([OauthAuthenticatorServiceFactory::class, 'create'])
      ->setArguments([
        '$openIdProviderService' => $openIdProviderService,
        '$oauthJwkSetProvider' => new Reference(OauthJwkSetProviderServiceInterface::class)
      ]);
    $container->setDefinition(OauthAuthenticatorServiceInterface::class, $oauthAuthenticatorService);

    return $oauthAuthenticatorService;
  }

}
