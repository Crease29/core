<?php declare(strict_types=1);

namespace Shopware\Api\Country\Collection;

use Shopware\Api\Country\Struct\CountryAreaBasicStruct;
use Shopware\Api\Entity\EntityCollection;

class CountryAreaBasicCollection extends EntityCollection
{
    /**
     * @var CountryAreaBasicStruct[]
     */
    protected $elements = [];

    public function get(string $id): ? CountryAreaBasicStruct
    {
        return parent::get($id);
    }

    public function current(): CountryAreaBasicStruct
    {
        return parent::current();
    }

    protected function getExpectedClass(): string
    {
        return CountryAreaBasicStruct::class;
    }
}
