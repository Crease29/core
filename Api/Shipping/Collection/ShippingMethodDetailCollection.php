<?php declare(strict_types=1);

namespace Shopware\Api\Shipping\Collection;

use Shopware\Api\Shipping\Struct\ShippingMethodDetailStruct;

class ShippingMethodDetailCollection extends ShippingMethodBasicCollection
{
    /**
     * @var ShippingMethodDetailStruct[]
     */
    protected $elements = [];

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

    public function getTranslations(): ShippingMethodTranslationBasicCollection
    {
        $collection = new ShippingMethodTranslationBasicCollection();
        foreach ($this->elements as $element) {
            $collection->fill($element->getTranslations()->getElements());
        }

        return $collection;
    }

    protected function getExpectedClass(): string
    {
        return ShippingMethodDetailStruct::class;
    }
}
