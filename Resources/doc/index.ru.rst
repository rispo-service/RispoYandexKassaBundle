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
        new Rispo\YandexKassaBundle\RispoYandexKassa(),
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
Использование
=====

Информация об использовании доступна по адресу `пример <https://github.com/schmittjoh/JMSPaymentCoreBundle/blob/master/Resources/doc/usage.rst>`_