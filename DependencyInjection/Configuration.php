<?php


namespace HalloVerden\Oidc\ClientBundle\DependencyInjection;


use HalloVerden\Oidc\ClientBundle\Factory\OidcTokenResponseFactory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

  /**
   * @inheritDoc
   */
  public function getConfigTreeBuilder() {
    $treeBuilder = new TreeBuilder('hallo_verden_oidc_client');

    $treeBuilder->getRootNode()
      ->addDefaultsIfNotSet()
      ->children()
        ->arrayNode('client_configurations')
          ->arrayPrototype()
            ->children()
              ->scalarNode('issuer')->defaultNull()->end()
              ->scalarNode('client_id')->defaultNull()->end()
              ->scalarNode('client_secret')->defaultNull()->end()
              ->scalarNode('redirect_uri')->defaultNull()->end()
              ->scalarNode('openid_configuration_endpoint')->defaultNull()->end()
              ->scalarNode('response_type')->defaultValue('code')->end()
              ->scalarNode('response_mode')->defaultNull()->end()
              ->scalarNode('acr_values')->defaultNull()->end()
              ->scalarNode('ui_locales')->defaultNull()->end()
              ->scalarNode('scope')->defaultValue('openid')->end()
              ->booleanNode('pkce_enabled')->defaultFalse()->end()
              ->scalarNode('cache')->defaultValue('cache.app')->end()
              ->integerNode('state_parameter_length')->defaultValue(10)->end()
              ->integerNode('nonce_parameter_length')->defaultValue(10)->end()
              ->scalarNode('jwk_id')->defaultNull()->end()
              ->scalarNode('jwt_serializer')->defaultValue('jws_compact')->end()
              ->arrayNode('jwt_custom_claims')->defaultValue([])->scalarPrototype()->end()->end()
              ->booleanNode('validate_access_tokens')->defaultTrue()->end()
              ->arrayNode('mandatory_claims')
                ->ignoreExtraKeys()
                ->addDefaultsIfNotSet()
                ->children()
                  ->arrayNode('accesstoken')->scalarPrototype()->end()->defaultValue(OidcTokenResponseFactory::MANDATORY_CLAIMS)->end()
                  ->arrayNode('access_token_client_credentials')->scalarPrototype()->end()->defaultValue(OidcTokenResponseFactory::MANDATORY_CLAIMS)->end()
                  ->arrayNode('idtoken')->scalarPrototype()->end()->defaultValue(OidcTokenResponseFactory::MANDATORY_CLAIMS)->end()
                  ->arrayNode('refreshtoken')->scalarPrototype()->end()->defaultValue(OidcTokenResponseFactory::MANDATORY_CLAIMS)->end()
                ->end()
              ->end()
              ->arrayNode('jws_loader')
                ->ignoreExtraKeys()
                ->addDefaultsIfNotSet()
                ->children()
                  ->scalarNode('accesstoken')->defaultNull()->end()
                  ->scalarNode('access_token_client_credentials')->defaultNull()->end()
                  ->scalarNode('idtoken')->defaultNull()->end()
                  ->scalarNode('refreshtoken')->defaultNull()->end()
                ->end()
              ->end()
              ->arrayNode('claim_checker')
                ->ignoreExtraKeys()
                ->addDefaultsIfNotSet()
                ->children()
                  ->scalarNode('accesstoken')->defaultNull()->end()
                  ->scalarNode('access_token_client_credentials')->defaultNull()->end()
                  ->scalarNode('idtoken')->defaultNull()->end()
                  ->scalarNode('refreshtoken')->defaultNull()->end()
                ->end()
              ->end()
            ->end()
          ->end()
        ->end()
        ->scalarNode('default_client_configuration')->end()
      ->end()
      ->validate()
        ->ifTrue(function ($v) {
          return isset($v['default_client_configuration']) && !array_key_exists($v['default_client_configuration'], $v['client_configurations']);
        })
        ->thenInvalid('default_client_configuration does not match any client_configuration')
      ->end();

    return $treeBuilder;
  }

}
