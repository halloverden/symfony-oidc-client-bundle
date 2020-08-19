<?php


namespace HalloVerden\Oidc\ClientBundle\Entity\Requests;

/**
 * Class OauthAuthorizeRequest
 *
 * @package App\Entity\Requests
 */
class AuthorizeRequest {

  /**
   * @var string|null
   */
  private $successUrl;

  /**
   * @var string|null
   */
  private $errorUrl;

  /**
   * @return string|null
   */
  public function getSuccessUrl(): ?string {
    return $this->successUrl;
  }

  /**
   * @param string|null $successUrl
   *
   * @return self
   */
  public function setSuccessUrl(?string $successUrl): self {
    $this->successUrl = $successUrl;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getErrorUrl(): ?string {
    return $this->errorUrl;
  }

  /**
   * @param string|null $errorUrl
   *
   * @return self
   */
  public function setErrorUrl(?string $errorUrl): self {
    $this->errorUrl = $errorUrl;
    return $this;
  }

}
