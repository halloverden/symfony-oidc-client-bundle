<?php


namespace HalloVerden\Oidc\ClientBundle\Services;

use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderRegistryServiceInterface;
use HalloVerden\Oidc\ClientBundle\Interfaces\OpenIdProviderServiceInterface;

/**
 * Class OpenIdProviderRegistryService
 *
 * @package HalloVerden\Oidc\ClientBundle\Services
 */
class OpenIdProviderRegistryService implements OpenIdProviderRegistryServiceInterface {

  /**
   * @var OpenIdProviderServiceInterface[]
   */
  private $openIdProviderServices;

  /**
   * ClientConfigurationRegistryService constructor.
   *
   * @param iterable<OpenIdProviderServiceInterface> $openIdProviderServices
   */
  public function __construct(iterable $openIdProviderServices) {
    $this->openIdProviderServices = $openIdProviderServices instanceof \Traversable ? iterator_to_array($openIdProviderServices) : (array) $openIdProviderServices;
  }

  /**
   * @inheritDoc
   */
  public function getOpenIdProviderServices(): array {
    return $this->openIdProviderServices;
  }

  /**
   * @inheritDoc
   */
  public function getOpenIdProviderServiceByIssuer(string $issuer): ?OpenIdProviderServiceInterface {
    foreach ($this->openIdProviderServices as $openIdProviderService) {
      if ($openIdProviderService->getClientConfiguration()->getIssuer() === $issuer) {
        return $openIdProviderService;
      }
    }

    return null;
  }

}
