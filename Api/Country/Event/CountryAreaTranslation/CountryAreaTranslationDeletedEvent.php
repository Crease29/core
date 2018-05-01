<?php declare(strict_types=1);

namespace Shopware\Api\Country\Event\CountryAreaTranslation;

use Shopware\Api\Country\Definition\CountryAreaTranslationDefinition;
use Shopware\Api\Entity\Write\DeletedEvent;
use Shopware\Api\Entity\Write\WrittenEvent;

class CountryAreaTranslationDeletedEvent extends WrittenEvent implements DeletedEvent
{
    public const NAME = 'country_area_translation.deleted';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return CountryAreaTranslationDefinition::class;
    }
}
