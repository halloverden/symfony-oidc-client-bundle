<?php


namespace HalloVerden\Oidc\ClientBundle\Exception;

/**
 * Class OauthAuthorizeException
 *
 * @package HalloVerden\Oidc\ClientBundle\Exception
 */
class OauthAuthorizeException extends \Exception {

  /**
   * @var string
   */
  private $error;

  /**
   * @var string|null
   */
  private $description;

  public function __construct(string $error, ?string $description = null, \Throwable $previous = null) {
    parent::__construct($error . $description ? ' ('.$description.')' : '', 0, $previous);
    $this->error = $error;
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getError(): string {
    return $this->error;
  }

  /**
   * @return string|null
   */
  public function getDescription(): ?string {
    return $this->description;
  }

}
