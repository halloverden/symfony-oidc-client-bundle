<?php

namespace HalloVerden\Oidc\ClientBundle\Entity\Grant;

use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;

class JwtGrant implements OidcGrantInterface {
  public const TYPE_NAME = 'urn:ietf:params:oauth:grant-type:jwt-bearer';

  /**
   * JwtGrant constructor.
   */
  public function __construct(private readonly ?string $jwt = null) {
  }


  /**
   * @inheritDoc
   */
  public function getTypeName(): string {
    return self::TYPE_NAME;
  }

  /**
   * @inheritDoc
   */
  public function getRequestData(): array {
    return [];
  }

  /**
   * @return string|null
   */
  public function getJwt(): ?string {
    return $this->jwt;
  }

}
