KitpagesSemaphoreBundle
=======================

# quick start

```php
// get manager
$semaphoreManager = $this->get("kitpages_semaphore.manager");

// wait for the semaphore disponibility
$semaphore = $semaphore->aquire($key);

// now the semaphore is locked for me
try {
    // do whatever

    // realease the semaphore
    $semaphoreManager->release($key);
} catch (Exception $e) {
    // release the semaphore
    $semaphoreManager->release($key);
    throw $e;
}
```

# Config

```yaml
kitpages_semaphore:
    sleep_time_microsecond: 100
```

