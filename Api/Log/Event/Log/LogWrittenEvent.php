<?php declare(strict_types=1);

namespace Shopware\Api\Log\Event\Log;

use Shopware\Api\Entity\Write\WrittenEvent;
use Shopware\Api\Log\Definition\LogDefinition;

class LogWrittenEvent extends WrittenEvent
{
    public const NAME = 'log.written';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return LogDefinition::class;
    }
}
