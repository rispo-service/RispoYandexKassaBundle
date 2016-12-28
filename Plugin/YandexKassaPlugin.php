<?php

/**
 * User: al
 * Date: 18.06.16
 * Time: 21:28
 */

namespace Rispo\YandexKassaBundle\Plugin;

use JMS\Payment\CoreBundle\Model\ExtendedDataInterface;
use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;
use JMS\Payment\CoreBundle\Plugin\AbstractPlugin;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException as PluginActionRequiredException;
use JMS\Payment\CoreBundle\Plugin\Exception\BlockedException;
use JMS\Payment\CoreBundle\Plugin\Exception\FinancialException;
use JMS\Payment\CoreBundle\Plugin\Exception\PaymentPendingException;
use JMS\Payment\CoreBundle\Plugin\PluginInterface;

use Rispo\YandexKassaBundle\Api\Client;

class YandexKassaPlugin extends AbstractPlugin
{
    /** @var  Client */
    private $client;

    /**
     * YandexKassaPlugin constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $paymentSystemName
     * @return bool
     */
    function processes($paymentSystemName)
    {
        return 'yandexkassa' === $paymentSystemName;
    }

    /**
     * @param FinancialTransactionInterface $transaction
     * @param $retry
     */
    public function approveAndDeposit(FinancialTransactionInterface $transaction, $retry)
    {
        if ($transaction->getState() === FinancialTransactionInterface::STATE_NEW) {
            throw $this->createRedirectActionException($transaction);
        }

        // approve
        $transaction->setProcessedAmount($transaction->getPayment()->getTargetAmount());
        $transaction->setResponseCode(PluginInterface::RESPONSE_CODE_SUCCESS);
        $transaction->setReasonCode(PluginInterface::REASON_CODE_SUCCESS);
    }

    /**
     * @param FinancialTransactionInterface $transaction
     * @return PluginActionRequiredException
     */
    public function createRedirectActionException(FinancialTransactionInterface $transaction)
    {
        $actionRequest = new PluginActionRequiredException('Redirect to pay (YandexKassa)');
        $actionRequest->setFinancialTransaction($transaction);
        $url = $this->client->getRedirectUrl($transaction);

        dump($url);
        $actionRequest->setAction(new VisitUrl($url));

        return $actionRequest;
    }
}