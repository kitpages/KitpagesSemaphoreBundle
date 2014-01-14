KitpagesSemaphoreBundle
=======================

[![Build Status](https://travis-ci.org/kitpages/KitpagesSemaphoreBundle.png?branch=master)](https://travis-ci.org/kitpages/KitpagesSemaphoreBundle)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e6c4a363-630d-4036-8c73-b93407f08043/small.png)](https://insight.sensiolabs.com/projects/e6c4a363-630d-4036-8c73-b93407f08043)

This bundle allows a synchronization between several parallel php process accessing a single resouce

## quick start

```php
// get manager
$semaphoreManager = $this->get("kitpages_semaphore.manager");

// wait for the semaphore disponibility
$semaphoreManager->aquire("my_semaphore_name");

// do someting interesting with the protected resource

// release the semaphore
$semaphoreManager->release("my_semaphore_name");
```

## Features

* shared semaphore between several parallel php processes (saved in DB for the moment)
* deadlock detection : consider a semaphore as dead after a configurable duration
* logging system for deadlock on a specific channel in monolog (channel : kitpages_semaphore)

## Installation
------------

Using [Composer](http://getcomposer.org/), just `$ composer require kitpages/semaphore-bundle` package or:

```javascript
{
  "require": {
    "kitpages/semaphore-bundle": "~1.2"
  }
}
```

Then add the bundle in AppKernel :

```php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            // use of doctrine (dbal only) and monolog
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            // the bundle itself
            new Kitpages\SemaphoreBundle\KitpagesSemaphoreBundle(),
        );
    }
```

The add configuration in your config.yml.

default values are 0.1s for pooling sleep time and 5s for deadlock duration

```yaml
kitpages_semaphore:
    sleep_time_microseconds: 100000
    dead_lock_microseconds: 5000000
```

create the needed db table

```bash
php app/console doctrine:schema:update --force
```

## Status

* stable, tested and under travis-ci

## Versions

2014-01-14 : v1.2.0 : logger for dead lock

* no BC break
* new : use monolog on the channel "kitpages_semaphore" to send a warning for every deadlock
* fix : dependencies in composer.json
* enh : more unit testing for configuration parser and service initialisation in the DIC
* enh : Readme rewriting

2013-12-11 : v1.1.0 : atomicity in aquire-release process

* no BC Break
* new : add a db transaction around the aquire-release process to garanty atomicity

2013-12-05 : v1.0.0 : first release

## Coming features

* implement a new manager with a faster storage (no BC break)


