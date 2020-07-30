<?php


namespace HalloVerden\Oidc\ClientBundle\Token;


use HalloVerden\Contracts\Oidc\Tokens\OidcRefreshTokenInterface;

class RefreshToken extends AbstractToken implements OidcRefreshTokenInterface {

  /**
   * @var int
   */
  protected $exp;

  /**
   * @var string
   */
  protected $jti;

  /**
   * @inheritDoc
   */
  public function getExp(): int {
    return $this->exp;
  }

  /**
   * @inheritDoc
   */
  public function getJti(): string {
    return $this->jti;
  }
}
