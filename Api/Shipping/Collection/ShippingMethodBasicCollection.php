<?php declare(strict_types=1);

namespace Shopware\Api\Shipping\Collection;

use Shopware\Api\Entity\EntityCollection;
use Shopware\Api\Shipping\Struct\ShippingMethodBasicStruct;

class ShippingMethodBasicCollection extends EntityCollection
{
    /**
     * @var ShippingMethodBasicStruct[]
     */
    protected $elements = [];

    public function get(string $id): ? ShippingMethodBasicStruct
    {
        return parent::get($id);
    }

    public function current(): ShippingMethodBasicStruct
    {
        return parent::current();
    }

    public function getPriceIds(): array
    {
        $ids = [];
        foreach ($this->elements as $element) {
            foreach ($element->getPrices()->getIds() as $id) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    public function getPrices(): ShippingMethodPriceBasicCollection
    {
        $collection = new ShippingMethodPriceBasicCollection();
        foreach ($this->elements as $element) {
            $collection->fill($element->getPrices()->getElements());
        }

        return $collection;
    }

    protected function getExpectedClass(): string
    {
        return ShippingMethodBasicStruct::class;
    }
}
