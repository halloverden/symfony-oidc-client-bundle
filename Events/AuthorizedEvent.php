<?php


namespace HalloVerden\Oidc\ClientBundle\Events;


use HalloVerden\Contracts\Oidc\Tokens\OidcAccessTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcIdTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcRefreshTokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

class AuthorizedEvent extends Event {

  /**
   * @var string
   */
  private $issuer;

  /**
   * @var OidcAccessTokenInterface
   */
  private $accessToken;

  /**
   * @var OidcIdTokenInterface|null
   */
  private $idToken;

  /**
   * @var OidcRefreshTokenInterface|null
   */
  private $refreshToken;

  /**
   * AuthorizedEvent constructor.
   *
   * @param string                         $issuer
   * @param OidcAccessTokenInterface       $accessToken
   * @param OidcIdTokenInterface|null      $idToken
   * @param OidcRefreshTokenInterface|null $refreshToken
   */
  public function __construct(string $issuer, OidcAccessTokenInterface $accessToken, ?OidcIdTokenInterface $idToken = null, ?OidcRefreshTokenInterface $refreshToken = null) {
    $this->issuer = $issuer;
    $this->accessToken = $accessToken;
    $this->idToken = $idToken;
    $this->refreshToken = $refreshToken;
  }

  /**
   * @return string
   */
  public function getIssuer(): string {
    return $this->issuer;
  }

  /**
   * @return OidcAccessTokenInterface
   */
  public function getAccessToken(): OidcAccessTokenInterface {
    return $this->accessToken;
  }

  /**
   * @return OidcIdTokenInterface|null
   */
  public function getIdToken(): ?OidcIdTokenInterface {
    return $this->idToken;
  }

  /**
   * @return OidcRefreshTokenInterface|null
   */
  public function getRefreshToken(): ?OidcRefreshTokenInterface {
    return $this->refreshToken;
  }

}
