<?php


namespace HalloVerden\Oidc\ClientBundle\Token;


use HalloVerden\Contracts\Oidc\Tokens\OidcTokenInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OidcRawTokenInterface;

abstract class AbstractToken implements OidcTokenInterface, OidcRawTokenInterface {

  /**
   * @var string
   */
  protected $iss;

  /**
   * @var string
   */
  protected $sub;

  /**
   * @var string
   */
  protected $aud;

  /**
   * @var int
   */
  protected $iat;

  /**
   * @var string
   */
  protected $type;

  /**
   * @var array
   */
  protected $payload;

  /**
   * @var string
   */
  protected $rawToken;

  /**
   * @param array  $claims
   * @param string $rawToken
   *
   * @return static
   */
  public static function createFromJwt(array $claims, string $rawToken): self {
    $token = new static();

    foreach ($claims as $key => $value) {
      $property = self::toCamelCase($key);
      if (property_exists($token, $property)) {
        $token->{$property} = $value;
        unset($claims[$key]);
      }
    }

    $token->payload = $claims;
    $token->rawToken = $rawToken;

    return $token;
  }

  /**
   * @param string $input
   *
   * @return string
   */
  private static function toCamelCase(string $input): string {
    return str_replace('_', '', lcfirst(ucwords($input, '_')));
  }

  /**
   * @inheritDoc
   */
  public function getIss(): string {
    return $this->iss;
  }

  /**
   * @inheritDoc
   */
  public function getSub(): string {
    return $this->sub;
  }

  /**
   * @inheritDoc
   */
  public function getAud(): string {
    return $this->aud;
  }

  /**
   * @inheritDoc
   */
  public function getIat(): int {
    return $this->iat;
  }

  /**
   * @inheritDoc
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * @inheritDoc
   */
  public function getPayload(): array {
    return $this->payload;
  }

  /**
   * @inheritDoc
   */
  public function getRawToken(): string {
    return $this->rawToken;
  }

}
