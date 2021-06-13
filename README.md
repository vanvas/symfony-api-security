## Installation

```shell
composer require vim/symfony-api-security
```

## Configuration

`config/packages/api_security.yaml`

```yaml
api_security:
  jwt_login_route_name: jwt_login
  jwt_config:
    secret_key: '%env(resolve:JWT_SECRET)%'
    leeway: 60
    exp: '+120 minutes' # datetime format
```

`config/routes.yaml`
```yaml
jwt_login:
  path: /api/v1/auth/login
  methods: POST
  controller: Vim\ApiSecurity\Controller\AuthController::login
```

`api/config/bundles.php`

```PHP
<?php
return [
  // ...
  Vim\ApiSecurity\ApiSecurityBundle::class => ['all' => true],
];
```

## Example

`config/packages/security.yaml`

```yaml
security:
  encoders:
    App\Entity\User:
      algorithm: auto
  providers:
    jwt:
      id: Vim\ApiSecurity\AuthProvider\JwtUserProvider
  firewalls:
    login:
      anonymous: true
      pattern: /api/v1/auth/login
      provider: db
      stateless: true
      guard:
        authenticators:
          - Vim\ApiSecurity\Authenticator\JwtLoginAuthenticator
    api:
      anonymous: true
      pattern: ^/
      provider: api
      stateless: true
      guard:
        authenticators:
          - Vim\ApiSecurity\Authenticator\JwtTokenAuthenticator
    dev:
      pattern: ^/(_(profiler|wdt)|css|images|js)/
      security: false
  access_control:
    - { path: ^/api/v1/auth/current, roles: [ ROLE_CLIENT, ROLE_ADMIN ], methods: [ GET ] }
    - { path: ^/api/v1, roles: ROLE_ADMIN }
```
