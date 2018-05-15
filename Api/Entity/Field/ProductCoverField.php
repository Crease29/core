<?php

namespace Shopware\Api\Entity\Field;

use Shopware\Content\Product\Definition\ProductMediaDefinition;

class ProductCoverField extends ManyToOneAssociationField
{
    public function __construct(string $propertyName, bool $loadInBasic)
    {
        parent::__construct($propertyName, 'id', ProductMediaDefinition::class, $loadInBasic, 'product_id');
    }
}