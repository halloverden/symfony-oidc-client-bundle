<?php


namespace HalloVerden\Oidc\ClientBundle\Events;


use HalloVerden\Oidc\ClientBundle\Exception\OauthAuthorizeException;
use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderServiceInterface;


class AuthorizationErrorEvent {

  /**
   * @var OpenIdProviderServiceInterface
   */
  private $openIdProviderService;

  /**
   * @var OauthAuthorizeException
   */
  private $authorizeException;

  /**
   * AuthorizationErrorEvent constructor.
   *
   * @param OpenIdProviderServiceInterface $openIdProviderService
   * @param OauthAuthorizeException        $authorizeException
   */
  public function __construct(OpenIdProviderServiceInterface $openIdProviderService, OauthAuthorizeException $authorizeException) {
    $this->openIdProviderService = $openIdProviderService;
    $this->authorizeException = $authorizeException;
  }

  /**
   * @return OpenIdProviderServiceInterface
   */
  public function getOpenIdProviderService(): OpenIdProviderServiceInterface {
    return $this->openIdProviderService;
  }

  /**
   * @return OauthAuthorizeException
   */
  public function getAuthorizeException(): OauthAuthorizeException {
    return $this->authorizeException;
  }

}
