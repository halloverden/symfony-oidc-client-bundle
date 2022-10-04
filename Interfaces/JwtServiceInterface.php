<?php

namespace HalloVerden\Oidc\ClientBundle\Interfaces;

interface JwtServiceInterface {

  /**
   * @param string $payload
   * @param string $jwkId
   * @param string $serializerName
   *
   * @return string
   */
  public function createJwt(string $payload, string $jwkId, string $serializerName): string;

}
