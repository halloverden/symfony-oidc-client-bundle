<?php


namespace HalloVerden\Oidc\ClientBundle\Events;


use HalloVerden\Oidc\ClientBundle\Exception\OauthAuthorizeException;

class AuthorizationErrorEvent {

  /**
   * @var OauthAuthorizeException
   */
  private $authorizeException;

  /**
   * AuthorizationErrorEvent constructor.
   *
   * @param OauthAuthorizeException $authorizeException
   */
  public function __construct(OauthAuthorizeException $authorizeException) {
    $this->authorizeException = $authorizeException;
  }

  /**
   * @return OauthAuthorizeException
   */
  public function getAuthorizeException(): OauthAuthorizeException {
    return $this->authorizeException;
  }

}
