<?php


namespace HalloVerden\Oidc\ClientBundle\Entity\Grant;

use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;

class AuthorizationCodeGrant implements OidcGrantInterface {
  const TYPE_NAME = 'authorization_code';

  /**
   * @var string
   */
  private $code;

  /**
   * AuthorizationCodeGrant constructor.
   *
   * @param string $code
   */
  public function __construct(string $code) {
    $this->code = $code;
  }

  /**
   * @return string
   */
  public function getCode(): string {
    return $this->code;
  }

  /**
   * @param string $code
   *
   * @return self
   */
  public function setCode(string $code): self {
    $this->code = $code;
    return $this;
  }

  /**
   * @return string[]
   */
  public function getRequestData(): array {
    return [
      'code' => $this->getCode()
    ];
  }

  /**
   * @inheritDoc
   */
  public function getTypeName(): string {
    return self::TYPE_NAME;
  }
}
