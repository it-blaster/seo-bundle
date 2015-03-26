SeoBundle
====================

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/it-blaster/seo-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/it-blaster/seo-bundle/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/it-blaster/seo-bundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/it-blaster/seo-bundle/build-status/master)

Symfony2 seo bundle

Installation
------------

Добавьте <b>ItBlasterSeoBundle</b> в `composer.json`:

```js
{
    "require": {
        "it-blaster/seo-bundle": "dev-master"
	},
}
```

Теперь запустите композер, чтобы скачать бандл командой:

``` bash
$ php composer.phar update it-blaster/seo-bundle
```

Композер установит бандл в папку проекта `vendor/it-blaster/seo-bundle`.

Далее подключите бандл в ядре `AppKernel.php`:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new ItBlaster\SeoBundle\ItBlasterSeoBundle(),
    );
}
```