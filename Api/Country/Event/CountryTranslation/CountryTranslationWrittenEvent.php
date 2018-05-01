<?php declare(strict_types=1);

namespace Shopware\Api\Country\Event\CountryTranslation;

use Shopware\Api\Country\Definition\CountryTranslationDefinition;
use Shopware\Api\Entity\Write\WrittenEvent;

class CountryTranslationWrittenEvent extends WrittenEvent
{
    public const NAME = 'country_translation.written';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return CountryTranslationDefinition::class;
    }
}
