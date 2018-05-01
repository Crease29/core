<?php declare(strict_types=1);

namespace Shopware\Api\User\Event\User;

use Shopware\Api\Entity\Write\DeletedEvent;
use Shopware\Api\Entity\Write\WrittenEvent;
use Shopware\Api\User\Definition\UserDefinition;

class UserDeletedEvent extends WrittenEvent implements DeletedEvent
{
    public const NAME = 'user.deleted';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return UserDefinition::class;
    }
}
