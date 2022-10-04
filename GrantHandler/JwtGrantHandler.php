<?php

namespace HalloVerden\Oidc\ClientBundle\GrantHandler;

use HalloVerden\Oidc\ClientBundle\Client\ClientConfiguration;
use HalloVerden\Oidc\ClientBundle\Entity\Grant\JwtGrant;
use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\GrantHandlerInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\JwtServiceInterface;
use HalloVerden\Oidc\ClientBundle\Provider\ProviderConfiguration;
use Jose\Component\Core\Util\JsonConverter;

final class JwtGrantHandler implements GrantHandlerInterface {

  /**
   * JwtGrantHandler constructor.
   */
  public function __construct(private readonly JwtServiceInterface $jwtService) {
  }

  /**
   * @inheritDoc
   */
  public function supports(OidcGrantInterface $grant, ClientConfiguration $clientConfiguration, ProviderConfiguration $providerConfiguration): bool {
    return $grant instanceof JwtGrant;
  }

  /**
   * @inheritDoc
   */
  public function getRequestData(OidcGrantInterface $grant, ClientConfiguration $clientConfiguration, ProviderConfiguration $providerConfiguration): array {
    if (!$grant instanceof JwtGrant) {
      throw new \LogicException(\sprintf('$grant should be instance of %s', JwtGrant::class));
    }

    return [
      'grant_type' => $grant->getTypeName(),
      'assertion' => $grant->getJwt() ?? $this->createJwt($clientConfiguration, $providerConfiguration)
    ];
  }

  /**
   * @param ClientConfiguration   $clientConfiguration
   * @param ProviderConfiguration $providerConfiguration
   *
   * @return string
   */
  private function createJwt(ClientConfiguration $clientConfiguration, ProviderConfiguration $providerConfiguration): string {
    $now = time();

    $payload = [
      'aud' => $providerConfiguration->getIssuer(),
      'iss' => $clientConfiguration->getClientId(),
      'scope' => $clientConfiguration->getScope(),
      'iat' => $now,
      'exp' => $now + 120
    ];

    return $this->jwtService->createJwt(JsonConverter::encode($payload), $clientConfiguration->getJwkId(), $clientConfiguration->getJwtSerializer());
  }

}
