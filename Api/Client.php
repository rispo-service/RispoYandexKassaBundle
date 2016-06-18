<?php
/**
 * Created by PhpStorm.
 * User: al
 * Date: 18.06.16
 * Time: 22:35
 */

namespace Rispo\YandexKassaBundle\Api;


class Client
{
    /** @var string */
    private $login;

    /** @var bool */
    private $test;

    public function __construct($login, $test)
    {
        $this->login = $login;
        $this->test = $test;
    }

    /**
     * @return string
     */
    private function getWebServerUrl()
    {
        return $this->test ? 'http://test.robokassa.ru/Index.aspx' : 'https://auth.robokassa.ru/Merchant/Index.aspx';
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

        /*
        $description = 'test desc';
        if($data->has('description')) {
            $description = $data->get('description');
        }

        $parameters = [
            'MrchLogin' => $this->login,
            'OutSum' => $transaction->getRequestedAmount(),
            'InvId' => $inv_id,
            'Desc' => $description,
            'IncCurrLabel' => '',
            'SignatureValue' => $this->auth->sign($this->login, $transaction->getRequestedAmount(), $inv_id)
        ];
        */

        $parameters = [];

        return $this->getWebServerUrl() .'?' . http_build_query($parameters);
    }

}