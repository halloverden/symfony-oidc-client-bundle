<?php


namespace HalloVerden\Oidc\ClientBundle\Helpers;


use Base64Url\Base64Url;

class OpenIdHashHelper {

  /**
   *  base64url encoding of the left-most half of the hash of the octets of the ASCII representation of the value,
   *    where the hash algorithm used is the hash algorithm used in the alg Header Parameter of the ID Token's JOSE Header.
   *    For instance, if the alg is RS256, hash the access_token value with SHA-256, then take the left-most 128 bits and base64url encode them.
   *
   * @param string $value
   * @param string $alg
   *
   * @return string
   */
  public static function createHash(string $value, string $alg): string {
    $hash = hash(self::signatureToHashAlgorithm($alg), $value, true);
    $at_hash = substr($hash, 0, strlen($hash) / 2);
    return Base64Url::encode($at_hash);
  }

  /**
   * @param string $value
   * @param string $hash
   * @param string $alg
   *
   * @return bool
   */
  public static function compare(string $value, string $hash, string $alg): bool {
    return self::createHash($value, $alg) === $hash;
  }

  /**
   * Maps the signature algorithm (RS256, HS256 etc) to the hash algorithm.
   *
   * This will cover most scenarios. see https://www.iana.org/assignments/jose/jose.xhtml#web-signature-encryption-header-parameters if you want support everything
   * We use RS256 for everything, so this won't be an issue!
   *
   * @param string $signatureAlgorithm
   *
   * @return string
   */
  private static function signatureToHashAlgorithm(string $signatureAlgorithm): string {
    return 'sha' . substr($signatureAlgorithm, -3);
  }

}
