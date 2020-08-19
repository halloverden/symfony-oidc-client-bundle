# oidc-client-bundle
OpenID Connect client bundle for symfony

# Install
1. `composer require halloverden/symfony-oidc-client-bundle`
2. Copy `hallo_verden_oidc_client.yaml` into your project folder and edit it to suit your needs.

### Authenticators
Authenticators can be used to allow authentication with an access token from your OpenID provider.

1. Create class that implements `HalloVerden\Security\Interfaces\OauthUserProviderServiceInterface`
2. Enable authenticators and the class you want to use as services
    ```yaml
      HalloVerden\Security\Interfaces\OauthUserProviderServiceInterface:
        class: App\Services\OauthUserProviderService # Your class
    
      HalloVerden\Security\AccessTokenAuthenticator: ~
      HalloVerden\Security\ClientCredentialsAccessTokenAuthenticator: ~
    ```
3. Add authenticators to your security config.
    ```yaml
      guard:
        authenticators:
          - HalloVerden\Security\AccessTokenAuthenticator
        entry_point: HalloVerden\Security\AccessTokenAuthenticator
    ```

### OauthAuthorizeService
You can use the OauthAuthorizeService to login users from the backend.

1. Enable the service:
    ```yaml
        HalloVerden\Oidc\ClientBundle\Interfaces\OauthAuthorizeServiceInterface:
            class: HalloVerden\Oidc\ClientBundle\Services\OauthAuthorizeService
            arguments:
                $openIdProviderService: '@hv.oidc.openid_provider.default' # Default refers to the client_configurations key in you config
                $authorizeSuccessUrl: 'http://localhost/success' # Where to redirect the user on success
                $authorizeErrorUrl: 'http://localhost/error' # Where to redirect the user on error
    
    ```
2. Create two controllers:
    ```php
    <?php
    namespace App\Controller;
    
    use HalloVerden\Oidc\ClientBundle\Interfaces\OauthAuthorizeServiceInterface;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    
    /**
     * Class AuthorizeController
     *
     * @package App\Controller
     *
     * @Route("/authorize", methods={"GET"}, name="authorize")
     */
    class AuthorizeController {
    
      /**
       * @param Request                        $request
       * @param OauthAuthorizeServiceInterface $oauthAuthorizeService
       *
       * @return RedirectResponse
       */
      public function __invoke(Request $request, OauthAuthorizeServiceInterface $oauthAuthorizeService): RedirectResponse {
        return $oauthAuthorizeService->handleAuthorize($request);
      }
    
    }
    ```
   ```php
   <?php
   namespace App\Controller;
   
   use HalloVerden\Oidc\ClientBundle\Interfaces\OauthAuthorizeServiceInterface;
   use Symfony\Component\HttpFoundation\RedirectResponse;use Symfony\Component\HttpFoundation\Request;
   use Symfony\Component\Routing\Annotation\Route;
   
   /**
    * Class HandleAuthCodeController
    *
    * @package App\Controller
    *
    * @Route("/handle", methods={"GET"}, name="authcodehandle")
    */
   class HandleAuthCodeController {
   
     /**
      * @param Request $request 
      * @param OauthAuthorizeServiceInterface $oauthAuthorizeService
      *
      * @return RedirectResponse
      */
     public function __invoke(Request $request, OauthAuthorizeServiceInterface $oauthAuthorizeService): RedirectResponse {
       return $oauthAuthorizeService->handleAuthCode($request);
     }
   
   }
   ```
 
 Make sure your redirect_uri is to the handle controller.
 
 You can now redirect you user to /authorize and you can listen to the `AuthorizedEvent` to know when a user is authorized.
   
# Examples
Get AccessToken with client credentials grant
```php
<?php
$openIdProviderService->getTokenResponse(new ClientCredentialsGrant())->getAccessToken();
```
