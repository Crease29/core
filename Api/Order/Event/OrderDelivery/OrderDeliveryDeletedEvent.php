<?php declare(strict_types=1);

namespace Shopware\Api\Order\Event\OrderDelivery;

use Shopware\Api\Entity\Write\DeletedEvent;
use Shopware\Api\Entity\Write\WrittenEvent;
use Shopware\Api\Order\Definition\OrderDeliveryDefinition;

class OrderDeliveryDeletedEvent extends WrittenEvent implements DeletedEvent
{
    public const NAME = 'order_delivery.deleted';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return OrderDeliveryDefinition::class;
    }
}
