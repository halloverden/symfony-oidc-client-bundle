<?php


namespace HalloVerden\Oidc\ClientBundle\Entity\Requests;


class HandleAuthCodeRequest {

  /**
   * @var string
   */
  private $code;

  /**
   * @var string|null
   */
  private $state;

  /**
   * @return string
   */
  public function getCode(): string {
    return $this->code;
  }

  /**
   * @param string $code
   *
   * @return HandleAuthCodeRequest
   */
  public function setCode(string $code): self {
    $this->code = $code;
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
   * @return HandleAuthCodeRequest
   */
  public function setState(?string $state): self {
    $this->state = $state;
    return $this;
  }

}
