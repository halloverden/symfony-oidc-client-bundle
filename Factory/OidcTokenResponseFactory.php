<?php


namespace HalloVerden\Oidc\ClientBundle\Factory;


use HalloVerden\Contracts\Oidc\Tokens\OidcAccessTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcIdTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcRefreshTokenInterface;
use HalloVerden\Contracts\Oidc\Tokens\OidcTokenInterface;
use HalloVerden\Oidc\ClientBundle\Entity\Grant\AuthorizationCodeGrant;
use HalloVerden\Oidc\ClientBundle\Entity\Grant\ClientCredentialsGrant;
use HalloVerden\Oidc\ClientBundle\Entity\Responses\OidcTokenResponse;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidAccessTokenException;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidIdTokenException;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidRefreshTokenException;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidTokenException;
use HalloVerden\Oidc\ClientBundle\Exception\ProviderException;
use HalloVerden\Oidc\ClientBundle\Helpers\OpenIdHashHelper;
use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OidcTokenResponseInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderServiceInterface;
use HalloVerden\Oidc\ClientBundle\Token\AccessToken;
use HalloVerden\Oidc\ClientBundle\Token\IdToken;
use HalloVerden\Oidc\ClientBundle\Token\RefreshToken;
use Jose\Component\Checker\ClaimCheckerManager;
use Jose\Component\Core\Util\JsonConverter;
use Jose\Component\Signature\JWSLoader;

class OidcTokenResponseFactory {

  const MANDATORY_CLAIMS = [
    'exp',
    'iss',
    'iat',
    'type',
  ];

  private OpenIdProviderServiceInterface $openIdProviderService;
  private array $mandatoryClaims;

  /**
   * @var array<string, JWSLoader>
   */
  private array $jwsLoaders;

  /**
   * @var array<string, ClaimCheckerManager>
   */
  private array $claimCheckers;

  /**
   * OidcTokenResponseFactory constructor.
   *
   * @param OpenIdProviderServiceInterface     $openIdProviderService
   * @param array<string, JWSLoader>           $jwsLoaders
   * @param array<string, ClaimCheckerManager> $claimCheckers
   */
  public function __construct(OpenIdProviderServiceInterface $openIdProviderService, array $jwsLoaders, array $claimCheckers) {
    $this->openIdProviderService = $openIdProviderService;
    $this->jwsLoaders = $jwsLoaders;
    $this->claimCheckers = $claimCheckers;
    $this->setMandatoryClaims(self::MANDATORY_CLAIMS);
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
    $oidcTokenResponse = new OidcTokenResponse($this->createAccessToken($responseData['access_token'], $grant), $responseData['token_type'], $responseData);

    if (isset($responseData['id_token'])) {
      $oidcTokenResponse->setIdToken($this->createIdToken($responseData['id_token'], $responseData['access_token'], $grant));
    }

    if (isset($responseData['refresh_token'])) {
      $oidcTokenResponse->setRefreshToken($this->createRefreshToken($responseData['refresh_token']));
    }

    return $oidcTokenResponse;
  }

  /**
   * @param string[]|array<string, array> $mandatoryClaims
   *
   * @return self
   */
  public function setMandatoryClaims(array $mandatoryClaims): self {
    if (\array_is_list($mandatoryClaims)) {
      $this->mandatoryClaims = [
        OidcTokenInterface::TYPE_ACCESS => $mandatoryClaims,
        OidcTokenInterface::TYPE_ACCESS_CLIENT_CREDENTIALS => $mandatoryClaims,
        OidcTokenInterface::TYPE_ID => $mandatoryClaims,
        OidcTokenInterface::TYPE_REFRESH => $mandatoryClaims,
      ];

      return $this;
    }

    $this->mandatoryClaims = $mandatoryClaims;
    return $this;
  }

  /**
   * @param string             $jwtTokenString
   * @param OidcGrantInterface $grant
   *
   * @return OidcAccessTokenInterface
   * @throws InvalidAccessTokenException
   */
  private function createAccessToken(string $jwtTokenString, OidcGrantInterface $grant): OidcAccessTokenInterface {
    try {
      $jwt = $this->createJWT($jwtTokenString, $grant instanceof ClientCredentialsGrant ? OidcTokenInterface::TYPE_ACCESS_CLIENT_CREDENTIALS : OidcTokenInterface::TYPE_ACCESS);
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
      $jwt = $this->createJWT($jwtTokenString, OidcTokenInterface::TYPE_ID, $headers);
    } catch (\Exception $e) {
      throw new InvalidIdTokenException($e->getMessage());
    }

    $idToken = IdToken::createFromJwt($jwt, $jwtTokenString);
    $alg = $headers['alg'];

    if ($idToken->getAtHash() && !OpenIdHashHelper::compare($jwtAccessTokenString, $idToken->getAtHash(), $alg)) {
      throw new InvalidIdTokenException('at_hash did not match');
    }

    if ($grant instanceof AuthorizationCodeGrant && $idToken->getCHash() && !OpenIdHashHelper::compare($grant->getCode(), $idToken->getCHash(), $alg)) {
      throw new InvalidIdTokenException('c_hash did not match');
    }

    $sHash = $idToken->getPayload()['s_hash'] ?? null;
    if ($sHash && $grant instanceof AuthorizationCodeGrant && $grant->getState() && !OpenIdHashHelper::compare($grant->getState(), $sHash, $alg)) {
      throw new InvalidIdTokenException('s_hash did not match');
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
      $jwt = $this->createJWT($jwtTokenString, OidcTokenInterface::TYPE_REFRESH);
    } catch (\Exception $e) {
      throw new InvalidRefreshTokenException($e->getMessage());
    }
    return RefreshToken::createFromJwt($jwt, $jwtTokenString);
  }

  /**
   * @param string     $jwtString
   * @param string     $type
   * @param array|null $headers
   *
   * @return array
   * @throws ProviderException
   * @throws \JsonException
   * @throws \Exception
   */
  private function createJWT(string $jwtString, string $type, ?array &$headers = null): array {
    $jwsLoader = $this->getJwsLoader($type);

    if ($this->openIdProviderService->getClientConfiguration()->isValidateAccessTokens()) {
      $jws = $jwsLoader->loadAndVerifyWithKeySet($jwtString, $this->openIdProviderService->getPublicKey(), $signature);
    } else {
      $jws = $jwsLoader->getSerializerManager()->unserialize($jwtString);
      $signature = 0;
    }

    $claims = JsonConverter::decode($jws->getPayload());
    $this->getClaimChecker($type)->check($claims, $this->getMandatoryClaims($type));

    $headers = $jws->getSignature($signature)->getProtectedHeader();
    return $claims;
  }

  /**
   * @param string $type
   *
   * @return JWSLoader
   */
  private function getJwsLoader(string $type): JWSLoader {
    return $this->jwsLoaders[$type];
  }

  /**
   * @param string $type
   *
   * @return ClaimCheckerManager
   */
  private function getClaimChecker(string $type): ClaimCheckerManager {
    return $this->claimCheckers[$type];
  }

  /**
   * @param string $type
   *
   * @return string[]
   */
  private function getMandatoryClaims(string $type): array {
    return $this->mandatoryClaims[$type] ?? [];
  }

}
