<?php

namespace HenryEjemuta\LaravelGecharl;

use HenryEjemuta\LaravelGecharl\Classes\GecharlResponse;
use HenryEjemuta\LaravelGecharl\Exceptions\GecharlErrorException;
use Illuminate\Support\Facades\Http;

class Gecharl
{
    /**
     * base url
     *
     * @var string
     */
    private $baseUrl;

    /**
     * the cart session key
     *
     * @var string
     */
    protected $instanceName;

    /**
     * Flexible handle to the VTPass Configuration
     *
     * @var
     */
    protected $config;

    public function __construct($baseUrl, $instanceName, $config)
    {
        $this->baseUrl = $baseUrl;
        $this->instanceName = $instanceName;
        $this->config = $config;
    }

    /**
     * get instance name of the cart
     *
     * @return string
     */
    public function getInstanceName()
    {
        return $this->instanceName;
    }

    private function withOAuth2()
    {
        return Http::withToken($this->config['api_key']);
    }

    /**
     * Get Your wallet available balance, Wallet is identified by username set in gecharl config or environmental variable
     * @return GecharlResponse
     * @throws GecharlErrorException
     */
    public function getWalletBalance(): GecharlResponse
    {
        $response = $this->withOAuth2()->get("{$this->baseUrl}/account?username={$this->config['username']}");

        $responseObject = json_decode($response->body());
        if (isset($responseObject->status) && isset($responseObject->message))
            return new GecharlResponse($responseObject->status ? 200 : 422, $responseObject->message);
        return new GecharlResponse(504, null);
    }

    /**
     * Purchase Airtime with py specifying the network (i.e. mtn, glo, airtel, or 9mobile to buy airtime corresponding the provided telco service code)
     * @param string $network Network ID e.g mtn, glo, airtel, or 9mobile
     * @param int $amount The amount you wish to topup
     * @param string $phoneNumber The phone number of the recipient of this service
     * @return GecharlResponse
     *
     * @throws GecharlErrorException
     */
    public function purchaseAirtime(string $network, int $amount, $phoneNumber): GecharlResponse
    {
        $response = $this->withOAuth2()->post("{$this->baseUrl}/account?username={$this->config['username']}", [
            'network' => $network,
            'amount' => $amount,
            'phone_number' => $phoneNumber
        ]);

        $responseObject = json_decode($response->body());
        if (isset($responseObject->status) && isset($responseObject->message))
            return new GecharlResponse($responseObject->status ? 200 : 422, $responseObject->message);
        return new GecharlResponse(504, null);
    }

    /**
     * Get all data plan for all networks or for specified network only
     * @param string|null $network
     * @return GecharlResponse
     * @throws GecharlErrorException
     */
    public function getDataPlans(string $network = null): GecharlResponse
    {
        $endpoint = "{$this->baseUrl}/data_lookup";
        if ($network !== null) $endpoint .= "?network=$network";
        $response = $this->withOAuth2()->get($endpoint);

        $responseObject = json_decode($response->body());
        if ($response->successful())
            return new GecharlResponse(200, $responseObject);
        return new GecharlResponse(504, null);
    }

    /**
     * Buy data bundle
     * @param string $network Unique network identification code e.g. mtn, glo, airtel...
     * @param string $plan Plan code of data plan to subscribe to
     * @param string $recipient Phone number to receive data subscription
     * @return GecharlResponse
     * @throws GecharlErrorException
     */
    public function purchaseDataBundle(string $network, string $plan, string $recipient): GecharlResponse
    {
        $endpoint = "{$this->baseUrl}/data?network=$network&plan=$plan&recipent=$recipient";
        $response = $this->withOAuth2()->get($endpoint);

        $responseObject = json_decode($response->body());
        if ($response->successful())
            return new GecharlResponse($responseObject->status_code, $responseObject->message);
        return new GecharlResponse(504, null);
    }

    /**
     * You need to verify your meter number before purchasing.
     *
     * Please note the disco unique codes below:
     * Ikaja Electricity = <strong>ie</strong>
     * Eko Electricity = <strong>ekedc</strong>
     * Enugu Electricity = <strong>eedc</strong>
     * Kano Electricity = <strong>kano</strong>
     * Port Harcourt Electricity = <strong>phed</strong>
     * Abuja Electricity = <strong>abuja</strong>
     * Ibadan Electricity = <strong>ibedc</strong>
     *
     * On successful validation for a prepaid meter number, a product_code key comes with it, which should be passed along other parameters when paying.
     *
     *
     * @param string $disco Unique code of the Electricity distribution company the meter number is for
     * @param string $meterNumber Meter Number to verify
     * @param string $meterType Meter type i.e. <strong>prepaid</strong> or <strong>postpaid</strong>
     * @return GecharlResponse
     * @throws GecharlErrorException
     */
    public function verifyMeterNumber(string $disco, string $meterNumber, string $meterType): GecharlResponse
    {
        $endpoint = "{$this->baseUrl}/electricity/validate/$disco/?meter_number=$meterNumber&meter_type=$meterType";
        $response = $this->withOAuth2()->get($endpoint);

        $responseObject = json_decode($response->body());
        if ($response->successful())
            return new GecharlResponse($responseObject->status_code, $responseObject->message);
        return new GecharlResponse(504, null);
    }

