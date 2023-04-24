<?php

namespace HalloVerden\Oidc\ClientBundle\Jwt;

use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderServiceInterface;
use Jose\Component\Core\Algorithm;
use Jose\Component\Core\JWK;
use Jose\Component\Core\JWKSet;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Traversable;

class CachedJwkSet extends JWKSet {
  private const CACHE_KEY = '_jwk_set';

  private ?JWKSet $jwkSet = null;

  /**
   * CachedJwkSet constructor.
   */
  public function __construct(
    private readonly OpenIdProviderServiceInterface $openIdProviderService,
    private readonly ?CacheInterface $cache = null
  ) {
    parent::__construct([]);
  }

  public function all(): array {
    return $this->getJwkSet(false)->all();
  }

  public function with(JWK $jwk): JWKSet {
    return $this->getJwkSet(false)->with($jwk);
  }

  public function without(int|string $key): JWKSet {
    $this->clearCacheIfKeyNotInCache($key);
    return $this->getJwkSet()->without($key);
  }

  public function has(int|string $index): bool {
    $this->clearCacheIfKeyNotInCache($index);
    return $this->getJwkSet()->has($index);
  }

  public function get(int|string $index): JWK {
    $this->clearCacheIfKeyNotInCache($index);
    return $this->getJwkSet()->get($index);
  }

  public function jsonSerialize(): array {
    return $this->getJwkSet(false)->jsonSerialize();
  }

  public function count($mode = COUNT_NORMAL): int {
    return $this->getJwkSet(false)->count($mode);
  }

  public function selectKey(string $type, ?Algorithm $algorithm = null, array $restrictions = []): ?JWK {
    return $this->getJwkSet(false)->selectKey($type, $algorithm, $restrictions);
  }

  public function getIterator(): Traversable {
    return $this->getJwkSet(false)->getIterator();
  }

  /**
   * @param bool $useCache
   *
   * @return JWKSet
   */
  private function getJwkSet(bool $useCache = true): JWKSet {
    if (!$this->cache) {
      return $this->jwkSet ??= $this->openIdProviderService->getPublicKey();
    }

    return $this->jwkSet ??= $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) {
      $item->expiresAfter(3600);
      return $this->openIdProviderService->getPublicKey();
    }, $useCache ? null : INF);
  }

  /**
   * @param string|int $key
   *
   * @return void
   */
  private function clearCacheIfKeyNotInCache(string|int $key): void {
    if ($this->getJwkSet()->has($key)) {
      return;
    }

    $this->cache?->delete(self::CACHE_KEY);
  }

}
