<?php


namespace HalloVerden\Oidc\ClientBundle\Entity\Grant;


use HalloVerden\Oidc\ClientBundle\Interfaces\Grant\OidcGrantInterface;

class ClientCredentialsGrant implements OidcGrantInterface {
  const TYPE_NAME = 'client_credentials';

  /**
   * @var array|null
   */
  private $scopes;

  /**
   * ClientCredentialsGrant constructor.
   *
   * @param array|null $scopes
   */
  public function __construct(?array $scopes = null) {
    $this->scopes = $scopes;
  }

  /**
   * @inheritDoc
   */
  public function getRequestData(): array {
    $data = [];

    if (null !== $scope = $this->getScope()) {
      $data['scope'] = $scope;
    }

    return $data;
  }

  /**
   * @inheritDoc
   */
  public function getTypeName(): string {
    return self::TYPE_NAME;
  }

  /**
   * @return string|null
   */
  private function getScope(): ?string {
    if (null === $this->scopes) {
      return null;
    }

    return implode(' ', $this->scopes);
  }

}
