<?php

namespace HalloVerden\Oidc\ClientBundle\Helpers;

/**
 * Class Base64Helper
 *
 * @package HalloVerden\Oidc\ClientBundle\Helpers
 */
class Base64Helper {

  /**
   * @param string $data
   *
   * @return string
   */
  public static function base64url_encode(string $data): string {
    return \str_replace('=', '', \strtr(\base64_encode($data), '+/', '-_'));
  }

  /**
   * @param string $data
   *
   * @return string|null
   */
  public static function base64url_decode(string $data): ?string {
    $remainder = \strlen($data) % 4;
    if ($remainder) {
      $padlen = 4 - $remainder;
      $data .= \str_repeat('=', $padlen);
    }
    return \base64_decode(\strtr($data, '-_', '+/'));
  }

}
