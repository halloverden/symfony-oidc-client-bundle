<?php


namespace HalloVerden\Oidc\ClientBundle\Factory;


use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderServiceInterface;
use HalloVerden\Security\Interfaces\OauthJwkSetProviderServiceInterface;
use HalloVerden\Security\Services\OauthAuthenticatorService;

/**
 * Class OauthAuthenticatorServiceFactory
 *
 * @package HalloVerden\Oidc\ClientBundle\Factory
 */
class OauthAuthenticatorServiceFactory {

  /**
   * @param OpenIdProviderServiceInterface      $openIdProviderService
   * @param OauthJwkSetProviderServiceInterface $oauthJwkSetProvider
   *
   * @return OauthAuthenticatorService
   */
  public function create(OpenIdProviderServiceInterface $openIdProviderService, OauthJwkSetProviderServiceInterface $oauthJwkSetProvider) {
    return new OauthAuthenticatorService(
      $openIdProviderService->getClientConfiguration()->getIssuer(),
      $oauthJwkSetProvider
    );
  }

}
