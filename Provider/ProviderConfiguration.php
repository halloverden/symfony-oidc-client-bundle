<?php


namespace HalloVerden\Oidc\ClientBundle\Provider;

use JMS\Serializer\Annotation as Serializer;

class ProviderConfiguration {

  #[Serializer\SerializedName('issuer')]
  private string $issuer;

  #[Serializer\SerializedName('authorization_endpoint')]
  private string $authorizationEndpoint;

  #[Serializer\SerializedName('token_endpoint')]
  private string $tokenEndpoint;

  #[Serializer\SerializedName('userinfo_endpoint')]
  private string $userinfoEndpoint;

  #[Serializer\SerializedName('revocation_endpoint')]
  private string $revocationEndpoint;

  #[Serializer\SerializedName('end_session_endpoint')]
  private string $endSessionEndpoint;

  #[Serializer\SerializedName('jwks_uri')]
  private string $jwksUri;

  #[Serializer\SerializedName('scopes_supported')]
  #[Serializer\Type(name: 'array<string>')]
  private array $scopesSupported;

  #[Serializer\SerializedName('response_types_supported')]
  #[Serializer\Type(name: 'array<string>')]
  private array $responseTypesSupported;

  #[Serializer\SerializedName('response_modes_supported')]
  #[Serializer\Type(name: 'array<string>')]
  private array $responseModesSupported;

  #[Serializer\SerializedName('grant_types_supported')]
  #[Serializer\Type(name: 'array<string>')]
  private array $grantTypesSupported;

  #[Serializer\SerializedName('subject_types_supported')]
  #[Serializer\Type(name: 'array<string>')]
  private array $subjectTypesSupported;

  #[Serializer\SerializedName('id_token_signing_alg_values_supported')]
  #[Serializer\Type(name: 'array<string>')]
  private array $idTokenSigningAlgValuesSupported;

  #[Serializer\SerializedName('token_endpoint_auth_methods_supported')]
  #[Serializer\Type(name: 'array<string>')]
  private array $tokenEndpointAuthMethodsSupported;

  #[Serializer\SerializedName('claims_parameter_supported')]
  private bool $claimsParameterSupported;

  #[Serializer\SerializedName('request_parameter_supported')]
  private $requestParameterSupported;

  #[Serializer\SerializedName('request_uri_parameter_supported')]
  private bool $requestUriParameterSupported;

  #[Serializer\SerializedName('code_challenge_methods_supported')]
  #[Serializer\Type(name: 'array<string>')]
  private ?array $codeChallengeMethodsSupported = null;

  /**
   * @return string
   */
  public function getIssuer(): string {
    return $this->issuer;
  }

  /**
   * @return string
   */
  public function getAuthorizationEndpoint(): string {
    return $this->authorizationEndpoint;
  }

  /**
   * @return string
   */
  public function getTokenEndpoint(): string {
    return $this->tokenEndpoint;
  }

  /**
   * @return string
   */
  public function getUserinfoEndpoint(): string {
    return $this->userinfoEndpoint;
  }

  /**
   * @return string
   */
  public function getRevocationEndpoint(): string {
    return $this->revocationEndpoint;
  }

  /**
   * @return string
   */
  public function getEndSessionEndpoint(): string {
    return $this->endSessionEndpoint;
  }

  /**
   * @return string
   */
  public function getJwksUri(): string {
    return $this->jwksUri;
  }

  /**
   * @return string[]
   */
  public function getScopesSupported(): array {
    return $this->scopesSupported;
  }

  /**
   * @return string[]
   */
  public function getResponseTypesSupported(): array {
    return $this->responseTypesSupported;
  }

  /**
   * @return string[]
   */
  public function getResponseModesSupported(): array {
    return $this->responseModesSupported;
  }

  /**
   * @return string[]
   */
  public function getGrantTypesSupported(): array {
    return $this->grantTypesSupported;
  }

  /**
   * @return string[]
   */
  public function getSubjectTypesSupported(): array {
    return $this->subjectTypesSupported;
  }

  /**
   * @return string[]
   */
  public function getIdTokenSigningAlgValuesSupported(): array {
    return $this->idTokenSigningAlgValuesSupported;
  }

  /**
   * @return string[]
   */
  public function getTokenEndpointAuthMethodsSupported(): array {
    return $this->tokenEndpointAuthMethodsSupported;
  }

  /**
   * @return bool
   */
  public function isClaimsParameterSupported(): bool {
    return $this->claimsParameterSupported;
  }

  /**
   * @return bool
   */
  public function isRequestParameterSupported(): bool {
    return $this->requestParameterSupported;
  }

  /**
   * @return bool
   */
  public function isRequestUriParameterSupported(): bool {
    return $this->requestUriParameterSupported;
  }

  /**
   * @return array|null
   */
  public function getCodeChallengeMethodsSupported(): ?array {
    return $this->codeChallengeMethodsSupported;
  }

}