    /**
     * Purchase Electricity
     *
     * Please note the disco unique codes below:
     * Ikaja Electricity = <strong>ie</strong>
     * Eko Electricity = <strong>ekedc</strong>
     * Enugu Electricity = <strong>eedc</strong>
     * Kano Electricity = <strong>kano</strong>
     * Port Harcourt Electricity = <strong>phed</strong>
     * Abuja Electricity = <strong>abuja</strong>
     * Ibadan Electricity = <strong>ibedc</strong>
     *
     *
     * @param string $disco Unique code of the Electricity distribution company the meter number is for
     * @param string $meterNumber Meter Number to verify
     * @param string $meterType Meter type i.e. <strong>prepaid</strong> or <strong>postpaid</strong>
     * @param $amount
     * @param string|null $productCode
     * @return GecharlResponse
     * @throws GecharlErrorException
     */
    public function purchaseElectricity(string $disco, string $meterNumber, string $meterType, $amount, string $productCode = null): GecharlResponse
    {
        $endpoint = "{$this->baseUrl}/electricity/payment/$disco";
        $params = [
            "meter_number" => $meterNumber,
            "meter_type" => $meterType,
            "amount" => $amount,
        ];
        if ($productCode !== null) $params['product_code'] = $productCode;
        $response = $this->withOAuth2()->post($endpoint, $params);

        $responseObject = json_decode($response->body());
        if ($response->successful())
            return new GecharlResponse($responseObject->status_code, $responseObject->message);
        return new GecharlResponse(504, null);
    }

    /**
     * Multichoice(DSTV and GoTv) Smart Card Number/Decoder verification
     * You need to verify your Smart card number before purchasing.
     *
     * @param string $multichoiceType DSTV|GOTV
     * @param string $smartCardNumber Customer unique smart card number to subscribe
     * @return GecharlResponse
     * @throws GecharlErrorException
     */
    public function verifySmartCardNumber(string $multichoiceType, string $smartCardNumber): GecharlResponse
    {
        $endpoint = "{$this->baseUrl}/validate/multichoice?multichoice_type=$multichoiceType&smart_card_no=$smartCardNumber";
        $response = $this->withOAuth2()->get($endpoint);

        $responseObject = json_decode($response->body());
        if ($response->successful())
            return new GecharlResponse($responseObject->status_code, $responseObject->message);
        return new GecharlResponse(504, null);
    }

    /**
     * Purchase DSTV or GoTv Cable Tv Plan
     * @param string $multiChoiceType DSTV|GOTV
     * @param string $smartCardNumber Customer unique smart card number to subscribe
     * @param $amount
     * @param string $productCode productCode as gotten from the verification call
     * @param string $plan Unique product_code as gotten from the verification call for available plan for the provided SmartCard Number
     * @param string $customerPhoneNumber
     * @param string $customerName
     * @param string|null $transactionId
     * @return GecharlResponse
     * @throws GecharlErrorException
     */
    public function purchaseMultiChoice(string $multiChoiceType, string $smartCardNumber, $amount, string $productCode, string $plan, string $customerPhoneNumber = '', string $customerName = '', string $transactionId = null): GecharlResponse
    {
        $endpoint = "{$this->baseUrl}/multichoice/payment";
        $params = [
            "multichoice_type" => $multiChoiceType,
            "smart_card_no" => $smartCardNumber,
            "amount" => $amount,
            "product_code" => $plan,
            "productCode" => $productCode,
        ];
        if (empty($customerName)) $params['customer_name'] = $customerName;
        if (empty($customerPhoneNumber)) $params['phone_number'] = $customerPhoneNumber;
        if ($transactionId !== null) $params['transaction_id'] = $transactionId;
        $response = $this->withOAuth2()->post($endpoint, $params);

        $responseObject = json_decode($response->body());
        if ($response->successful())
            return new GecharlResponse($responseObject->status_code, $responseObject->message);
        return new GecharlResponse(504, null);
    }


    /**
     * StarTimes Smart Card Number/Decoder verification
     * You need to verify your Smart card number before purchasing.
     *
     * @param string $smartCardNumber Customer unique smart card number to subscribe
     * @return GecharlResponse
     * @throws GecharlErrorException
     */
    public function verifyStarTimesSmartCardNumber(string $smartCardNumber): GecharlResponse
    {
        $endpoint = "{$this->baseUrl}/validate/startimes?smart_card_no=$smartCardNumber";
        $response = $this->withOAuth2()->get($endpoint);

        $responseObject = json_decode($response->body());
        if ($response->successful())
            return new GecharlResponse($responseObject->status_code, $responseObject->message);
        return new GecharlResponse(504, null);
    }

    /**
     * Purchase StarTimes Cable Tv Plan
     * The product_code is either <strong>NOVA</strong>, <strong>BASIC</strong>, <strong>SMART</strong>, <strong>CLASSIC</strong> or <strong>SUPER</strong>
     *
     * @param string $smartCardNumber Customer unique smart card number to subscribe
     * @param $amount
     * @param string $productCode productCode as gotten from the verification call
     * @param string $plan Unique product_code as gotten from the verification call for available plan for the provided SmartCard Number
     * @param string $customerPhoneNumber
     * @param string $customerName
     * @param string|null $transactionId
     * @return GecharlResponse
     * @throws GecharlErrorException
     */
    public function purchaseStarTimes(string $smartCardNumber, $amount, string $productCode, string $plan, string $transactionId, string $customerPhoneNumber = '', string $customerName = ''): GecharlResponse
    {
        $endpoint = "{$this->baseUrl}/startimes/payment";
        $params = [
            "smart_card_no" => $smartCardNumber,
            "amount" => $amount,
            "product_code" => $plan,
            "productCode" => $productCode,
            "transaction_id" => $transactionId,
        ];
        if (empty($customerName)) $params['customer_name'] = $customerName;
        if (empty($customerPhoneNumber)) $params['phone_number'] = $customerPhoneNumber;
        $response = $this->withOAuth2()->post($endpoint, $params);

        $responseObject = json_decode($response->body());
        if ($response->successful())
            return new GecharlResponse($responseObject->status_code, $responseObject->message);
        return new GecharlResponse(504, null);
    }
}
