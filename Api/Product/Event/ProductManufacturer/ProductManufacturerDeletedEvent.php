<?php declare(strict_types=1);

namespace Shopware\Api\Product\Event\ProductManufacturer;

use Shopware\Api\Entity\Write\DeletedEvent;
use Shopware\Api\Entity\Write\WrittenEvent;
use Shopware\Api\Product\Definition\ProductManufacturerDefinition;

class ProductManufacturerDeletedEvent extends WrittenEvent implements DeletedEvent
{
    public const NAME = 'product_manufacturer.deleted';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return ProductManufacturerDefinition::class;
    }
}
