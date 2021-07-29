<?php

namespace ElasticExportGoogleShopping\Helper;

use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Property\V2\Contracts\PropertyRelationRepositoryContract;
use Plenty\Modules\Property\V2\Models\PropertyRelation;
use Plenty\Plugin\Log\Loggable;

class ShippingCostsHelper
{
    use Loggable;

    /**
     * @var CountryRepositoryContract
     */
    private $countryRepositoryContract;

    /**
     * @var PropertyRelationRepositoryContract
     */
    private $propertyRelationRepositoryContract;


    /**
     * PriceHelper constructor.
     * @param CountryRepositoryContract $countryRepositoryContract
     * @param PropertyRelationRepositoryContract $propertyRelationRepositoryContract
     */
    public function __construct(
        CountryRepositoryContract $countryRepositoryContract,
        PropertyRelationRepositoryContract $propertyRelationRepositoryContract
    )
    {
        $this->countryRepositoryContract = $countryRepositoryContract;
        $this->propertyRelationRepositoryContract = $propertyRelationRepositoryContract;
    }

    /**
     * @param array $variation
     * @param KeyValue $settings
     */
    public function getShippingCosts(array $variation, KeyValue $settings){

        $shippingCosts = 0;

        $country = $this->countryRepositoryContract->getCountryById($settings->get('destination'));
        $propertyID = 0;

        if($country->shippingDestinationId == 1){ // EU
            $propertyID = 6;

        } elseif($country->shippingDestinationId == 2){ // DE
            $propertyID = 5;

        } elseif($country->shippingDestinationId == 3){ // Europa
            $propertyID = 7;

        } elseif($country->shippingDestinationId == 4){ // Welt
            $propertyID = 8;

        }

        if($propertyID > 0){

            $this->propertyRelationRepositoryContract->setFilters([
                'type' => 'item',
                'targetId' => $variation['id'],
                'propertyId' => $propertyID
            ]);

            $propertyRelations = $this->propertyRelationRepositoryContract->search();

            /** @var PropertyRelation $propertyRelation */
            foreach($propertyRelations as $propertyRelation){
                if($propertyRelation->propertyId == $propertyID && $propertyRelation->targetId == $variation['id']){
                    if(!empty($propertyRelation->value)){
                        $shippingCosts = $country->isoCode2.':::'.$propertyRelation->value.' EUR';
                    }
                }
            }

            $this->getLogger('getShippingCosts')
                ->addReference('variationId', (int)$variation['id'])
                ->debug('ElasticExportGoogleShopping::Debug.getShippingCosts', [
                    'propertyRelations' => $propertyRelations
                ]);
        }


        return $shippingCosts;
    }
}
