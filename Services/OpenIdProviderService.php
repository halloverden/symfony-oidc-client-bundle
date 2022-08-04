<?php


namespace HalloVerden\Oidc\ClientBundle\Services;


use HalloVerden\Contracts\Oidc\OidcEndSessionResponseInterface;
use HalloVerden\Contracts\Oidc\Requests\OidcAuthenticationRequestInterface;
use HalloVerden\Oidc\ClientBundle\Client\ClientConfiguration;
use HalloVerden\Oidc\ClientBundle\Helpers\UriHelper;
use HalloVerden\Oidc\ClientBundle\Entity\Requests\Client\OidcAuthenticationRequest;
use HalloVerden\Oidc\ClientBundle\Entity\Responses\OidcEndSessionResponse;
use HalloVerden\Oidc\ClientBundle\Exception\ProviderException;
use HalloVerden\Oidc\ClientBundle\Factory\OidcTokenResponseFactory;
use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OidcRawTokenInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OidcTokenResponseInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderServiceInterface;
use HalloVerden\Oidc\ClientBundle\Provider\ProviderConfiguration;
use JMS\Serializer\SerializerInterface;
use Jose\Component\Core\JWKSet;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenIdProviderService implements OpenIdProviderServiceInterface {
  private ClientConfiguration $clientConfiguration;
  private HttpClientInterface $client;
  private SerializerInterface $serializer;
  private ?ProviderConfiguration $providerConfiguration;
  private ?JWKSet $publicKey = null;
  protected OidcTokenResponseFactory $oidcTokenResponseFactory;

  /**
   * OpenIdProviderService constructor.
   *
   * @param ClientConfiguration $clientConfiguration
   * @param HttpClientInterface $client
   * @param SerializerInterface $serializer
   * @param array               $jwsLoaders
   * @param array               $claimCheckers
   */
  public function __construct(ClientConfiguration $clientConfiguration, HttpClientInterface $client, SerializerInterface $serializer, array $jwsLoaders, array $claimCheckers) {
    $this->clientConfiguration = $clientConfiguration;
    $this->client = $client;
    $this->serializer = $serializer;
    $this->oidcTokenResponseFactory = new OidcTokenResponseFactory($this, $jwsLoaders, $claimCheckers);
  }

  /**
   * @inheritDoc
   */
  public function getProviderConfiguration(): ProviderConfiguration {
    if (null !== $this->providerConfiguration) {
      return $this->providerConfiguration;
    }

    try {
      $response = $this->client->request(Request::METHOD_GET, $this->clientConfiguration->getOpenIdConfigurationEndpoint());
      return $this->providerConfiguration = $this->serializer->deserialize($response->getContent(), ProviderConfiguration::class, 'json');
    } catch (ExceptionInterface $e) {
      throw ProviderException::unableToFetchConfiguration($e);
    }
  }

  /**
   * @inheritDoc
   */
  public function getPublicKey(): JWKSet {
    if (null !== $this->publicKey) {
      return $this->publicKey;
    }

    try {
      $response = $this->client->request(Request::METHOD_GET, $this->getProviderConfiguration()->getJwksUri());
      return $this->publicKey = JWKSet::createFromJson($response->getContent());
    } catch (ExceptionInterface $e) {
      throw ProviderException::unableToFetchPublicKey($e);
    }
  }

  /**
   * @inheritDoc
   */
  public function getClientConfiguration(): ClientConfiguration {
    return $this->clientConfiguration;
  }

  /**
   * @inheritDoc
   */
  public function getAuthorizeRequest(): OidcAuthenticationRequestInterface {
    return OidcAuthenticationRequest::createFromConfigs($this->getClientConfiguration(), $this->getProviderConfiguration());
  }

  /**
   * @inheritDoc
   */
  public function getTokenResponse(OidcGrantInterface $grant): OidcTokenResponseInterface {
    $data = array_merge([
      'grant_type' => $grant->getTypeName(),
      'client_id' => $this->getClientConfiguration()->getClientId(),
      'client_secret' => $this->getClientConfiguration()->getClientSecret(),
      'redirect_uri' => $this->getClientConfiguration()->getRedirectUri(),
      'scope' => $this->getClientConfiguration()->getScope(),
    ], $grant->getRequestData());

    try {
      $response = $this->client->request(Request::METHOD_POST, $this->getProviderConfiguration()->getTokenEndpoint(), [
        'body' => $data
      ]);

      return $this->oidcTokenResponseFactory->createOidcTokenResponse($response->toArray(), $grant);
    } catch (ExceptionInterface $e) {
      throw ProviderException::unableToFetchToken($e);
    }
  }

  /**
   * @inheritDoc
   */
  public function revokeToken(OidcRawTokenInterface $token): void {
    try {
      $response = $this->client->request(Request::METHOD_POST, $this->getProviderConfiguration()->getRevocationEndpoint(), [
        'body' => [
          'token' => $token->getRawToken(),
          'client_id' => $this->getClientConfiguration()->getClientId(),
          'client_secret' => $this->getClientConfiguration()->getClientSecret(),
        ]
      ]);

      if ($response->getStatusCode() !== Response::HTTP_OK) {
        throw ProviderException::unableToRevokeToken(new ClientException($response));
      }
    } catch (TransportExceptionInterface $e) {
      throw ProviderException::unableToRevokeToken($e);
    }
  }

  /**
   * @inheritDoc
   */
  public function getEndSessionResponse(OidcRawTokenInterface $idTokenHint, string $postLogoutRedirectUri, string $state): OidcEndSessionResponseInterface {
    $uri = (new UriHelper($this->getProviderConfiguration()->getEndSessionEndpoint()))
      ->addQueryParameter('id_token_hint', $idTokenHint->getRawToken())
      ->addQueryParameter('post_logout_redirect_uri', $postLogoutRedirectUri)
      ->addQueryParameter('state', $state);

    return new OidcEndSessionResponse($uri->toString());
  }

  /**
   * @return OidcTokenResponseFactory
   */
  public function getOidcTokenResponseFactory(): OidcTokenResponseFactory {
    return $this->oidcTokenResponseFactory;
  }

}
