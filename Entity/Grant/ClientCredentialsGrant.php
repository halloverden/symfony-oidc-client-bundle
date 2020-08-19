<?php


namespace HalloVerden\Oidc\ClientBundle\Entity\Grant;


use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;

class ClientCredentialsGrant implements OidcGrantInterface {
  const TYPE_NAME = 'client_credentials';

  public function getRequestData(): array {
    return [];
  }

  /**
   * @inheritDoc
   */
  public function getTypeName(): string {
    return self::TYPE_NAME;
  }

}
