<?php


namespace HalloVerden\Oidc\ClientBundle\Entity\Grant;


use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;

class RefreshTokenGrant implements OidcGrantInterface {
  const TYPE_NAME = 'refresh_token';

  /**
   * @var string
   */
  private $refreshToken;

  /**
   * RefreshTokenGrant constructor.
   *
   * @param string $refreshToken
   */
  public function __construct(string $refreshToken) {
    $this->refreshToken = $refreshToken;
  }

  /**
   * @inheritDoc
   */
  public function getRequestData(): array {
    return [
      'refresh_token' => $this->refreshToken
    ];
  }

  /**
   * @inheritDoc
   */
  public function getTypeName(): string {
    return self::TYPE_NAME;
  }

}
