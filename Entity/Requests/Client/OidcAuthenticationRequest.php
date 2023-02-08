<?php


namespace HalloVerden\Oidc\ClientBundle\Entity\Requests\Client;

use HalloVerden\Contracts\Oidc\OidcAuthCodeInterface;
use HalloVerden\Contracts\Oidc\Requests\OidcAuthenticationRequestInterface;
use HalloVerden\Oidc\ClientBundle\Client\ClientConfiguration;
use HalloVerden\Oidc\ClientBundle\Helpers\Base64Helper;
use HalloVerden\Oidc\ClientBundle\Helpers\RandomHelper;
use HalloVerden\Oidc\ClientBundle\Provider\ProviderConfiguration;

/**
 * Class OidcAuthenticationRequest
 *
 * @package HalloVerden\Oidc\ClientBundle\Entity\Requests\Client
 */
class OidcAuthenticationRequest implements OidcAuthenticationRequestInterface {
  private string $scope;
  private string $clientId;
  private string $redirectUri;
  private string $responseType;
  private ?string $prompt = null;
  private ?string $nonce = null;
  private ?string $responseMode = null;
  private ?string $state = null;
  private ?string $codeChallengeMethod = null;
  private ?string $codeChallenge = null;
  private ?string $codeVerifier = null;
  private ?string $acrValues = null;
  private ?string $uiLocales = null;
  private string $authorizeUrl;

  /**
   * @param ClientConfiguration   $clientConfiguration
   * @param ProviderConfiguration $providerConfiguration
   *
   * @return $this
   */
  public static function createFromConfigs(ClientConfiguration $clientConfiguration, ProviderConfiguration $providerConfiguration): self {
    $clientId = $clientConfiguration->getClientId();
    if (null === $clientId) {
      throw new \RuntimeException('"client_id" is required to create authentication request.');
    }

    $request = (new static())
      ->setScope($clientConfiguration->getScope())
      ->setClientId($clientId)
      ->setRedirectUri($clientConfiguration->getRedirectUri())
      ->setResponseType($clientConfiguration->getResponseType())
      ->setNonce(RandomHelper::generateRandomString($clientConfiguration->getNonceParameterLength(), true))
      ->setResponseMode($clientConfiguration->getResponseMode())
      ->setState(RandomHelper::generateRandomString($clientConfiguration->getStateParameterLength(), true))
      ->setAuthorizeUrl($providerConfiguration->getAuthorizationEndpoint())
      ->setAcrValues($clientConfiguration->getAcrValues())
      ->setUiLocales($clientConfiguration->getUiLocales())
    ;

    if ($clientConfiguration->isPkceEnabled() && null !== ($codeChallengeMethodsSupported = $providerConfiguration->getCodeChallengeMethodsSupported())) {
      $verifier = RandomHelper::generateRandomString(128, true);

      if (\in_array(OidcAuthCodeInterface::CHALLENGE_METHOD_S256, $codeChallengeMethodsSupported)) {
        $request->setCodeChallenge(Base64Helper::base64url_encode(pack('H*', hash('sha256', $verifier))))
          ->setCodeChallengeMethod(OidcAuthCodeInterface::CHALLENGE_METHOD_S256)
          ->setCodeVerifier($verifier);
      } elseif (\in_array(OidcAuthCodeInterface::CHALLENGE_METHOD_PLAIN, $codeChallengeMethodsSupported)) {
        $request->setCodeChallenge($verifier)
          ->setCodeChallengeMethod(OidcAuthCodeInterface::CHALLENGE_METHOD_PLAIN)
          ->setCodeVerifier($verifier);
      }
    }

    return $request;
  }

  /**
   * @inheritDoc
   */
  public function getClientIdParam(): string {
    return $this->clientId;
  }

  /**
   * @inheritDoc
   */
  public function getScopeParam(): string {
    return $this->scope;
  }

  /**
   * @inheritDoc
   */
  public function getResponseTypeParam(): string {
    return $this->responseType;
  }

  /**
   * @inheritDoc
   */
  public function getStateParam(): ?string {
    return $this->state;
  }

  /**
   * @inheritDoc
   */
  public function getRedirectUriParam(): string {
    return $this->redirectUri;
  }

  /**
   * @inheritDoc
   */
  public function getResponseModeParam(): ?string {
    return $this->responseMode;
  }

  /**
   * @inheritDoc
   */
  public function getNonceParam(): ?string {
    return $this->nonce;
  }

  /**
   * @inheritDoc
   */
  public function getDisplayParam(): ?string {
    return null;
  }

  /**
   * @inheritDoc
   */
  public function getPromptParam(): ?string {
    return $this->prompt;
  }

