<?php

namespace HalloVerden\Oidc\ClientBundle\Services;

use HalloVerden\Oidc\ClientBundle\Entity\Grant\ClientCredentialsGrant;
use HalloVerden\Oidc\ClientBundle\Interfaces\ClientCredentialsTokenProviderServiceInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OidcRawTokenInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderServiceInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class ClientCredentialsTokenProviderService implements ClientCredentialsTokenProviderServiceInterface {

  /**
   * ClientCredentialsTokenProviderService constructor.
   */
  public function __construct(
    private OpenIdProviderServiceInterface $openIdProviderService,
    private CacheInterface                 $cache,
    private string                         $cacheKey,
    private ?array                         $scopes = null,
  ) {
  }

  /**
   * @inheritDoc
   * @throws InvalidArgumentException
   */
  public function getToken(): string {
    return $this->cache->get($this->cacheKey, function (ItemInterface $item): string {
      $accessToken = $this->openIdProviderService->getTokenResponse(new ClientCredentialsGrant($this->scopes))->getAccessToken();

      if (!$accessToken instanceof OidcRawTokenInterface) {
        throw new \LogicException(\sprintf('$accessToken is not instance of %s', OidcRawTokenInterface::class));
      }

      $item->expiresAfter(\max($accessToken->getExp() - time() - 300, 0));

      return $accessToken->getRawToken();
    });
  }

}
