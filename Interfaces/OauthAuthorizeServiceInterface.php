<?php


namespace HalloVerden\Oidc\ClientBundle\Interfaces;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface OauthAuthorizeServiceInterface
 *
 * @package HalloVerden\Oidc\ClientBundle\Interfaces
 */
interface OauthAuthorizeServiceInterface {
  const ERROR_UNABLE_TO_AUTHORIZE = 'UNABLE_TO_AUTHORIZE';
  const ERROR_INVALID_SESSION_STATE = 'INVALID_SESSION_STATE';
  const ERROR_MISSING_AUTHORIZE_SESSION = 'MISSING_AUTHORIZE_SESSION';
  const ERROR_UNABLE_TO_REFRESH_TOKEN = 'UNABLE_TO_REFRESH_TOKEN';
  const ERROR_INVALID_ID_TOKEN = 'INVALID_ID_TOKEN';
  const ERROR_INVALID_ACCESS_TOKEN = 'INVALID_ACCESS_TOKEN';
  const ERROR_INVALID_REFRESH_TOKEN = 'INVALID_REFRESH_TOKEN';
  const ERROR_MISSING_ID_TOKEN = 'MISSING_ID_TOKEN';
  const ERROR_UNKNOWN_ERROR = 'UNKNOWN_ERROR';

  /**
   * @param Request $request
   *
   * @return RedirectResponse
   */
  public function handleAuthorize(Request $request): RedirectResponse;

  /**
   * @param Request $request
   *
   * @return RedirectResponse
   */
  public function handleAuthCode(Request $request): RedirectResponse;

}
