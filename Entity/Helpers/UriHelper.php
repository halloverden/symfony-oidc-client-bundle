<?php


namespace HalloVerden\Oidc\ClientBundle\Entity\Helpers;


use Nyholm\Psr7\Uri;

class UriHelper {

  /**
   * @var Uri
   */
  private $uri;

  /**
   * URIHelper constructor.
   *
   * @param string $uriSting
   */
  public function __construct(string $uriSting) {
    $this->uri = new Uri($uriSting);
  }

  /**
   * @param string $key
   * @param string $value
   *
   * @return URIHelper
   */
  public function addQueryParameter(string $key, string $value): self {
    $this->uri = $this->uri->withQuery($this->addToQuery($this->uri->getQuery(), $key, $value));
    return $this;
  }

  /**
   * @param array $parameters
   *
   * @return URIHelper
   */
  public function addQueryParameters(array $parameters): self {
    foreach ($parameters as $key => $value) {
      if ($value !== null) {
        $this->addQueryParameter($key, $value);
      }
    }

    return $this;
  }

  /**
   * @param string $key
   * @param string $value
   *
   * @return URIHelper
   */
  public function addFragmentParameter(string $key, string $value): self {
    $this->uri = $this->uri->withFragment($this->addToQuery($this->uri->getFragment(), $key, $value));
    return $this;
  }

  /**
   * @param array<string, string> $parameters
   *
   * @return URIHelper
   */
  public function addFragmentParameters(array $parameters): self {
    foreach ($parameters as $key => $value) {
      if ($value !== null) {
        $this->addFragmentParameter($key, $value);
      }
    }

    return $this;
  }

  /**
   * @param string $query
   * @param string $key
   * @param string $value
   *
   * @return string
   */
  private function addToQuery(string $query, string $key, string $value): string {
    \parse_str($query, $queryArray);

    $queryArray[\rawurlencode($key)] = \rawurlencode($value);

    return http_build_query($queryArray);
  }


  /**
   * @return string
   */
  public function toString(): string {
    return $this->__toString();
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->uri->__toString();
  }
}
