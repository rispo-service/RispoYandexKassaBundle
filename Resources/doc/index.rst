============
Installation
============

Use git to clone repository from Github.

.. code-block :: php

    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Rispo\YandexKassaBundle\RispoYandexKassa(),
        // ...
    );


Dependencies
------------
This plugin depends on the `JMSPaymentCoreBundle <https://github.com/schmittjoh/JMSPaymentCoreBundle/>`_, so you'll need to add this to your kernel
as well even if you don't want to use its persistence capabilities.

.. code-block :: php

    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new JMS\Payment\CoreBundle\JMSPaymentCoreBundle(),
        // ...
    );

Configuration
-------------

.. code-block :: yml

    // config.yml
    rispo_yandex_kassa:
        rispo_yandexkassa_shopId: number
        rispo_yandexkassa_scid: number
        rispo_yandexkassa_ShopPassword: pass
        rispo_yandexkassa_test: true/false


.. code-block :: yml

    // routing.yml
    karser_robokassa:
        resource: "@RispoYandexKassaBundle/Resources/config/routing.yml"
        prefix:   /

=====
Usage
=====
Usage `example <https://github.com/schmittjoh/JMSPaymentCoreBundle/blob/master/Resources/doc/usage.rst>`_