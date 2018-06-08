============
Установка
============

Используте комманду:

.. code-block :: bash

    $ php composer.phar require rispo/yandexkassa-bundle dev-master

Готово! Composer автоматически скачает все необходимые файлы и установит их в ваш проект.
После этого нужно подключить загрузку бандла в файл ``AppKernel.php``:

.. code-block :: php

    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new Rispo\YandexKassaBundle\RispoYandexKassaBudnle(),
        // ...
    );

Зависимости
------------

Плагин зависит от `JMSPaymentCoreBundle <https://github.com/schmittjoh/JMSPaymentCoreBundle/>`_,
и его также необходимо подключить в функцию регистрации в файл ``AppKernel.php``:

.. code-block :: php

    <?php

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new JMS\Payment\CoreBundle\JMSPaymentCoreBundle(),
        // ...
    );


Настройки
------------

.. code-block :: yml

    // config.yml
    rispo_yandex_kassa:
        rispo_yandexkassa_shopId: %rispo_yandexkassa_shopid%
        rispo_yandexkassa_scid: %rispo_yandexkassa_scid%
        rispo_yandexkassa_ShopPassword: %rispo_yandexkassa_shoppassword%
        rispo_yandexkassa_test: %rispo_yandexkassa_test%  # true/false

.. code-block :: yml

    // parameters.yml

    rispo_yandexkassa_shopid: 00000
    rispo_yandexkassa_scid: 00000
    rispo_yandexkassa_shoppassword: realpass
    rispo_yandexkassa_test: true # or false


.. code-block :: yml

    // routing.yml
    karser_robokassa:
        resource: "@RispoYandexKassaBundle/Resources/config/routing.yml"
        prefix:   /


=====
Использование
=====

Информация об использовании доступна по адресу `пример <https://github.com/schmittjoh/JMSPaymentCoreBundle/blob/master/Resources/doc/usage.rst>`_
