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
  public static function create(OpenIdProviderServiceInterface $openIdProviderService, OauthJwkSetProviderServiceInterface $oauthJwkSetProvider): OauthAuthenticatorService {
    return new OauthAuthenticatorService(
      $openIdProviderService->getClientConfiguration()->getIssuer(),
      $oauthJwkSetProvider
    );
  }

}
