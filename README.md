RambleUuidBundle
================

Provides a binary guid type for storing guids efficiently.

### Step 1: Download RambleUuidBundle using composer

Add RambleUuidBundle in your composer.json:

```js
{
    "require": {
        "ramble/uuid-bundle": "*"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ramble/RambleUuidBundle"
        }
    ]
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update ramble/uuid-bundle
```

Composer will install the bundle to your project's `vendor/ramble` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Ramble\UuidBundle\RambleUuidBundle(),
    );
}
```

### Step 3: Use the type in your entity

N.b. Doctrine doesn't seem to respect the length of the column, and doesn't generate a foreign key if you don't hardcode the columnDefinition.

``` php
/**
 * @ORM\Entity
 * @ORM\Table(name="my_entity")
 */
class MyEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="binary_guid", columnDefinition="BINARY(16) NOT NULL", name="id")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;
}
```

### Step 4: Build your entities

``` bash
php ./app/console doctrine:schema:update --force
```

### Todo

- Support multiple database types