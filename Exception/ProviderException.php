<?php


namespace HalloVerden\Oidc\ClientBundle\Exception;

class ProviderException extends \Exception {
  const ERROR_UNABLE_TO_FETCH_CONFIGURATION = 'UNABLE_TO_FETCH_CONFIGURATION';
  const ERROR_UNABLE_TO_FETCH_PUBLIC_KEY = 'UNABLE_TO_FETCH_PUBLIC_KEY';
  const ERROR_UNABLE_TO_FETCH_TOKEN = 'UNABLE_TO_FETCH_TOKEN';
  const ERROR_UNABLE_TO_REVOKE_TOKEN = 'UNABLE_TO_REVOKE_TOKEN';

  /**
   * @inheritDoc
   */
  public function __construct(string $error, \Throwable $previous = null) {
    parent::__construct($error, 0, $previous);
  }

  /**
   * @param \Throwable|null $previous
   *
   * @return static
   */
  public static function unableToFetchConfiguration(\Throwable $previous = null): self {
    return new self(self::ERROR_UNABLE_TO_FETCH_CONFIGURATION, $previous);
  }

  /**
   * @param \Throwable|null $previous
   *
   * @return static
   */
  public static function unableToFetchPublicKey(\Throwable $previous = null): self {
    return new self(self::ERROR_UNABLE_TO_FETCH_PUBLIC_KEY, $previous);
  }

  /**
   * @param \Throwable|null $previous
   *
   * @return static
   */
  public static function unableToFetchToken(\Throwable $previous = null): self {
    return new self(self::ERROR_UNABLE_TO_FETCH_TOKEN, $previous);
  }

  /**
   * @param \Throwable|null $previous
   *
   * @return static
   */
  public static function unableToRevokeToken(\Throwable $previous = null): self {
    return new self(self::ERROR_UNABLE_TO_REVOKE_TOKEN, $previous);
  }

}
