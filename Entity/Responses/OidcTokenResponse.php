<?php


namespace HalloVerden\Oidc\ClientBundle\Entity\Responses;

use HalloVerden\Contracts\Oidc\Tokens\OidcAccessTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcIdTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcRefreshTokenInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OidcTokenResponseInterface;

/**
 * Class OidcTokenResponse
 *
 * @package HalloVerden\Oidc\ClientBundle\Entity\Responses
 */
class OidcTokenResponse implements OidcTokenResponseInterface {

  /**
   * @var OidcAccessTokenInterface
   */
  private $accessToken;

  /**
   * @var OidcIdTokenInterface
   */
  private $idToken;

  /**
   * @var string
   */
  private $tokenType;

  /**
   * @var OidcRefreshTokenInterface|null
   */
  private $refreshToken;

  /**
   * @var array
   */
  private $rawData;

  /**
   * OidcTokenResponse constructor.
   *
   * @param OidcAccessTokenInterface $accessToken
   * @param string                   $tokenType
   * @param array                    $rawData
   */
  public function __construct(OidcAccessTokenInterface $accessToken, string $tokenType, array $rawData) {
    $this->accessToken = $accessToken;
    $this->tokenType = $tokenType;
    $this->rawData = $rawData;
  }

  /**
   * @inheritDoc
   */
  public function getAccessToken(): OidcAccessTokenInterface {
    return $this->accessToken;
  }

  /**
   * @inheritDoc
   */
  public function getIdToken(): ?OidcIdTokenInterface {
    return $this->idToken;
  }

  /**
   * @inheritDoc
   */
  public function getTokenType(): string {
    return $this->tokenType;
  }

  /**
   * @inheritDoc
   */
  public function getRefreshToken(): ?OidcRefreshTokenInterface {
    return $this->refreshToken;
  }

  /**
   * @param OidcAccessTokenInterface $accessToken
   *
   * @return self
   */
  public function setAccessToken(OidcAccessTokenInterface $accessToken): self {
    $this->accessToken = $accessToken;
    return $this;
  }

  /**
   * @param OidcIdTokenInterface $idToken
   *
   * @return self
   */
  public function setIdToken(OidcIdTokenInterface $idToken): self {
    $this->idToken = $idToken;
    return $this;
  }

  /**
   * @param string $tokenType
   *
   * @return self
   */
  public function setTokenType(string $tokenType): self {
    $this->tokenType = $tokenType;
    return $this;
  }

  /**
   * @param OidcRefreshTokenInterface|null $refreshToken
   *
   * @return self
   */
  public function setRefreshToken(?OidcRefreshTokenInterface $refreshToken): self {
    $this->refreshToken = $refreshToken;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getRawData(): array {
    return $this->rawData;
  }
}