  /**
   * @inheritDoc
   */
  public function getMaxAgeParam(): ?int {
    return null;
  }

  /**
   * @inheritDoc
   */
  public function getUiLocalesParam(): ?string {
    return $this->uiLocales;
  }

  /**
   * @inheritDoc
   */
  public function getIdTokenHintParam(): ?string {
    return null;
  }

  /**
   * @inheritDoc
   */
  public function getLoginHintParam(): ?string {
    return null;
  }

  /**
   * @inheritDoc
   */
  public function getAcrValuesParam(): ?string {
    return null;
  }

  /**
   * @inheritDoc
   */
  public function getRequestParam(): ?string {
    return null;
  }

  /**
   * @inheritDoc
   */
  public function getRequestUriParam(): ?string {
    return null;
  }

  /**
   * @inheritDoc
   */
  public function getRegistrationParam(): ?string {
    return null;
  }

  /**
   * @inheritDoc
   */
  public function getCodeChallengeParam(): ?string {
    return $this->codeChallenge;
  }

  /**
   * @inheritDoc
   */
  public function getCodeChallengeMethodParam(): ?string {
    return $this->codeChallengeMethod;
  }

  /**
   * @return string|null
   */
  public function getCodeVerifier(): ?string {
    return $this->codeVerifier;
  }

  /**
   * @param string|null $response_mode
   *
   * @return static
   */
  public function setResponseMode(?string $response_mode): OidcAuthenticationRequestInterface {
    $this->responseMode = $response_mode;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getRequestUrl(): string {
    return $this->authorizeUrl . '?' . http_build_query([
        'scope' => $this->getScopeParam(),
        'client_id' => $this->getClientIdParam(),
        'redirect_uri' => $this->getRedirectUriParam(),
        'response_type' => $this->getResponseTypeParam(),
        'nonce' => $this->getNonceParam(),
        'state' => $this->getStateParam(),
        'prompt' => $this->getPromptParam(),
        'response_mode' => $this->getResponseModeParam(),
        'code_challenge' => $this->getCodeChallengeParam(),
        'code_challenge_method' => $this->getCodeChallengeMethodParam(),
        'acr_values' => $this->getAcrValues(),
        'ui_locales' => $this->getUiLocalesParam(),
      ]);
  }

  /**
   * @param string $scope
   *
   * @return self
   */
  public function setScope(string $scope): self {
    $this->scope = $scope;
    return $this;
  }

  /**
   * @param string $clientId
   *
   * @return self
   */
  public function setClientId(string $clientId): self {
    $this->clientId = $clientId;
    return $this;
  }

  /**
   * @param string $redirectUri
   *
   * @return self
   */
  public function setRedirectUri(string $redirectUri): self {
    $this->redirectUri = $redirectUri;
    return $this;
  }

  /**
   * @param string $responseType
   *
   * @return self
   */
  public function setResponseType(string $responseType): self {
    $this->responseType = $responseType;
    return $this;
  }

  /**
   * @param string $prompt
   *
   * @return self
   */
  public function setPrompt(string $prompt): self {
    $this->prompt = $prompt;
    return $this;
  }

  /**
   * @param string $nonce
   *
   * @return self
   */
  public function setNonce(string $nonce): self {
    $this->nonce = $nonce;
    return $this;
  }

  /**
   * @param string $state
   *
   * @return self
   */
  public function setState(string $state): self {
    $this->state = $state;
    return $this;
  }

  /**
   * @param string $codeChallengeMethod
   *
   * @return self
   */
  public function setCodeChallengeMethod(string $codeChallengeMethod): self {
    $this->codeChallengeMethod = $codeChallengeMethod;
    return $this;
  }

  /**
   * @param string $codeChallenge
   *
   * @return self
   */
  public function setCodeChallenge(string $codeChallenge): self {
    $this->codeChallenge = $codeChallenge;
    return $this;
  }

  /**
   * @param string $authorizeUrl
   *
   * @return self
   */
  public function setAuthorizeUrl(string $authorizeUrl): self {
    $this->authorizeUrl = $authorizeUrl;
    return $this;
  }

  /**
   * @param string|null $codeVerifier
   *
   * @return self
   */
  public function setCodeVerifier(?string $codeVerifier): self {
    $this->codeVerifier = $codeVerifier;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getAcrValues(): ?string {
    return $this->acrValues;
  }

  /**
   * @param string|null $acrValues
   *
   * @return self
   */
  public function setAcrValues(?string $acrValues): self {
    $this->acrValues = $acrValues;
    return $this;
  }

  /**
   * @param string|null $uiLocales
   *
   * @return OidcAuthenticationRequest
   */
  public function setUiLocales(?string $uiLocales): self {
    $this->uiLocales = $uiLocales;
    return $this;
  }

}
