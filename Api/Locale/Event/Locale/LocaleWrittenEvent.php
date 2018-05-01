<?php declare(strict_types=1);

namespace Shopware\Api\Locale\Event\Locale;

use Shopware\Api\Entity\Write\WrittenEvent;
use Shopware\Api\Locale\Definition\LocaleDefinition;

class LocaleWrittenEvent extends WrittenEvent
{
    public const NAME = 'locale.written';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return LocaleDefinition::class;
    }
}
