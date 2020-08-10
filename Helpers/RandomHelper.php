<?php


namespace HalloVerden\Oidc\ClientBundle\Helpers;


class RandomHelper {

  /**
   * @param int $length
   * @param bool $urlSafe
   * @param bool $includeNumbers
   * @return string
   */
  public static function generateRandomString($length = 10, $urlSafe = false, $includeNumbers = true) {
    $UrlUnsafecharacters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/-+*%&¤#\"\\=(){}';
    $UrlSafecharacters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $characters = $urlSafe ? $UrlSafecharacters : $UrlUnsafecharacters;

    if ($includeNumbers) {
      $characters .= '1234567890';
    }

    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
  }

}
