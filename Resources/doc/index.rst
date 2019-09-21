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
        new Rispo\YandexKassaBundle\RispoYandexKassaBundle(),
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
Usage
=====
Usage `example <https://github.com/schmittjoh/JMSPaymentCoreBundle/blob/master/Resources/doc/usage.rst>`_

Example:

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

                        // Apply Services
                        $this->container->get("app.paid_services_manager")->apply($service);
                    }
                }
            }
        }
    }