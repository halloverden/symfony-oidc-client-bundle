<?php

namespace HalloVerden\Oidc\ClientBundle\Interfaces;

interface ClientCredentialsTokenProviderServiceInterface {

  /**
   * @return string
   */
  public function getToken(): string;

}
