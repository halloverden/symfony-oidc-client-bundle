<?php


namespace HalloVerden\Oidc\ClientBundle\Interfaces;


use HalloVerden\Contracts\Oidc\Tokens\OidcAccessTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcIdTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcRefreshTokenInterface;

interface OidcTokenResponseInterface {

  /**
   * @return OidcAccessTokenInterface
   */
  public function getAccessToken(): OidcAccessTokenInterface;

  /**
   * @return OidcIdTokenInterface|null
   */
  public function getIdToken(): ?OidcIdTokenInterface;

  /**
   * @return string
   */
  public function getTokenType(): string;

  /**
   * @return OidcRefreshTokenInterface|null
   */
  public function getRefreshToken(): ?OidcRefreshTokenInterface;

  /**
   * @return array
   */
  public function getRawData(): array;

}
