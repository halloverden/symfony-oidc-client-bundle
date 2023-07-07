<?php


namespace HalloVerden\Oidc\ClientBundle\Client;


class ClientConfiguration {
  private ?string $issuer;
  private ?string $clientId = null;
  private ?string $secret = null;
  private ?string $redirectUri = null;
  private ?string $openIdConfigurationEndpoint = null;
  private ?string $responseType = null;
  private ?string $responseMode = null;
  private ?string $acrValues = null;
  private ?string $uiLocales = null;
  private string $scope;
  private bool $pkceEnabled = false;
  private int $stateParameterLength = 10;
  private int $nonceParameterLength = 10;
  private ?string $jwkId;
  private string $jwtSerializer;
  private array $jtwCustomClaims = [];
  private bool $validateAccessTokens = true;

  /**
   * ClientConfiguration constructor.
   *
   * @param string|null $issuer
   */
  public function __construct(string $issuer = null) {
    $this->issuer = $issuer;
  }

  /**
   * @return string|null
   */
  public function getIssuer(): ?string {
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
   * @return string|null
   */
  public function getClientId(): ?string {
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
   * @return string|null
   */
  public function getUiLocales(): ?string {
    return $this->uiLocales;
  }

  /**
   * @param string|null $uiLocales
   *
   * @return ClientConfiguration
   */
  public function setUiLocales(?string $uiLocales): self {
    $this->uiLocales = $uiLocales;
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

  /**
   * @return bool
   */
  public function isPkceEnabled(): bool {
    return $this->pkceEnabled;
  }

  /**
   * @param bool $pkceEnabled
   *
   * @return self
   */
  public function setPkceEnabled(bool $pkceEnabled): self {
    $this->pkceEnabled = $pkceEnabled;
    return $this;
  }

  /**
   * @return int
   */
  public function getStateParameterLength(): int {
    return $this->stateParameterLength;
  }

  /**
   * @param int $stateParameterLength
   *
   * @return ClientConfiguration
   */
  public function setStateParameterLength(int $stateParameterLength): self {
    $this->stateParameterLength = $stateParameterLength;
    return $this;
  }

  /**
   * @return int
   */
  public function getNonceParameterLength(): int {
    return $this->nonceParameterLength;
  }

  /**
   * @param int $nonceParameterLength
   *
   * @return ClientConfiguration
   */
  public function setNonceParameterLength(int $nonceParameterLength): self {
    $this->nonceParameterLength = $nonceParameterLength;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getJwkId(): ?string {
    return $this->jwkId;
  }

  /**
   * @param string|null $jwkId
   *
   * @return self
   */
  public function setJwkId(?string $jwkId): self {
    $this->jwkId = $jwkId;
    return $this;
  }

  /**
   * @return string
   */
  public function getJwtSerializer(): string {
    return $this->jwtSerializer;
  }

  /**
   * @param string $jwtSerializer
   *
   * @return self
   */
  public function setJwtSerializer(string $jwtSerializer): self {
    $this->jwtSerializer = $jwtSerializer;
    return $this;
  }

  /**
   * @return array
   */
  public function getJtwCustomClaims(): array {
    return $this->jtwCustomClaims;
  }

  /**
   * @param array $jtwCustomClaims
   *
   * @return self
   */
  public function setJtwCustomClaims(array $jtwCustomClaims): self {
    $this->jtwCustomClaims = $jtwCustomClaims;
    return $this;
  }

  /**
   * @return bool
   */
  public function isValidateAccessTokens(): bool {
    return $this->validateAccessTokens;
  }

  /**
   * @param bool $validateAccessTokens
   *
   * @return static
   */
  public function setValidateAccessTokens(bool $validateAccessTokens): static {
    $this->validateAccessTokens = $validateAccessTokens;
    return $this;
  }

}
