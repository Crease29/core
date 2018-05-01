<?php declare(strict_types=1);

namespace Shopware\Api\Country\Collection;

use Shopware\Api\Country\Struct\CountryAreaTranslationBasicStruct;
use Shopware\Api\Entity\EntityCollection;

class CountryAreaTranslationBasicCollection extends EntityCollection
{
    /**
     * @var CountryAreaTranslationBasicStruct[]
     */
    protected $elements = [];

    public function get(string $id): ? CountryAreaTranslationBasicStruct
    {
        return parent::get($id);
    }

    public function current(): CountryAreaTranslationBasicStruct
    {
        return parent::current();
    }

    public function getCountryAreaIds(): array
    {
        return $this->fmap(function (CountryAreaTranslationBasicStruct $countryAreaTranslation) {
            return $countryAreaTranslation->getCountryAreaId();
        });
    }

    public function filterByCountryAreaId(string $id): self
    {
        return $this->filter(function (CountryAreaTranslationBasicStruct $countryAreaTranslation) use ($id) {
            return $countryAreaTranslation->getCountryAreaId() === $id;
        });
    }

    public function getLanguageIds(): array
    {
        return $this->fmap(function (CountryAreaTranslationBasicStruct $countryAreaTranslation) {
            return $countryAreaTranslation->getLanguageId();
        });
    }

    public function filterByLanguageId(string $id): self
    {
        return $this->filter(function (CountryAreaTranslationBasicStruct $countryAreaTranslation) use ($id) {
            return $countryAreaTranslation->getLanguageId() === $id;
        });
    }

    protected function getExpectedClass(): string
    {
        return CountryAreaTranslationBasicStruct::class;
    }
}
