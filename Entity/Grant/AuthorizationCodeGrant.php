<?php


namespace HalloVerden\Oidc\ClientBundle\Entity\Grant;

use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;

class AuthorizationCodeGrant implements OidcGrantInterface {
  const TYPE_NAME = 'authorization_code';

  private string $code;
  private ?string $codeVerifier;
  private ?string $state;

  /**
   * AuthorizationCodeGrant constructor.
   *
   * @param string      $code
   * @param string|null $codeVerifier
   * @param string|null $state
   */
  public function __construct(string $code, ?string $codeVerifier = null, ?string $state = null) {
    $this->code = $code;
    $this->codeVerifier = $codeVerifier;
    $this->state = $state;
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
   * @return string|null
   */
  public function getCodeVerifier(): ?string {
    return $this->codeVerifier;
  }

  /**
   * @param string|null $codeVerifier
   *
   * @return self
   */
  public function setCodeVerifier(?string $codeVerifier): self {
    $this->codeVerifier = $codeVerifier;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getState(): ?string {
    return $this->state;
  }

  /**
   * @param string|null $state
   *
   * @return AuthorizationCodeGrant
   */
  public function setState(?string $state): self {
    $this->state = $state;
    return $this;
  }

  /**
   * @return string[]
   */
  public function getRequestData(): array {
    $data = [
      'code' => $this->getCode()
    ];

    if (null !== $this->getCodeVerifier()) {
      $data['code_verifier'] = $this->getCodeVerifier();
    }

    return $data;
  }

  /**
   * @inheritDoc
   */
  public function getTypeName(): string {
    return self::TYPE_NAME;
  }
}
