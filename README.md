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

## Status

* stable, tested and under travis-ci

## Coming features

* implement a new manager with a faster storage (no BC break)

## Config

default values are 0.1s for pooling sleep time and 5s for deadlock duration

```yaml
kitpages_semaphore:
    sleep_time_microseconds: 100000
    dead_lock_microseconds: 5000000
```
