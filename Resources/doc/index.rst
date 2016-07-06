============
Installation
============


To install RispoYandexKassaBundle with Composer just add the following to your
`composer.json` file:

.. code-block :: js

    // composer.json
    {
        require: {
            "rispo/yandexkassa-bundle": "dev-master"
        }
    }


Then, you can install the new dependencies by running Composer's ``update``
command from the directory where your ``composer.json`` file is located:

.. code-block :: bash

    $ php composer.phar update

Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your ``AppKernel.php`` file, and
register the new bundle:

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