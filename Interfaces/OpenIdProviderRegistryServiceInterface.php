<?php


namespace HalloVerden\Oidc\ClientBundle\Interfaces;


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
