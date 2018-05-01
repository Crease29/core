<?php declare(strict_types=1);

namespace Shopware\Api\Tax\Collection;

use Shopware\Api\Country\Collection\CountryAreaBasicCollection;
use Shopware\Api\Country\Collection\CountryBasicCollection;
use Shopware\Api\Country\Collection\CountryStateBasicCollection;
use Shopware\Api\Customer\Collection\CustomerGroupBasicCollection;
use Shopware\Api\Tax\Struct\TaxAreaRuleDetailStruct;

class TaxAreaRuleDetailCollection extends TaxAreaRuleBasicCollection
{
    /**
     * @var TaxAreaRuleDetailStruct[]
     */
    protected $elements = [];

    public function getCountryAreas(): CountryAreaBasicCollection
    {
        return new CountryAreaBasicCollection(
            $this->fmap(function (TaxAreaRuleDetailStruct $taxAreaRule) {
                return $taxAreaRule->getCountryArea();
            })
        );
    }

    public function getCountries(): CountryBasicCollection
    {
        return new CountryBasicCollection(
            $this->fmap(function (TaxAreaRuleDetailStruct $taxAreaRule) {
                return $taxAreaRule->getCountry();
            })
        );
    }

    public function getCountryStates(): CountryStateBasicCollection
    {
        return new CountryStateBasicCollection(
            $this->fmap(function (TaxAreaRuleDetailStruct $taxAreaRule) {
                return $taxAreaRule->getCountryState();
            })
        );
    }

    public function getTaxes(): TaxBasicCollection
    {
        return new TaxBasicCollection(
            $this->fmap(function (TaxAreaRuleDetailStruct $taxAreaRule) {
                return $taxAreaRule->getTax();
            })
        );
    }

    public function getCustomerGroups(): CustomerGroupBasicCollection
    {
        return new CustomerGroupBasicCollection(
            $this->fmap(function (TaxAreaRuleDetailStruct $taxAreaRule) {
                return $taxAreaRule->getCustomerGroup();
            })
        );
    }

    public function getTranslationIds(): array
    {
        $ids = [];
        foreach ($this->elements as $element) {
            foreach ($element->getTranslations()->getIds() as $id) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    public function getTranslations(): TaxAreaRuleTranslationBasicCollection
    {
        $collection = new TaxAreaRuleTranslationBasicCollection();
        foreach ($this->elements as $element) {
            $collection->fill($element->getTranslations()->getElements());
        }

        return $collection;
    }

    protected function getExpectedClass(): string
    {
        return TaxAreaRuleDetailStruct::class;
    }
}
