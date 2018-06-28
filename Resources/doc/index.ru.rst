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
    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        JMSPaymentCoreBundle: ~
    rispo_yandex_kassa:
        rispo_yandexkassa_shopId: number
        rispo_yandexkassa_scid: number
        rispo_yandexkassa_ShopPassword: pass
        rispo_yandexkassa_test: true/false

И обновление схемы базы данных:

    $ php app/console doctrine:schema:update --force

или

    $ php bin/console doctrine:schema:update --force

.. code-block :: yml

    // routing.yml
    rispo_yandexkassa:
        resource: "@RispoYandexKassaBundle/Resources/config/routing.yml"
        prefix:   /


=====
Использование
=====

Вот пример создания сервиса, который подписывается на собыие изменения статуса платежа и при положительной оплате производит применение платных услуг:


.. code-block :: yml

    // services.yml
    app.payment_listener:
        class: AppBundle\EventListener\PaymentListener
        arguments: [@service_container]
        tags:
            - { name: kernel.event_listener, event: payment.state_change, method: onPaymentStateChange }

.. code-block :: php

    <?php

    namespace AppBundle\EventListener;

    use AppBundle\Entity\PaidService;
    use AppBundle\Entity\PaidServiceTransaction;
    use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
    use JMS\Payment\CoreBundle\Model\PaymentInterface;
    use JMS\Payment\CoreBundle\PluginController\Event\PaymentStateChangeEvent;
    use JMS\DiExtraBundle\Annotation as DI;
    use Symfony\Component\DependencyInjection\ContainerInterface;

    /**
     * Payment listener.
     *
     * @DI\Service("app.payment_listener", public=true)
     * @DI\Tag("kernel.event_listener", attributes = {"event" = "payment.state_change", "method" = "onPaymentStateChange"})
     */
    class PaymentListener
    {
        /** @var  ContainerInterface */
        private $container;

        /**
         * @DI\InjectParams({
         *     "container" = @DI\Inject("service_container"),
         * })
         */
        public function __construct($container)
        {
            $this->container = $container;
        }

        public function onPaymentStateChange(PaymentStateChangeEvent $event)
        {
            $payment = $event->getPayment();
            $instruction = $event->getPaymentInstruction();

            if ($event->getNewState() == PaymentInterface::STATE_DEPOSITED) {
                /** @var $em \Doctrine\ORM\EntityManager */
                $em = $this->container->get("doctrine")->getManager();

                $service = $em->getRepository("AppBundle:PaidServiceTransaction")->findOneBy([
                    "paymentInstruction" => $instruction,
                ]);

                if ($service instanceof PaidServiceTransaction) {
                    if (!$service->getPaymentSent()) {
                        $service->setPaymentSent(true);
                        $service->setPaymentSentAt(new \DateTime());

                        $em->persist($service);
                        $em->flush($service);

                        // Тут производим применение платных услуг
                        $this->container->get("app.paid_services_manager")->apply($service);
                    }
                }
            }
        }
    }

Информация об использовании доступна по адресу `пример <https://github.com/schmittjoh/JMSPaymentCoreBundle/blob/master/Resources/doc/usage.rst>`_
