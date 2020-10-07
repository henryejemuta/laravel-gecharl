<?php

namespace HenryEjemuta\LaravelGecharl\Facades;

use HenryEjemuta\LaravelGecharl\Classes\GecharlResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static GecharlResponse getWalletBalance()
 * @method static GecharlResponse purchaseAirtime(string $network, int $amount, $phoneNumber)
 * @method static GecharlResponse getDataPlans(string $network = null)
 * @method static GecharlResponse purchaseDataBundle(string $network, string $plan, string $recipient)
 * @method static GecharlResponse verifyMeterNumber(string $disco, string $meterNumber, string $meterType)
 * @method static GecharlResponse purchaseElectricity(string $disco, string $meterNumber, string $meterType, $amount, string $productCode = null)
 * @method static GecharlResponse verifySmartCardNumber(string $multichoiceType, string $smartCardNumber)
 * @method static GecharlResponse purchaseMultiChoice(string $multiChoiceType, string $smartCardNumber, $amount, string $productCode, string $plan, string $customerPhoneNumber = '', string $customerName = '', string $transactionId = null)
 *
 * @see \HenryEjemuta\LaravelGecharl\Gecharl
 */
class Gecharl extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'gecharl';
    }
}
