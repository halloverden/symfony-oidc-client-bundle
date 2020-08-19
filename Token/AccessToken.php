<?php


namespace HalloVerden\Oidc\ClientBundle\Token;


use HalloVerden\Contracts\Oidc\Tokens\OidcAccessTokenInterface;

class AccessToken extends AbstractToken implements OidcAccessTokenInterface {

  /**
   * @var int
   */
  protected $exp;

  /**
   * @var string
   */
  protected $jti;

  /**
   * @var string
   */
  protected $scope;

  /**
   * @var int
   */
  protected $subUpdatedAt;

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

  /**
   * @inheritDoc
   */
  public function getScope(): string {
    return $this->scope;
  }

  /**
   * @inheritDoc
   */
  public function getSubUpdatedAt(): int {
    return $this->subUpdatedAt;
  }

}
