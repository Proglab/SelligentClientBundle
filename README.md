SelligentClientBundle
================

A Selligent basic client

Installation
------------

You must add this to your .env file :

```
###> proglab/selligent-client-bundle ###
APISELLIGENTCLIENT_INDIVIDUAL_URL=xxx
APISELLIGENTCLIENT_BROADCAST_URL=xxx
APISELLIGENTCLIENT_LOGIN=xxx
APISELLIGENTCLIENT_PASSWORD=xxx
###< proglab/selligent-client-bundle ###
```

Open a command console, enter your project directory and execute:

```console
composer require proglab/selligent-client-bundle
```

If you're not using symfony/flex, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Proglab\SelligentClientBundle\SelligentClientBundle::class => ['all' => true],
];
```
Usage
-----

### Generals

#### Getting a SelligentClient

You have two choices:

1. Create the client manually, and pass it a logger:

```php
use Proglab\SelligentClientBundle\Service\SelligentClient;
use Psr\Log\NullLogger;

$logger = new NullLogger();
$client = new SelligentClient($logger);
```

2. Get the client from Dependency Injection:

```php
use Proglab\SelligentClientBundle\Service\SelligentClient;

class Service
{
    public function __construct(private SelligentClient $client)
    {
    }
}
```

#### Connection

You must connect to Selligent SOAP API.

You need the individual_url, broadcast_url, login and password.


```php
$client->connect('individual_url', 'broadcast_url', 'login', 'password');
```


### Get a record

Get a record with a filter :

```php
$filters = ['ID' => 18];
$listId = 54;
$client->getOneByFilter($filters, $listId);
```
### Create a row

Create a record in a list :

```php
$data = [
    'MAIL' => 'xx@xxxxx.xxx',
    'NAME' => 'xxxxx xxxx',
    'LANGUAGE' => 'fr'
];
$listId = 54;
$client->createRow($data, $listId);
```


### Update a row

Update a record in a list :

```php
$data = ['FIRSTNAME' => 'Fabrice'];
$listId = 54;
$client->updateRow($data, 54, $listId);
```