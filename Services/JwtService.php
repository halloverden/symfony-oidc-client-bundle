<?php

namespace HalloVerden\Oidc\ClientBundle\Services;

use HalloVerden\Oidc\ClientBundle\Interfaces\JwtServiceInterface;
use Jose\Bundle\JoseFramework\Services\JWSBuilderFactory;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\Serializer\JWSSerializerManagerFactory;


class JwtService implements JwtServiceInterface {
  private JWKSet $jwkSet;

  /**
   * JwtService constructor.
   */
  public function __construct(
    private readonly JWSBuilderFactory $jwsBuilderFactory,
    private readonly JWSSerializerManagerFactory $jwsSerializerManagerFactory,
    iterable $jwks = []
  ) {
    $this->jwkSet = new JWKSet($jwks instanceof \Traversable ? iterator_to_array($jwks) : (array) $jwks);
  }

  /**
   * @inheritDoc
   */
  public function createJwt(string $payload, string $jwkId, string $serializerName): string {
    $jwk = $this->jwkSet->get($jwkId);
    $jwsBuilder = $this->jwsBuilderFactory->create([$jwk->get('alg')]);

    $jws = $jwsBuilder
      ->create()
      ->withPayload($payload)
      ->addSignature($jwk, ['alg' => $jwk->get('alg'), 'kid' => $jwkId])
      ->build();

    return $this->jwsSerializerManagerFactory->create([$serializerName])->serialize($serializerName, $jws);
  }

}
