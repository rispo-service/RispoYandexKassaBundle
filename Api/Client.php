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
use FOS\UserBundle\Model\User;

/**
 * Class Client
 * @package Rispo\YandexKassaBundle\Api
 */
class Client
{
    /** @var Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    /** @var string */
    private $shopId;

    /** @var string */
    private $scid;

    /** @var string */
    private $shopPassword;

    /** @var bool */
    private $test;

    public function __construct(TokenStorageInterface $tokenStorage, $shopId, $scid, $shopPassword, $test)
    {
        $this->tokenStorage = $tokenStorage;
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

        $data = [
            'shopId' => $this->shopId,
            'scid' => $this->scid,
            'Sum' => $transaction->getRequestedAmount(),
            'cms_name' => 'symfony2-github',
            'orderNumber' => $instruction->getId()
        ];

        $user = $this->tokenStorage->getToken()->getUser();
        if ($user instanceof User) {
            $data['customerNumber'] = $user->getId();
        } else {
            $data['customerNumber'] = 0;
        }

        $url = $this->getWebServerUrl();

        // Initialize Guzzle client
        $client = new \GuzzleHttp\Client();

        // Create a POST request
        $response = $client->request(
            'POST',
            $url,
            [
                'form_params' => $data,
                'allow_redirects' => false
            ]
        );

        if($response->getStatusCode() == 302) {
            return $response->getHeaderLine('Location');
        } else {
            throw new \Exception('Yandex.Kassa no redirect!');
        }
    }

}