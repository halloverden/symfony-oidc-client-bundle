<?php


namespace HalloVerden\Oidc\ClientBundle\Services;


use HalloVerden\Contracts\Oidc\Tokens\OidcAccessTokenInterface;
use HalloVerden\Oidc\ClientBundle\Token\AccessToken;
use HalloVerden\Security\Interfaces\OauthTokenProviderServiceInterface;
use Jose\Easy\JWT;

class OauthTokenProviderService implements OauthTokenProviderServiceInterface {

  /**
   * @inheritDoc
   */
  public function getOauthTokenFromJWT(JWT $jwt, string $rawToken): ?OidcAccessTokenInterface {
    return AccessToken::createFromJwt($jwt, $rawToken);
  }

}
