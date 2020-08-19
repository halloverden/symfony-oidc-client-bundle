<?php


namespace HalloVerden\Oidc\ClientBundle\Interfaces;


use HalloVerden\Contracts\Oidc\OidcEndSessionResponseInterface;
use HalloVerden\Contracts\Oidc\Requests\OidcAuthenticationRequestInterface;
use HalloVerden\Oidc\ClientBundle\Client\ClientConfiguration;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidTokenException;
use HalloVerden\Oidc\ClientBundle\Exception\ProviderException;
use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;
use HalloVerden\Oidc\ClientBundle\Provider\ProviderConfiguration;
use Jose\Component\Core\JWKSet;

/**
 * Interface OpenIdProviderServiceInterface
 *
 * @package HalloVerden\Oidc\ClientBundle\Interfaces
 */
interface OpenIdProviderServiceInterface {

  /**
   * @return ClientConfiguration
   */
  public function getClientConfiguration(): ClientConfiguration;

  /**
   * @return ProviderConfiguration
   * @throws ProviderException
   */
  public function getProviderConfiguration(): ProviderConfiguration;

  /**
   * @return JWKSet
   * @throws ProviderException
   */
  public function getPublicKey(): JWKSet;

  /**
   * @return OidcAuthenticationRequestInterface
   * @throws ProviderException
   */
  public function getAuthorizeRequest(): OidcAuthenticationRequestInterface;

  /**
   * @param OidcGrantInterface $grant
   *
   * @return OidcTokenResponseInterface
   * @throws ProviderException
   * @throws InvalidTokenException
   */
  public function getTokenResponse(OidcGrantInterface $grant): OidcTokenResponseInterface;

  /**
   * @param OidcRawTokenInterface $token
   *
   * @throws ProviderException
   */
  public function revokeToken(OidcRawTokenInterface $token): void;

  /**
   * @param OidcRawTokenInterface $idTokenHint
   * @param string                $postLogoutRedirectUri
   * @param string                $state
   *
   * @return OidcEndSessionResponseInterface
   * @throws ProviderException
   */
  public function getEndSessionResponse(OidcRawTokenInterface $idTokenHint, string $postLogoutRedirectUri, string $state): OidcEndSessionResponseInterface;

}
