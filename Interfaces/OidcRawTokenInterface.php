<?php


namespace HalloVerden\Oidc\ClientBundle\Interfaces;


interface OidcRawTokenInterface {

  /**
   * @return string
   */
  public function getRawToken(): string;

}
