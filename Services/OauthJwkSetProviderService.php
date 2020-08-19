<?php


namespace HalloVerden\Oidc\ClientBundle\Services;


use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderRegistryServiceInterface;
use HalloVerden\Security\Interfaces\OauthJwkSetProviderServiceInterface;
use Jose\Component\Core\JWKSet;

/**
 * Class OauthJwkSetProviderService
 *
 * @package HalloVerden\Oidc\ClientBundle\Services
 */
class OauthJwkSetProviderService implements OauthJwkSetProviderServiceInterface {

  /**
   * @var OpenIdProviderRegistryServiceInterface
   */
  private $openIdProviderRegistryService;

  /**
   * OauthJwkSetProviderService constructor.
   *
   * @param OpenIdProviderRegistryServiceInterface $openIdProviderRegistryService
   */
  public function __construct(OpenIdProviderRegistryServiceInterface $openIdProviderRegistryService) {
    $this->openIdProviderRegistryService = $openIdProviderRegistryService;
  }

  /**
   * @inheritDoc
   */
  public function getPublicKey(string $issuer): JWKSet {
    $openIdProviderService = $this->openIdProviderRegistryService->getOpenIdProviderServiceByIssuer($issuer);

    if ($openIdProviderService === null) {
      throw new \LogicException('No openIdProviderService with this issuer');
    }

    return $openIdProviderService->getPublicKey();
  }

}
