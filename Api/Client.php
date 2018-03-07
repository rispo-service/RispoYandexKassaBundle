<?php
/**
 * Created by PhpStorm.
 * User: al
 * Date: 18.06.16
 * Time: 22:35
 */

namespace Rispo\YandexKassaBundle\Api;

use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use JMS\Payment\CoreBundle\Plugin\PluginInterface;

/**
 * Class Client
 * @package Rispo\YandexKassaBundle\Api
 */
class Client
{
    /** @var Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $token_storage;

    /** @var string */
    private $shopId;

    /** @var string */
    private $scid;

    /** @var string */
    private $shopPassword;

    /** @var bool */
    private $test;

    /**
     * Client constructor.
     * @param TokenStorageInterface $token_storage
     * @param $shopId
     * @param $scid
     * @param $shopPassword
     * @param $test
     */
    public function __construct(TokenStorageInterface $token_storage, $shopId, $scid, $shopPassword, $test)
    {
        $this->token_storage = $token_storage;
        $this->shopId = $shopId;
        $this->scid = $scid;
        $this->shopPassword = $shopPassword;
        $this->test = $test;
    }

    /**
     * @return string
     */
    private function getWebServerUrl()
    {
        return $this->test ? 'https://demomoney.yandex.ru/eshop.xml' : 'https://money.yandex.ru/eshop.xml';
    }


    /**
     * @param FinancialTransactionInterface $transaction
     * @return string
     */
    public function getRedirectUrl(FinancialTransactionInterface $transaction)
    {
        /** @var PaymentInstructionInterface $instruction */
        $instruction = $transaction->getPayment()->getPaymentInstruction();
        $inv_id = $instruction->getId();
        /** @var ExtendedDataInterface $data */
        $data = $transaction->getExtendedData();
        $data->set('inv_id', $inv_id);

        $parameters = [
            'shopId' => $this->shopId,
            'scid' => $this->scid,
            'Sum' => $transaction->getRequestedAmount(),
            'cms_name' => 'symfony-github',
            'orderNumber' => $instruction->getId()
        ];

        return $this->getWebServerUrl() . '?' . http_build_query($parameters);
    }

}