<?php


namespace HalloVerden\Oidc\ClientBundle\Interfaces;

use HalloVerden\Oidc\ClientBundle\Services\OpenIdProviderService;

/**
 * Interface OpenIdProviderRegistryServiceInterface
 *
 * @package HalloVerden\Oidc\ClientBundle\Interfaces
 *
 * @method OpenIdProviderServiceInterface|null getOpenIdProviderServiceByKey(string $key)
 */
interface OpenIdProviderRegistryServiceInterface {

  /**
   * @return OpenIdProviderServiceInterface[]
   */
  public function getOpenIdProviderServices(): array;

  /**
   * @param string $issuer
   *
   * @return OpenIdProviderServiceInterface|null
   */
  public function getOpenIdProviderServiceByIssuer(string $issuer): ?OpenIdProviderServiceInterface;

}
