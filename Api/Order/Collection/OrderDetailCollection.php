<?php declare(strict_types=1);

namespace Shopware\Api\Order\Collection;

use Shopware\Api\Order\Struct\OrderDetailStruct;

class OrderDetailCollection extends OrderBasicCollection
{
    /**
     * @var OrderDetailStruct[]
     */
    protected $elements = [];

    public function getDeliveryIds(): array
    {
        $ids = [];
        foreach ($this->elements as $element) {
            foreach ($element->getDeliveries()->getIds() as $id) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    public function getDeliveries(): OrderDeliveryBasicCollection
    {
        $collection = new OrderDeliveryBasicCollection();
        foreach ($this->elements as $element) {
            $collection->fill($element->getDeliveries()->getElements());
        }

        return $collection;
    }

    public function getLineItemIds(): array
    {
        $ids = [];
        foreach ($this->elements as $element) {
            foreach ($element->getLineItems()->getIds() as $id) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    public function getLineItems(): OrderLineItemBasicCollection
    {
        $collection = new OrderLineItemBasicCollection();
        foreach ($this->elements as $element) {
            $collection->fill($element->getLineItems()->getElements());
        }

        return $collection;
    }

    public function getTransactionIds(): array
    {
        $ids = [];
        foreach ($this->elements as $element) {
            foreach ($element->getTransactions()->getIds() as $id) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    public function getTransactions(): OrderTransactionBasicCollection
    {
        $collection = new OrderTransactionBasicCollection();
        foreach ($this->elements as $element) {
            $collection->fill($element->getTransactions()->getElements());
        }

        return $collection;
    }

    protected function getExpectedClass(): string
    {
        return OrderDetailStruct::class;
    }
}
