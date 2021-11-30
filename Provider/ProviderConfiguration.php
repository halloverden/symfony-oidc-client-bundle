<?php


namespace HalloVerden\Oidc\ClientBundle\Provider;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class ProviderConfiguration
 *
 * @package HalloVerden\Oidc\ClientBundle\Provider
 *
 * @Serializer\ExclusionPolicy("ALL")
 */
class ProviderConfiguration {

  /**
   * @var string
   *
   * @Serializer\SerializedName("issuer")
   * @Serializer\Type(name="string")
   * @Serializer\Expose()
   */
  private $issuer;

  /**
   * @var string
   *
   * @Serializer\SerializedName("authorization_endpoint")
   * @Serializer\Type(name="string")
   * @Serializer\Expose()
   */
  private $authorizationEndpoint;

  /**
   * @var string
   *
   * @Serializer\SerializedName("token_endpoint")
   * @Serializer\Type(name="string")
   * @Serializer\Expose()
   */
  private $tokenEndpoint;

  /**
   * @var string
   *
   * @Serializer\SerializedName("userinfo_endpoint")
   * @Serializer\Type(name="string")
   * @Serializer\Expose()
   */
  private $userinfoEndpoint;

  /**
   * @var string
   *
   * @Serializer\SerializedName("revocation_endpoint")
   * @Serializer\Type(name="string")
   * @Serializer\Expose()
   */
  private $revocationEndpoint;

  /**
   * @var string
   *
   * @Serializer\SerializedName("end_session_endpoint")
   * @Serializer\Type(name="string")
   * @Serializer\Expose()
   */
  private $endSessionEndpoint;

  /**
   * @var string
   *
   * @Serializer\SerializedName("jwks_uri")
   * @Serializer\Type(name="string")
   * @Serializer\Expose()
   */
  private $jwksUri;

  /**
   * @var string[]
   *
   * @Serializer\SerializedName("scopes_supported")
   * @Serializer\Type(name="array<string>")
   * @Serializer\Expose()
   */
  private $scopesSupported;

  /**
   * @var string[]
   *
   * @Serializer\SerializedName("response_types_supported")
   * @Serializer\Type(name="array<string>")
   * @Serializer\Expose()
   */
  private $responseTypesSupported;

  /**
   * @var string[]
   *
   * @Serializer\SerializedName("response_modes_supported")
   * @Serializer\Type(name="array<string>")
   * @Serializer\Expose()
   */
  private $responseModesSupported;

  /**
   * @var string[]
   *
   * @Serializer\SerializedName("grant_types_supported")
   * @Serializer\Type(name="array<string>")
   * @Serializer\Expose()
   */
  private $grantTypesSupported;

  /**
   * @var string[]
   *
   * @Serializer\SerializedName("subject_types_supported")
   * @Serializer\Type(name="array<string>")
   * @Serializer\Expose()
   */
  private $subjectTypesSupported;

  /**
   * @var string[]
   *
   * @Serializer\SerializedName("id_token_signing_alg_values_supported")
   * @Serializer\Type(name="array<string>")
   * @Serializer\Expose()
   */
  private $idTokenSigningAlgValuesSupported;

  /**
   * @var string[]
   *
   * @Serializer\SerializedName("token_endpoint_auth_methods_supported")
   * @Serializer\Type(name="array<string>")
   * @Serializer\Expose()
   */
  private $tokenEndpointAuthMethodsSupported;

  /**
   * @var bool
   *
   * @Serializer\SerializedName("claims_parameter_supported")
   * @Serializer\Type(name="boolean")
   * @Serializer\Expose()
   */
  private $claimsParameterSupported;

  /**
   * @var bool
   *
   * @Serializer\SerializedName("request_parameter_supported")
   * @Serializer\Type(name="boolean")
   * @Serializer\Expose()
   */
  private $requestParameterSupported;

  /**
   * @var bool
   *
   * @Serializer\SerializedName("request_uri_parameter_supported")
   * @Serializer\Type(name="boolean")
   * @Serializer\Expose()
   */
  private $requestUriParameterSupported;

  /**
   * @var array|null
   *
   * @Serializer\SerializedName("code_challenge_methods_supported")
   * @Serializer\Type(name="array")
   * @Serializer\Expose()
   */
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
