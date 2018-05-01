<?php declare(strict_types=1);

namespace Shopware\Api\Product\Event\ProductStreamAssignment;

use Shopware\Api\Entity\Write\DeletedEvent;
use Shopware\Api\Entity\Write\WrittenEvent;
use Shopware\Api\Product\Definition\ProductStreamAssignmentDefinition;

class ProductStreamAssignmentDeletedEvent extends WrittenEvent implements DeletedEvent
{
    public const NAME = 'product_stream_assignment.deleted';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return ProductStreamAssignmentDefinition::class;
    }
}
