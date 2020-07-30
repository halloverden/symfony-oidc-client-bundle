<?php


namespace HalloVerden\Oidc\ClientBundle\Entity;


class OauthAuthorizeSession {

  /**
   * @var string
   */
  private $state;

  /**
   * @var string
   */
  private $successUrl;

  /**
   * @var string
   */
  private $errorUrl;

  /**
   * @var string|null
   */
  private $nonce;

  /**
   * OauthAuthorizeSession constructor.
   *
   * @param string      $state
   * @param string      $successUrl
   * @param string      $errorUrl
   * @param string|null $nonce
   */
  public function __construct(string $state, string $successUrl, string $errorUrl, ?string $nonce = null) {
    $this->state = $state;
    $this->successUrl = $successUrl;
    $this->errorUrl = $errorUrl;
    $this->nonce = $nonce;
  }

  /**
   * @return string
   */
  public function getState(): string {
    return $this->state;
  }

  /**
   * @return string
   */
  public function getSuccessUrl(): string {
    return $this->successUrl;
  }

  /**
   * @return string
   */
  public function getErrorUrl(): string {
    return $this->errorUrl;
  }

  /**
   * @return string|null
   */
  public function getNonce(): ?string {
    return $this->nonce;
  }
}
