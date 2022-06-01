<?php


namespace HalloVerden\Oidc\ClientBundle\Services;


use HalloVerden\Oidc\ClientBundle\Entity\Grant\AuthorizationCodeGrant;
use HalloVerden\Oidc\ClientBundle\Helpers\UriHelper;
use HalloVerden\Oidc\ClientBundle\Entity\OauthAuthorizeSession;
use HalloVerden\Oidc\ClientBundle\Entity\Requests\AuthorizeRequest;
use HalloVerden\Oidc\ClientBundle\Entity\Requests\HandleAuthCodeRequest;
use HalloVerden\Oidc\ClientBundle\Events\AuthorizationErrorEvent;
use HalloVerden\Oidc\ClientBundle\Events\AuthorizedEvent;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidAccessTokenException;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidIdTokenException;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidRefreshTokenException;
use HalloVerden\Oidc\ClientBundle\Exception\OauthAuthorizeException;
use HalloVerden\Oidc\ClientBundle\Exception\ProviderException;
use HalloVerden\Oidc\ClientBundle\Exception\InvalidTokenException;
use HalloVerden\Oidc\ClientBundle\Interfaces\OauthAuthorizeServiceInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderServiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class OauthAuthorizeService implements OauthAuthorizeServiceInterface {
  const SESSION_STATE_KEY = 'oauth2session';

  /**
   * @var OpenIdProviderServiceInterface
   */
  private $openIdProviderService;

  /**
   * @var SessionInterface
   */
  private $session;

  /**
   * @var string
   */
  private $authorizeSuccessUrl;

  /**
   * @var string
   */
  private $authorizeErrorUrl;

  /**
   * @var EventDispatcherInterface
   */
  private $dispatcher;

  /**
   * OauthAuthorizeService constructor.
   *
   * @param OpenIdProviderServiceInterface $openIdProviderService
   * @param SessionInterface               $session
   * @param EventDispatcherInterface       $dispatcher
   * @param string                         $authorizeSuccessUrl
   * @param string                         $authorizeErrorUrl
   */
  public function __construct(OpenIdProviderServiceInterface $openIdProviderService,
                              SessionInterface $session,
                              EventDispatcherInterface $dispatcher,
                              string $authorizeSuccessUrl,
                              string $authorizeErrorUrl) {
    $this->openIdProviderService = $openIdProviderService;
    $this->session = $session;
    $this->authorizeSuccessUrl = $authorizeSuccessUrl;
    $this->authorizeErrorUrl = $authorizeErrorUrl;
    $this->dispatcher = $dispatcher;
  }

  /**
   * @param Request $request
   *
   * @return RedirectResponse
   */
  public function handleAuthorize(Request $request): RedirectResponse {
    $authorizeRequest = $this->getAuthorizeRequest($request);

    try {
      $oauthAuthorizeRequest = $this->openIdProviderService->getAuthorizeRequest();

      $this->session->set(self::SESSION_STATE_KEY, new OauthAuthorizeSession(
        $oauthAuthorizeRequest->getStateParam(),
        $authorizeRequest->getSuccessUrl() ?: $this->authorizeSuccessUrl,
        $authorizeRequest->getErrorUrl() ?: $this->authorizeErrorUrl,
        $oauthAuthorizeRequest->getNonceParam()
      ));

      return new RedirectResponse($oauthAuthorizeRequest->getRequestUrl());
    } catch (ProviderException $e) {
      $response = $this->handleErrorException($this->providerExceptionToAuthorizeException($e));
      $this->removeSession();
      return $response;
    }
  }

  /**
   * @param Request $request
   *
   * @return RedirectResponse
   */
  public function handleAuthCode(Request $request): RedirectResponse {
    try {
      $authCodeRequest = $this->getAuthCodeRequest($request);

      $oauthAuthorizeSession = $this->getOauthAuthorizeSession();
      $this->validateSessionState($oauthAuthorizeSession, $authCodeRequest->getState());

      $tokenResponse = $this->openIdProviderService->getTokenResponse(new AuthorizationCodeGrant($authCodeRequest->getCode()));

      if ($idToken = $tokenResponse->getIdToken()) {
        $this->validateNonce($oauthAuthorizeSession, $idToken->getNonce());
      }

      $this->dispatcher->dispatch(new AuthorizedEvent(
        $this->openIdProviderService,
        $tokenResponse->getAccessToken(),
        $tokenResponse->getIdToken(),
        $tokenResponse->getRefreshToken()
      ));

      return new RedirectResponse($oauthAuthorizeSession->getSuccessUrl());
    } catch (OauthAuthorizeException $exception) {
      return $this->handleErrorException($exception);
    } catch (ProviderException $e) {
      return $this->handleErrorException($this->providerExceptionToAuthorizeException($e));
    } catch (InvalidTokenException $e) {
      return $this->handleErrorException($this->tokenExceptionToAuthorizeException($e));
    } catch (\Throwable $e) {
      return $this->handleErrorException(new OauthAuthorizeException(self::ERROR_UNKNOWN_ERROR, $e->getMessage(), $e));
    } finally {
      $this->removeSession();
    }
  }

  /**
   * @param Request $request
   *
   * @return AuthorizeRequest
   */
  private function getAuthorizeRequest(Request $request): AuthorizeRequest {
    return (new AuthorizeRequest())->setSuccessUrl($request->get('successUrl'))->setErrorUrl($request->get('errorUrl'));
  }

  /**
   * @param Request $request
   *
   * @return HandleAuthCodeRequest
   * @throws OauthAuthorizeException
   */
  private function getAuthCodeRequest(Request $request): HandleAuthCodeRequest {
    $code = $request->get('code');
    if (!$code) {
      throw new OauthAuthorizeException(self::ERROR_UNABLE_TO_AUTHORIZE, $request->get('error'));
    }

    return (new HandleAuthCodeRequest())->setCode($code)->setState($request->get('state'));
  }

  /**
   * @return OauthAuthorizeSession
   * @throws OauthAuthorizeException
   */
  private function getOauthAuthorizeSession(): OauthAuthorizeSession {
    $oauthAuthorizeSession = $this->session->get(self::SESSION_STATE_KEY);

    if (!$oauthAuthorizeSession instanceof OauthAuthorizeSession) {
      throw new OauthAuthorizeException(self::ERROR_MISSING_AUTHORIZE_SESSION);
    }

    return $oauthAuthorizeSession;
  }

  /**
   * @param OauthAuthorizeSession $oauthAuthorizeSession
   * @param string|null           $state
   *
   * @throws OauthAuthorizeException
   */
  private function validateSessionState(OauthAuthorizeSession $oauthAuthorizeSession, ?string $state): void {
    if ($oauthAuthorizeSession->getState() !== $state) {
      throw new OauthAuthorizeException(self::ERROR_INVALID_SESSION_STATE);
    }
  }

  /**
   * @param OauthAuthorizeSession $oauthAuthorizeSession
   * @param string|null           $nonce
   *
   * @throws OauthAuthorizeException
   */
  private function validateNonce(OauthAuthorizeSession $oauthAuthorizeSession, ?string $nonce): void {
    if ($oauthAuthorizeSession->getNonce() !== $nonce) {
      throw new OauthAuthorizeException(self::ERROR_INVALID_ID_TOKEN, 'nonce does no match');
    }
  }

  /**
   * @param OauthAuthorizeException $exception
   *
   * @return string
   */
  private function createErrorUrl(OauthAuthorizeException $exception): string {
    try {
      $url = $this->getOauthAuthorizeSession()->getErrorUrl();
    } catch (OauthAuthorizeException $e) {
      $url = $this->authorizeErrorUrl;
    }

    return (new URIHelper($url))
      ->addQueryParameter('error', $exception->getError())
      ->toString();
  }

  /**
   * @param ProviderException $providerException
   *
   * @return OauthAuthorizeException
   */
  private function providerExceptionToAuthorizeException(ProviderException $providerException): OauthAuthorizeException {
    $previous = $providerException->getPrevious();

    if ($previous instanceof ClientExceptionInterface) {
      /** @noinspection PhpUnhandledExceptionInspection */
      $data = $previous->getResponse()->toArray(false);

      if (isset($data['error'])) {
        return new OauthAuthorizeException($data['error'], $data['description'] ?? null, $providerException);
      }
    }

    return new OauthAuthorizeException(self::ERROR_UNKNOWN_ERROR, null, $providerException);
  }

  /**
   * @param InvalidTokenException $tokenInvalidException
   *
   * @return OauthAuthorizeException
   */
  private function tokenExceptionToAuthorizeException(InvalidTokenException $tokenInvalidException): OauthAuthorizeException {
    switch (true) {
      case $tokenInvalidException instanceof InvalidIdTokenException:
        return new OauthAuthorizeException(self::ERROR_INVALID_ID_TOKEN, null, $tokenInvalidException);
      case $tokenInvalidException instanceof InvalidAccessTokenException:
        return new OauthAuthorizeException(self::ERROR_INVALID_ACCESS_TOKEN, null, $tokenInvalidException);
      case $tokenInvalidException instanceof InvalidRefreshTokenException:
        return new OauthAuthorizeException(self::ERROR_INVALID_REFRESH_TOKEN, null, $tokenInvalidException);
      default:
        return new OauthAuthorizeException(self::ERROR_UNKNOWN_ERROR, null, $tokenInvalidException);
    }
  }

  /**
   * @param OauthAuthorizeException $exception
   *
   * @return RedirectResponse
   */
  private function handleErrorException(OauthAuthorizeException $exception): RedirectResponse {
    $this->dispatcher->dispatch(new AuthorizationErrorEvent($this->openIdProviderService, $exception));
    return new RedirectResponse($this->createErrorUrl($exception));
  }

  /**
   * Remove session
   */
  private function removeSession(): void {
    $this->session->remove(self::SESSION_STATE_KEY);
  }

}
