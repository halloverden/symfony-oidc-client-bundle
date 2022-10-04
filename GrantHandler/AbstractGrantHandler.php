<?php

namespace HalloVerden\Oidc\ClientBundle\GrantHandler;

use HalloVerden\Oidc\ClientBundle\Client\ClientConfiguration;
use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\GrantHandlerInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;
use HalloVerden\Oidc\ClientBundle\Provider\ProviderConfiguration;

abstract class AbstractGrantHandler implements GrantHandlerInterface {

  /**
   * @inheritDoc
   */
  public function getRequestData(OidcGrantInterface $grant, ClientConfiguration $clientConfiguration, ProviderConfiguration $providerConfiguration): array {
    return array_merge([
      'grant_type' => $grant->getTypeName(),
      'client_id' => $clientConfiguration->getClientId(),
      'client_secret' => $clientConfiguration->getClientSecret(),
      'redirect_uri' => $clientConfiguration->getRedirectUri(),
      'scope' => $clientConfiguration->getScope(),
    ], $grant->getRequestData());
  }

}
