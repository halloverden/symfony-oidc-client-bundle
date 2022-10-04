<?php

namespace HalloVerden\Oidc\ClientBundle\Interfaces\Grant;

use HalloVerden\Oidc\ClientBundle\Client\ClientConfiguration;
use HalloVerden\Oidc\ClientBundle\Provider\ProviderConfiguration;

interface GrantHandlerInterface {

  /**
   * @param OidcGrantInterface    $grant
   * @param ClientConfiguration   $clientConfiguration
   * @param ProviderConfiguration $providerConfiguration
   *
   * @return bool
   */
  public function supports(OidcGrantInterface $grant, ClientConfiguration $clientConfiguration, ProviderConfiguration $providerConfiguration): bool;

  /**
   * @param OidcGrantInterface    $grant
   * @param ClientConfiguration   $clientConfiguration
   * @param ProviderConfiguration $providerConfiguration
   *
   * @return array<string, scalar|null>
   */
  public function getRequestData(OidcGrantInterface $grant, ClientConfiguration $clientConfiguration, ProviderConfiguration $providerConfiguration): array;

}
