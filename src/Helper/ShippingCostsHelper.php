<?php

namespace ElasticExportGoogleShopping\Helper;

use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\DefaultShippingCost\Contracts\DefaultShippingCostRepositoryContract;
use Plenty\Modules\Item\DefaultShippingCost\Models\DefaultShippingCost;
use Plenty\Modules\Order\Shipping\ServiceProvider\Contracts\ShippingServiceProviderRepositoryContract;
use Plenty\Plugin\Log\Loggable;

class ShippingCostsHelper
{
    use Loggable;

    const PAYMENT_METHOD_ID = 6000;

    /**
     * @var ShippingServiceProviderRepositoryContract
     */
    private $shippingServiceProviderRepositoryContract;

    /**
     * PriceHelper constructor.
     * @param ShippingServiceProviderRepositoryContract $shippingServiceProviderRepositoryContract
     */
    public function __construct(
        ShippingServiceProviderRepositoryContract $shippingServiceProviderRepositoryContract
    )
    {
        $this->shippingServiceProviderRepositoryContract = $shippingServiceProviderRepositoryContract;
    }

    /**
     * @param array $variation
     * @param KeyValue $settings
     */
    public function getShippingCosts(array $variation, KeyValue $settings){

        $shippingCosts = 0;


        $this->getLogger('getShippingCosts')
            ->addReference('Variation', (int)$variation['id'])
            ->error('ElasticExportGoogleShopping::getShippingCosts', [
            'shippingCosts' => $shippingCosts,
            'variationId' => $variation['data']
        ]);

        return $shippingCosts;
    }
}
