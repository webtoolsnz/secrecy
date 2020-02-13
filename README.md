Secrecy
================================
[![CI Action](https://github.com/webtoolsnz/secrecy/workflows/continuous-integration/badge.svg)](https://github.com/webtoolsnz/secrecy/workflows/continuous-integration)
[![codecov](https://codecov.io/gh/webtoolsnz/secrecy/branch/master/graph/badge.svg)](https://codecov.io/gh/webtoolsnz/secrecy)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webtoolsnz/secrecy/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webtoolsnz/secrecy/?branch=master)


Secrecy is a secret management abstraction that allows you to easily swap out different adapters based on configuration. 

 Goals
 ------
 - [x] Promote explicit separation of configuration data and sensitive credentials
 - [ ] Provide support for popular secret management services
 - [ ] Provide seamless integration with popular frameworks
 - [x] Make it easy to manage your application secrets

 
Installation
--------------

```bash
 composer require secrecy/secrecy
```

Usage
------
Below is an example of using the JsonFileAdapter

create a `secrets.json` file in the root of your project
```json
{
    "secrets" : {
        "DB_USER": "root",
        "DB_PASSWORD": "f4x3!33s@",
        "API_KEY": "SOME_SUPER_SECRET_KEY"
    }
}
```

```php
require 'vendor/autoload.php';

use Secrecy\SecretManager;
use Secrecy\Adapter\JsonFileAdapter;

$secretManager = new SecretManager(
    new JsonFileAdapter(__DIR__.'/secrets.json')
);

// Get a list of all secrets
print_r($secretManager->list());

// Retrieve a single by name
print_r($secretManager->get('API_KEY'));

// Update an existing secret
print_r($secretManager->update('DB_USER', 'app_user'));

// Create a new secret
print_r($secretManager->create('APP_SECRET', 'SHH'));
```

Caching
-------
The `SecretManager` and its adapters does not cache any data, this must be done at the framework integration layer.
