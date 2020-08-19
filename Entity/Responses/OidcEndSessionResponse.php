<?php


namespace HalloVerden\Oidc\ClientBundle\Entity\Responses;


use HalloVerden\Contracts\Oidc\OidcEndSessionResponseInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OidcEndSessionResponse implements OidcEndSessionResponseInterface {

  /**
   * @var string
   */
  private $uri;

  /**
   * OidcEndSessionResponse constructor.
   *
   * @param string $uri
   */
  public function __construct(string $uri) {
    $this->uri = $uri;
  }

  /**
   * @inheritDoc
   */
  public function getRedirectResponse(): RedirectResponse {
    return new RedirectResponse($this->uri);
  }

}
