<?php


namespace HalloVerden\Oidc\ClientBundle\DependencyInjection;


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
              ->scalarNode('issuer')->isRequired()->end()
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
              ->integerNode('state_parameter_length')->defaultValue(10)->end()
              ->integerNode('nonce_parameter_length')->defaultValue(10)->end()
              ->arrayNode('jws_loader')
                ->ignoreExtraKeys()
                ->addDefaultsIfNotSet()
                ->children()
                  ->scalarNode('accesstoken')->defaultValue('hv_oidc_client_default')->end()
                  ->scalarNode('access_token_client_credentials')->defaultValue('hv_oidc_client_default')->end()
                  ->scalarNode('idtoken')->defaultValue('hv_oidc_client_default')->end()
                  ->scalarNode('refreshtoken')->defaultValue('hv_oidc_client_default')->end()
                ->end()
              ->end()
              ->arrayNode('claim_checker')
                ->ignoreExtraKeys()
                ->addDefaultsIfNotSet()
                ->children()
                  ->scalarNode('accesstoken')->defaultValue('hv_oidc_client_default_accesstoken')->end()
                  ->scalarNode('access_token_client_credentials')->defaultValue('hv_oidc_client_default_access_token_client_credentials')->end()
                  ->scalarNode('idtoken')->defaultValue('hv_oidc_client_default_idtoken')->end()
                  ->scalarNode('refreshtoken')->defaultValue('hv_oidc_client_default_refreshtoken')->end()
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
