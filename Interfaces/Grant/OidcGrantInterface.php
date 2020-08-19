<?php


namespace HalloVerden\Oidc\ClientBundle\Interfaces\Grant;


interface OidcGrantInterface {

  /**
   * @return string
   */
  public function getTypeName(): string;

  /**
   * @return array
   */
  public function getRequestData(): array;

}
