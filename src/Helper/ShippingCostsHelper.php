<?php

namespace ElasticExportGoogleShopping\Helper;

use Illuminate\Support\Collection;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\ItemShippingProfiles\Contracts\ItemShippingProfilesRepositoryContract;
use Plenty\Modules\Item\ItemShippingProfiles\Models\ItemShippingProfiles;
use Plenty\Modules\Order\Shipping\Contracts\ParcelServicePresetRepositoryContract;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\System\Contracts\WebstoreRepositoryContract;
use Plenty\Plugin\Log\Loggable;

class ShippingCostsHelper
{
    use Loggable;

    /**
     * @var ParcelServicePresetRepositoryContract
     */
    private $parcelServicePresetRepositoryContract;

    /**
     * @var ItemShippingProfilesRepositoryContract
     */
    private $itemShippingProfilesRepositoryContract;

    /**
     * @var WebstoreRepositoryContract
     */
    private $webstoreRepositoryContract;

    /**
     * @var CountryRepositoryContract
     */
    private $countryRepositoryContract;

    /**
     * PriceHelper constructor.
     * @param ParcelServicePresetRepositoryContract $parcelServicePresetRepositoryContract
     * @param ItemShippingProfilesRepositoryContract $itemShippingProfilesRepositoryContract
     * @param WebstoreRepositoryContract $webstoreRepositoryContract
     * @param CountryRepositoryContract $countryRepositoryContract
     */
    public function __construct(
        ParcelServicePresetRepositoryContract $parcelServicePresetRepositoryContract,
        ItemShippingProfilesRepositoryContract $itemShippingProfilesRepositoryContract,
        WebstoreRepositoryContract $webstoreRepositoryContract,
        CountryRepositoryContract $countryRepositoryContract
    )
    {
        $this->parcelServicePresetRepositoryContract = $parcelServicePresetRepositoryContract;
        $this->itemShippingProfilesRepositoryContract = $itemShippingProfilesRepositoryContract;
        $this->webstoreRepositoryContract = $webstoreRepositoryContract;
        $this->countryRepositoryContract = $countryRepositoryContract;
    }

    /**
     * @param array $variation
     * @param KeyValue $settings
     */
    public function getShippingCosts(array $variation, KeyValue $settings){

        $this->getLogger('getShippingCosts')
            ->addReference('Variation', (int)$variation['id'])
            ->error('ElasticExportGoogleShopping::Debug.getShippingCostsStart');

        $webstore = $this->webstoreRepositoryContract->findByPlentyId($settings->get('plentyId'));
        $country = $this->countryRepositoryContract->getCountryById($settings->get('destination'));
        $shippingCosts = 0;

        $itemShippingProfiles = $this->itemShippingProfilesRepositoryContract->findByItemId($variation['data']['item']['id']);

        /** @var ItemShippingProfiles $itemShippingProfile */
        foreach($itemShippingProfiles as $itemShippingProfile){
            $parcelServicePreset = $this->parcelServicePresetRepositoryContract->getPresetById($itemShippingProfile->profileId);

            $this->getLogger('getShippingCosts')
                ->addReference('Variation', (int)$variation['id'])
                ->error('ElasticExportGoogleShopping::Debug.parcelServicePreset', $parcelServicePreset);

            if(in_array($webstore->id, $parcelServicePreset->supportedMultishop) || in_array('-1', $parcelServicePreset->supportedMultishop)){

                $this->getLogger('getShippingCosts')
                    ->addReference('Variation', (int)$variation['id'])
                    ->error('ElasticExportGoogleShopping::Debug.supportedMultishop');

                $parcelServiceRegionConstraints = Collection::make($parcelServicePreset->parcelServiceRegionConstraint)->firstWhere('shippingRegionId', $country->shippingDestinationId);

                $this->getLogger('getShippingCosts')
                    ->addReference('Variation', (int)$variation['id'])
                    ->error('ElasticExportGoogleShopping::Debug.parcelServiceRegionConstraints', $parcelServiceRegionConstraints);

                foreach($parcelServiceRegionConstraints->constraint as $constraint){
                    $this->getLogger('getShippingCosts')
                        ->addReference('Variation', (int)$variation['id'])
                        ->error('ElasticExportGoogleShopping::Debug.constraint', [
                            'startValue' => $constraint->startValue,
                            'weightG' => $variation['data']['variation']['weightG'],
                            'result' => $constraint->startValue < $variation['data']['variation']['weightG'],
                            'shippingCosts' => $shippingCosts,
                            'constraint->cost' => $constraint->cost
                        ]);

                    if($constraint->startValue < $variation['data']['variation']['weightG']){
                        if($shippingCosts < $constraint->cost){
                            $shippingCosts = $constraint->cost;
                        }
                    }
                }
            }
        }


        $this->getLogger('getShippingCosts')
            ->addReference('Variation', (int)$variation['id'])
            ->error('ElasticExportGoogleShopping::Debug.getShippingCosts', [
            'shippingCosts' => $shippingCosts,
        ]);

        return $shippingCosts;
    }
}
