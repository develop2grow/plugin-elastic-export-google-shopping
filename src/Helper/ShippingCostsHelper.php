<?php

namespace ElasticExportGoogleShopping\Helper;

use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\DefaultShippingCost\Contracts\DefaultShippingCostRepositoryContract;
use Plenty\Plugin\Log\Loggable;

class ShippingCostsHelper
{
    use Loggable;

    const PAYMENT_METHOD_ID = 6000;

    /**
     * @var DefaultShippingCostRepositoryContract
     */
    private $defaultShippingCostRepositoryContract;

    /**
     * PriceHelper constructor.
     * @param DefaultShippingCostRepositoryContract $defaultShippingCostRepositoryContract
     */
    public function __construct(
        DefaultShippingCostRepositoryContract $defaultShippingCostRepositoryContract
    )
    {
        $this->defaultShippingCostRepositoryContract = $defaultShippingCostRepositoryContract;
    }

    /**
     * @param array $variation
     * @param KeyValue $settings
     */
    public function getShippingCosts(array $variation, KeyValue $settings){

        $shippingCosts = $this->defaultShippingCostRepositoryContract->findShippingCost(
            $variation['data']['item']['id'],
            $settings->get('referrerId'),
            $settings->get('destination'),
            self::PAYMENT_METHOD_ID
        );

        $this->getLogger('ElasticExportGoogleShopping')->debug('ElasticExportGoogleShopping::getShippingCosts', [
            'shippingCosts' => $shippingCosts,
            'variationId' => $variation['data']['item']['id'],
            'referrerId' => $settings->get('referrerId'),
            'shippingDestinationId' => $settings->get('destination'),
            'paymentMethodId' => self::PAYMENT_METHOD_ID
        ]);

        return $shippingCosts;
    }
}
