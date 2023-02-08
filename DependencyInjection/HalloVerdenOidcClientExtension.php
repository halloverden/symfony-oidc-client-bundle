<?php


namespace HalloVerden\Oidc\ClientBundle\DependencyInjection;


use HalloVerden\Oidc\ClientBundle\Client\ClientConfiguration;
use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderServiceInterface;
use HalloVerden\Oidc\ClientBundle\Services\OpenIdProviderService;
use Jose\Bundle\JoseFramework\Helper\ConfigurationHelper;
use Jose\Component\Checker\AudienceChecker;
use Jose\Component\Core\JWKSet;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class HalloVerdenOidcClientExtension extends Extension implements PrependExtensionInterface {

  /**
   * @inheritDoc
   * @throws \Exception
   */
  public function load(array $configs, ContainerBuilder $container) {
    $config = $this->processConfiguration(new Configuration(), $configs);

    foreach ($config['client_configurations'] as $key => $clientConfigurationArray) {
      $clientConfiguration = $this->registerClientConfiguration($clientConfigurationArray, $key, $config, $container);
      $this->registerOpenIdProviderService($clientConfiguration, $key, $config, $container);
      $this->registerJwkSet($key, $container);
      $this->registerAudienceChecker($key, $clientConfigurationArray, $container);
    }

    $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.yaml');
  }

  /**
   * @inheritDoc
   */
  public function prepend(ContainerBuilder $container) {
    $configs = $container->getExtensionConfig('hallo_verden_oidc_client');
    $config = $this->processConfiguration(new Configuration(), $configs);

    foreach (\array_keys($config['client_configurations']) as $key) {
      $this->addJwsLoaders($key, $container);
      $this->addClaimCheckers($key, $container);
    }
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
        ['setClientSecret', [$clientConfigurationArray['client_secret']]],
        ['setRedirectUri', [$clientConfigurationArray['redirect_uri']]],
        ['setOpenIdConfigurationEndpoint', [$clientConfigurationArray['openid_configuration_endpoint']]],
        ['setResponseType', [$clientConfigurationArray['response_type']]],
        ['setResponseMode', [$clientConfigurationArray['response_mode']]],
        ['setAcrValues', [$clientConfigurationArray['acr_values']]],
        ['setUiLocales', [$clientConfigurationArray['ui_locales']]],
        ['setScope', [$clientConfigurationArray['scope']]],
        ['setPkceEnabled', [$clientConfigurationArray['pkce_enabled']]],
        ['setStateParameterLength', [$clientConfigurationArray['state_parameter_length']]],
        ['setNonceParameterLength', [$clientConfigurationArray['nonce_parameter_length']]],
        ['setJwkId', [$clientConfigurationArray['jwk_id']]],
        ['setJwtSerializer', [$clientConfigurationArray['jwt_serializer']]],
        ['setJtwCustomClaims', [$clientConfigurationArray['jwt_custom_claims']]]
      ],
    );

    if (isset($clientConfigurationArray['client_id'])) {
      $clientConfiguration->addMethodCall('setClientId', [$clientConfigurationArray['client_id']]);
    }

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

    if ($key === ($config['default_client_configuration'] ?? null)) {
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
   * @return void
   */
  private function registerOpenIdProviderService(Definition $clientConfiguration, string $key, array $config, ContainerBuilder $container): void {
    $jwsLoaders = [];
    foreach ($config['client_configurations'][$key]['jws_loader'] as $tokenType => $loader) {
      if (null === $loader) {
        $loader = 'hv_oidc_client_default' . '.' . $key;
      }

      $jwsLoaders[$tokenType] = new Reference('jose.jws_loader.' . $loader);
    }

    $claimCheckers = [];
    foreach ($config['client_configurations'][$key]['claim_checker'] as $tokenType => $claimChecker) {
      if (null === $claimChecker) {
        $claimChecker = 'hv_oidc_client_default_' . $tokenType . '.' . $key;
      }

      $claimCheckers[$tokenType] = new Reference('jose.claim_checker.' . $claimChecker);
    }

    $openIdProviderService = new Definition(OpenIdProviderService::class, [
      '$clientConfiguration' => $clientConfiguration,
      '$client' => new Reference('http_client'),
      '$serializer' => new Reference('jms_serializer'),
      '$jwsLoaders' => $jwsLoaders,
      '$claimCheckers' => $claimCheckers,
      '$mandatoryClaims' => $config['client_configurations'][$key]['mandatory_claims'],
      '$grantHandlers' => new TaggedIteratorArgument('hv_oidc_client.grant_handler')
    ]);
    $openIdProviderService->addTag('hv.oidc.openid_provider_service', ['key' => $key]);
    $openIdProviderServiceId = 'hv.oidc.openid_provider.' . $key;
    $container->setDefinition($openIdProviderServiceId, $openIdProviderService);

    $container->registerAliasForArgument($openIdProviderServiceId, OpenIdProviderServiceInterface::class, $key .  '.open_id_provider_service');

    if ($key === ($config['default_client_configuration'] ?? null)) {
      $defaultOpenIdProviderServiceId = 'hv.oidc.openid_provider.default';
      $container->setAlias($defaultOpenIdProviderServiceId, $openIdProviderServiceId);
      $container->setAlias(OpenIdProviderServiceInterface::class, $defaultOpenIdProviderServiceId);
    }
  }

  /**
   * @param string           $key
   * @param ContainerBuilder $container
   *
   * @return void
   */
  private function registerJwkSet(string $key, ContainerBuilder $container): void {
    $jwkSetService = new Definition(JWKSet::class);
    $jwkSetService->setFactory([new Reference('hv.oidc.openid_provider.' . $key), 'getPublicKey']);
    $jwkSetService->addTag('jose.jwkset');

    $container->setDefinition('jose.key_set.hv_oidc_client.' . $key, $jwkSetService);
  }

  /**
   * @param string           $key
   * @param array            $config
   * @param ContainerBuilder $container
   *
   * @return void
   */
  private function registerAudienceChecker(string $key, array $config, ContainerBuilder $container): void {
    $audienceChecker = new Definition(AudienceChecker::class, [
      '$audience' => $config['client_id']
    ]);
    $audienceChecker->addTag('jose.checker.claim', ['alias' => 'hv_oidc_client_aud_default.' . $key]);

    $container->setDefinition('hv.oidc.claim_checker.aud.' . $key, $audienceChecker);
  }

  /**
   * @param string           $key
   * @param ContainerBuilder $container
   *
   * @return void
   */
  private function addJwsLoaders(string $key, ContainerBuilder $container): void {
    ConfigurationHelper::addJWSLoader(
      $container,
      'hv_oidc_client_default.' . $key,
      ['jws_compact'],
      ['RS256'],
      []
    );
  }

  /**
   * @param string           $key
   * @param ContainerBuilder $container
   *
   * @return void
   */
  private function addClaimCheckers(string $key, ContainerBuilder $container): void {
    ConfigurationHelper::addClaimChecker(
      $container,
      'hv_oidc_client_default_accesstoken.' . $key,
      ['exp', 'iat', 'nbf', 'token_type.accesstoken']
    );

    ConfigurationHelper::addClaimChecker(
      $container,
      'hv_oidc_client_default_access_token_client_credentials.' . $key,
      ['exp', 'iat', 'nbf', 'token_type.access_token_client_credentials']
    );

    ConfigurationHelper::addClaimChecker(
      $container,
      'hv_oidc_client_default_idtoken.' . $key,
      ['exp', 'iat', 'nbf', 'token_type.idtoken', 'hv_oidc_client_aud_default.' . $key]
    );

    ConfigurationHelper::addClaimChecker(
      $container,
      'hv_oidc_client_default_refreshtoken.' . $key,
      ['exp', 'iat', 'nbf', 'token_type.refreshtoken']
    );
  }

}
