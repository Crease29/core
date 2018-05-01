<?php declare(strict_types=1);

namespace Shopware\Api\Country\Event\CountryStateTranslation;

use Shopware\Api\Country\Definition\CountryStateTranslationDefinition;
use Shopware\Api\Entity\Write\DeletedEvent;
use Shopware\Api\Entity\Write\WrittenEvent;

class CountryStateTranslationDeletedEvent extends WrittenEvent implements DeletedEvent
{
    public const NAME = 'country_state_translation.deleted';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return CountryStateTranslationDefinition::class;
    }
}
