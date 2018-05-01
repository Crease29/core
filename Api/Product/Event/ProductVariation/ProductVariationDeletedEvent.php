<?php declare(strict_types=1);

namespace Shopware\Api\Product\Event\ProductVariation;

use Shopware\Api\Entity\Write\DeletedEvent;
use Shopware\Api\Entity\Write\WrittenEvent;
use Shopware\Api\Product\Definition\ProductVariationDefinition;

class ProductVariationDeletedEvent extends WrittenEvent implements DeletedEvent
{
    public const NAME = 'product_variation.deleted';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return ProductVariationDefinition::class;
    }
}
