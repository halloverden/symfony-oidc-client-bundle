<?php


namespace HalloVerden\Oidc\ClientBundle\Token;


use HalloVerden\Contracts\Oidc\Tokens\OidcTokenInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OidcRawTokenInterface;
use Jose\Easy\JWT;

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
   * @param JWT    $JWT
   * @param string $rawToken
   *
   * @return static
   */
  public static function createFromJwt(JWT $JWT, string $rawToken): self {
    $accessToken = new static();

    $claims = $JWT->claims->all();

    foreach ($claims as $key => $value) {
      $property = self::toCamelCase($key);
      if (property_exists($accessToken, $property)) {
        $accessToken->{$property} = $value;
        unset($claims[$key]);
      }
    }

    $accessToken->payload = $claims;
    $accessToken->rawToken = $rawToken;

    return $accessToken;
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
