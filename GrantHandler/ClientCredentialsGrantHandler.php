<?php

namespace HalloVerden\Oidc\ClientBundle\GrantHandler;

use HalloVerden\Oidc\ClientBundle\Client\ClientConfiguration;
use HalloVerden\Oidc\ClientBundle\Entity\Grant\ClientCredentialsGrant;
use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;
use HalloVerden\Oidc\ClientBundle\Provider\ProviderConfiguration;

final class ClientCredentialsGrantHandler extends AbstractGrantHandler {

  /**
   * @inheritDoc
   */
  public function supports(OidcGrantInterface $grant, ClientConfiguration $clientConfiguration, ProviderConfiguration $providerConfiguration): bool {
    return $grant instanceof ClientCredentialsGrant;
  }

}
