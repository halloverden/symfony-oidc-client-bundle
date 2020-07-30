<?php


namespace HalloVerden\Oidc\ClientBundle\Client;


class ClientConfiguration {

  /**
   * @var string
   */
  private $issuer;

  /**
   * @var string
   */
  private $clientId;

  /**
   * @var string|null
   */
  private $secret;

  /**
   * @var string|null
   */
  private $redirectUri;

  /**
   * @var string|null
   */
  private $openIdConfigurationEndpoint;

  /**
   * @var string|null
   */
  private $responseType;

  /**
   * @var string|null
   */
  private $responseMode;

  /**
   * @var bool
   */
  private $pkce;

  /**
   * @var string
   */
  private $scope;

  /**
   * ClientConfiguration constructor.
   *
   * @param string $issuer
   */
  public function __construct(string $issuer) {
    $this->issuer = $issuer;
  }

  /**
   * @return string
   */
  public function getIssuer(): string {
    return $this->issuer;
  }

  /**
   * @param string $issuer
   *
   * @return self
   */
  public function setIssuer(string $issuer): self {
    $this->issuer = $issuer;
    return $this;
  }

  /**
   * @return string
   */
  public function getClientId(): string {
    return $this->clientId;
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
   * @return string|null
   */
  public function getClientSecret(): ?string {
    return $this->secret;
  }

  /**
   * @param string|null $secret
   *
   * @return self
   */
  public function setClientSecret(?string $secret): self {
    $this->secret = $secret;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getRedirectUri(): ?string {
    return $this->redirectUri;
  }

  /**
   * @param string|null $redirectUri
   *
   * @return self
   */
  public function setRedirectUri(?string $redirectUri): self {
    $this->redirectUri = $redirectUri;
    return $this;
  }

  /**
   * @return string
   */
  public function getOpenIdConfigurationEndpoint(): string {
    return $this->openIdConfigurationEndpoint ?: $this->generateOpenIdConfigurationEndpoint();
  }

  /**
   * @return string
   */
  private function generateOpenIdConfigurationEndpoint(): string {
    $generatedOpenIdConfigurationEndpoint =  $this->issuer . '/.well-known/openid-configuration';
    $this->setOpenIdConfigurationEndpoint($generatedOpenIdConfigurationEndpoint);
    return $generatedOpenIdConfigurationEndpoint;
  }

  /**
   * @param string|null $openIdConfigurationEndpoint
   *
   * @return self
   */
  public function setOpenIdConfigurationEndpoint(?string $openIdConfigurationEndpoint): self {
    $this->openIdConfigurationEndpoint = $openIdConfigurationEndpoint;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getResponseType(): ?string {
    return $this->responseType;
  }

  /**
   * @param string|null $responseType
   *
   * @return self
   */
  public function setResponseType(?string $responseType): self {
    $this->responseType = $responseType;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getResponseMode(): ?string {
    return $this->responseMode;
  }

  /**
   * @param string|null $responseMode
   *
   * @return self
   */
  public function setResponseMode(?string $responseMode): self {
    $this->responseMode = $responseMode;
    return $this;
  }

  /**
   * @return bool
   */
  public function isPkce(): bool {
    return $this->pkce;
  }

  /**
   * @param bool $pkce
   *
   * @return self
   */
  public function setPkce(bool $pkce): self {
    $this->pkce = $pkce;
    return $this;
  }

  /**
   * @return string
   */
  public function getScope(): string {
    return $this->scope;
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

}
