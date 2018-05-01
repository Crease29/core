<?php declare(strict_types=1);

namespace Shopware\Api\Customer\Event\CustomerGroup;

use Shopware\Api\Customer\Definition\CustomerGroupDefinition;
use Shopware\Api\Entity\Write\DeletedEvent;
use Shopware\Api\Entity\Write\WrittenEvent;

class CustomerGroupDeletedEvent extends WrittenEvent implements DeletedEvent
{
    public const NAME = 'customer_group.deleted';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return CustomerGroupDefinition::class;
    }
}
