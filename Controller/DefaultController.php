<?php

namespace Rispo\YandexKassaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;


class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * @Template("RispoYandexKassaBundle:Default:checkOrder.xml.twig")
     */
    public function checkOrderAction(Request $request)
    {
        $hipChat = $this->container->get('hipchat');
        $hipChat->message_room('hhonline', 'API', 'checkOrderAction');
        $hipChat->message_room('hhonline', 'API',
            sizeof($request->request->all()) ? implode(' --- ', $request->request->all()) : 'no params' );

        $hash = md5(
            $request->get('action') . ';' . $request->get('orderSumAmount')
            . ';' . $request->get('orderSumCurrencyPaycash') . ';' . $request->get('orderSumBankPaycash')
            . ';' . $this->container->getParameter('rispo_yandexkassa_shopId')
            . ';' . $request->get('invoiceId') . ';' . $request->get('customerNumber')
            . ';' . $this->container->getParameter('rispo_yandexkassa_ShopPassword'));

        $hipChat->message_room('hhonline', '-----', '-----');

        $hipChat->message_room('hhonline', 'hash', strtolower($hash) );
        $hipChat->message_room('hhonline', 'md5', strtolower($request->get('md5')));

        $hipChat->message_room('hhonline', '-----', '-----');

        $hipChat->message_room('hhonline', '1: action', $request->get('action'));
        $hipChat->message_room('hhonline', '2: orderSumAm..', $request->get('orderSumAmount'));
        $hipChat->message_room('hhonline', '3: orderSumCu..', $request->get('orderSumCurrencyPaycash'));
        $hipChat->message_room('hhonline', '4: orderSumBa..', $request->get('orderSumBankPaycash'));
        $hipChat->message_room('hhonline', '5: shopId', $this->container->getParameter('rispo_yandexkassa_shopId'));
        $hipChat->message_room('hhonline', '6: invoiceId', $request->get('invoiceId'));
        $hipChat->message_room('hhonline', '7: customerNu..', $request->get('customerNumber'));
        $hipChat->message_room('hhonline', '8: ShopPasswo..', $this->container->getParameter('rispo_yandexkassa_ShopPassword'));

        $hipChat->message_room('hhonline', '-----', '-----');
        $hipChat->message_room('hhonline', 'orderNumber', $request->get('orderNumber'));

        $code = 1;
        if ( strtolower($hash) == strtolower( $request->get('md5') ) ){
            $code = 0;
            // TODO set to APPROVED
        } else {
            // TODO set to CANCELED
        }
        $code = 0;

        $hipChat->message_room('hhonline', 'code', 'code: ' . $code);
        $hipChat->message_room('hhonline', '-----', '-----');

        return [
            'requestDatetime' => $request->get('requestDatetime'),
            'code' => $code,
            'invoiceId' => $request->get('invoiceId'),
            'shopId' => $request->get('shopId')
        ];
    }

    /**
     * @param Request $request
     * @return array
     * @Template("RispoYandexKassaBundle:Default:paymentAviso.xml.twig")
     */
    public function paymentAvisoAction(Request $request)
    {
        // TODO set to DEPOSITING

        $hipChat = $this->container->get('hipchat');
        $hipChat->message_room('hhonline', 'API', 'paymentAvisoAction');

        $hipChat->message_room('hhonline', 'API',
            sizeof($request->request->all()) ? implode('; ', $request->request->all()) : 'no params' );

        $hash = md5($request->get('action')
            . ';' . $request->get('orderSumAmount')
            . ';' . $request->get('orderSumCurrencyPaycash')
            . ';' . $request->get('orderSumBankPaycash')
            . ';' . $this->container->getParameter('rispo_yandexkassa_shopId')
            . ';' . $request->get('invoiceId')
            . ';' . $request->get('customerNumber')
            . ';' . $this->container->getParameter('rispo_yandexkassa_ShopPassword'));

        $code = 1;
        if ( strtolower($hash) == strtolower($request->get('md5') ) ) {
            // TODO set to DEPOSITED
            $code = 0;
        } else {
            // TODO set to FAILED
        }

        $code = 0;

        if ($code == 0) {
            $instruction = $this->getInstruction( $request->get('orderNumber') );

            /** @var FinancialTransactionInterface $transaction */
            if (null === $transaction = $instruction->getPendingTransaction()) {
                $hipChat->message_room('hhonline', 'FAIL', 'FAIL (null === $transaction = $instruction->getPendingTransaction())');
                return new Response('FAIL (null === $transaction = $instruction->getPendingTransaction())', 500);
            }

            try {

                $this->get('payment.plugin_controller')->approveAndDeposit(
                        $transaction->getPayment()->getId(),
                        $request->get('orderSumAmount')
                );

                // TODO set to DEPOSITED

            } catch (\Exception $e) {
                // TODO set to FAILED
                $hipChat->message_room('hhonline', 'FAIL', 'approveAndDeposit');
                return new Response('FAIL (approveAndDeposit)', 500);
            }

            $this->getDoctrine()->getManager()->flush();
        }

        return [
            'requestDatetime' => $request->get('requestDatetime'),
            'code' => $code,
            'invoiceId' => $request->get('invoiceId'),
            'shopId' => $request->get('shopId')
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function successAction(Request $request)
    {
        $out_sum = $request->get('OutSum');
        $inv_id = $request->get('InvId');
        $instruction = $this->getInstruction($inv_id);
        $data = $instruction->getExtendedData();
        return $this->redirect($data->get('return_url'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function failAction(Request $request)
    {
        $inv_id = $request->get('InvId');
        $instruction = $this->getInstruction($inv_id);
        $data = $instruction->getExtendedData();
        return $this->redirect($data->get('cancel_url'));
    }

    private function getInstruction($id)
    {
        $instruction = $this->getDoctrine()->getManager()->getRepository('JMSPaymentCoreBundle:PaymentInstruction')->find($id);
        if (empty($instruction)) {
            throw new \Exception('Cannot find instruction id='.$id);
        }
        return $instruction;
    }
}