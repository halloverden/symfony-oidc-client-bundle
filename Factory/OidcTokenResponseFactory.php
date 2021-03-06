<?php


namespace HalloVerden\Oidc\ClientBundle\Factory;


use HalloVerden\Contracts\Oidc\Tokens\OidcAccessTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcIdTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcRefreshTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcTokenInterface;
use HalloVerden\Oidc\ClientBundle\Entity\Grant\AuthorizationCodeGrant;
use HalloVerden\Oidc\ClientBundle\Helpers\OpenIdHashHelper;
use HalloVerden\Oidc\ClientBundle\Entity\Responses\OidcTokenResponse;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidAccessTokenException;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidIdTokenException;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidRefreshTokenException;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidTokenException;
use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OidcTokenResponseInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderServiceInterface;
use HalloVerden\Oidc\ClientBundle\Token\AccessToken;
use HalloVerden\Oidc\ClientBundle\Token\IdToken;
use HalloVerden\Oidc\ClientBundle\Token\RefreshToken;
use HalloVerden\Security\ClaimCheckers\TokenTypeChecker;
use Jose\Easy\JWT;
use Jose\Easy\Load;

class OidcTokenResponseFactory {

  const MANDATORY_CLAIMS = [
    'exp',
    'iss',
    'iat',
    'type',
  ];

  const VALID_ACCESS_TOKEN_TYPES = [
    OidcTokenInterface::TYPE_ACCESS,
    OidcTokenInterface::TYPE_ACCESS_CLIENT_CREDENTIALS,
  ];

  /**
   * @var OpenIdProviderServiceInterface
   */
  private $openIdProviderService;

  /**
   * OidcTokenResponseFactory constructor.
   *
   * @param OpenIdProviderServiceInterface $openIdProviderService
   */
  public function __construct(OpenIdProviderServiceInterface $openIdProviderService) {
    $this->openIdProviderService = $openIdProviderService;
  }

  /**
   * @param array              $responseData
   * @param OidcGrantInterface $grant
   *
   * @return OidcTokenResponseInterface
   * @throws InvalidIdTokenException
   * @throws InvalidTokenException
   */
  public function createOidcTokenResponse(array $responseData, OidcGrantInterface $grant): OidcTokenResponseInterface {
    $oidcTokenResponse = new OidcTokenResponse($this->createAccessToken($responseData['access_token']), $responseData['token_type'], $responseData);

    if (isset($responseData['id_token'])) {
      $oidcTokenResponse->setIdToken($this->createIdToken($responseData['id_token'], $responseData['access_token'], $grant));
    }

    if (isset($responseData['refresh_token'])) {
      $oidcTokenResponse->setRefreshToken($this->createRefreshToken($responseData['refresh_token']));
    }

    return $oidcTokenResponse;
  }

  /**
   * @param string $jwtTokenString
   *
   * @return OidcAccessTokenInterface
   * @throws InvalidTokenException
   */
  private function createAccessToken(string $jwtTokenString): OidcAccessTokenInterface {
    try {
      $jwt = $this->createJWT($jwtTokenString, self::VALID_ACCESS_TOKEN_TYPES);
    } catch (\Exception $e) {
      throw new InvalidAccessTokenException($e->getMessage());
    }
    return AccessToken::createFromJwt($jwt, $jwtTokenString);
  }

  /**
   * @param string             $jwtTokenString
   * @param string             $jwtAccessTokenString
   *
   * @param OidcGrantInterface $grant
   *
   * @return OidcIdTokenInterface
   * @throws InvalidIdTokenException
   */
  private function createIdToken(string $jwtTokenString, string $jwtAccessTokenString, OidcGrantInterface $grant): OidcIdTokenInterface {
    try {
      $jwt = $this->createJWT($jwtTokenString, [OidcTokenInterface::TYPE_ID]);
    } catch (\Exception $e) {
      throw new InvalidIdTokenException($e->getMessage());
    }

    $idToken = IdToken::createFromJwt($jwt, $jwtTokenString);
    $alg = $jwt->header->get('alg');

    if (!OpenIdHashHelper::compare($jwtAccessTokenString, $idToken->getAtHash(), $alg)) {
      throw new InvalidIdTokenException('at_hash did not match');
    }

    if ($grant instanceof AuthorizationCodeGrant && !OpenIdHashHelper::compare($grant->getCode(), $idToken->getCHash(), $alg)) {
      throw new InvalidIdTokenException('c_hash did not match');
    }

    return $idToken;
  }

  /**
   * @param string $jwtTokenString
   *
   * @return OidcRefreshTokenInterface
   * @throws InvalidTokenException
   */
  private function createRefreshToken(string $jwtTokenString): OidcRefreshTokenInterface {
    try {
      $jwt = $this->createJWT($jwtTokenString, [OidcTokenInterface::TYPE_REFRESH]);
    } catch (\Exception $e) {
      throw new InvalidRefreshTokenException($e->getMessage());
    }
    return RefreshToken::createFromJwt($jwt, $jwtTokenString);
  }

  /**
   * @param string $jwtString
   * @param array  $validTypes
   *
   * @return JWT
   * @throws \Exception
   */
  private function createJWT(string $jwtString, array $validTypes): JWT {
    return Load::jws($jwtString)
      ->exp(100)
      ->iat(100)
      ->aud($this->openIdProviderService->getClientConfiguration()->getClientId())
      ->iss($this->openIdProviderService->getClientConfiguration()->getIssuer())
      ->keyset($this->openIdProviderService->getPublicKey())
      ->mandatory(self::MANDATORY_CLAIMS)
      ->claim('type', new TokenTypeChecker($validTypes))
      ->run();
  }

}
