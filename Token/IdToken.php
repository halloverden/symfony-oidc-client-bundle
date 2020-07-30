<?php


namespace HalloVerden\Oidc\ClientBundle\Token;


use HalloVerden\Contracts\Oidc\Tokens\OidcIdTokenInterface;

class IdToken extends AbstractToken implements OidcIdTokenInterface {

  /**
   * @var int
   */
  protected $exp;

  /**
   * @var string
   */
  protected $authTime;

  /**
   * @var string
   */
  protected $nonce;

  /**
   * @var string
   */
  protected $atHash;

  /**
   * @var string
   */
  protected $cHash;

  /**
   * @var array
   */
  protected $userClaims;


  /**
   * @inheritDoc
   */
  public function getExp(): int {
    return $this->exp;
  }

  /**
   * @inheritDoc
   */
  public function getAuthTime(): string {
    return $this->authTime;
  }

  /**
   * @inheritDoc
   */
  public function getNonce(): ?string {
    return $this->nonce;
  }

  /**
   * @inheritDoc
   */
  public function getAtHash(): ?string {
    return $this->atHash;
  }

  /**
   * @inheritDoc
   */
  public function getCHash(): ?string {
    return $this->cHash;
  }

  /**
   * @inheritDoc
   */
  public function getUserClaims(): ?array {
    return $this->userClaims;
  }
}
