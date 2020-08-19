<?php


namespace HalloVerden\Oidc\ClientBundle\Events;


use HalloVerden\Contracts\Oidc\Tokens\OidcAccessTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcIdTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcRefreshTokenInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderServiceInterface;
use Symfony\Contracts\EventDispatcher\Event;

class AuthorizedEvent extends Event {

  /**
   * @var OpenIdProviderServiceInterface
   */
  private $openIdServiceProviderService;

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
   * @param OpenIdProviderServiceInterface $openIdProviderService
   * @param OidcAccessTokenInterface       $accessToken
   * @param OidcIdTokenInterface|null      $idToken
   * @param OidcRefreshTokenInterface|null $refreshToken
   */
  public function __construct(OpenIdProviderServiceInterface $openIdProviderService, OidcAccessTokenInterface $accessToken, ?OidcIdTokenInterface $idToken = null, ?OidcRefreshTokenInterface $refreshToken = null) {
    $this->openIdServiceProviderService = $openIdProviderService;
    $this->accessToken = $accessToken;
    $this->idToken = $idToken;
    $this->refreshToken = $refreshToken;
  }

  /**
   * @return OpenIdProviderServiceInterface
   */
  public function getOpenIdServiceProviderService(): OpenIdProviderServiceInterface {
    return $this->openIdServiceProviderService;
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
