<?php

namespace ElasticExportGoogleShopping\Helper;

use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\VariationSalesPrice\Contracts\VariationSalesPriceRepositoryContract;

class SalesPriceHelper
{

    const SALES_PRICE_ID = 1;
    const SALE_SALES_PRICE_ID = 1;
    const CURRENCY = 'EUR';

    /**
     * @var VariationSalesPriceRepositoryContract
     */
    private $variationSalesPriceRepositoryContract;

    /**
     * PriceHelper constructor.
     * @param VariationSalesPriceRepositoryContract $variationSalesPriceRepositoryContract
     */
    public function __construct(
        VariationSalesPriceRepositoryContract $variationSalesPriceRepositoryContract
    )
    {
        $this->variationSalesPriceRepositoryContract = $variationSalesPriceRepositoryContract;
    }

    /**
     * @param array $variation
     * @param KeyValue $settings
     */
    public function getPrice(array $variation, KeyValue $settings){

        $variationSalesPrice = $this->variationSalesPriceRepositoryContract->show(self::SALES_PRICE_ID, $variation['id']);


        return $variationSalesPrice->price.' '.self::CURRENCY;
    }

    /**
     * @param array $variation
     * @param KeyValue $settings
     */
    public function getSalePrice(array $variation, KeyValue $settings){

        $price = 0.00;


        return $price;
    }
}
