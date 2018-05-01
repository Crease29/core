<?php declare(strict_types=1);

namespace Shopware\Api\Config\Event\ConfigFormTranslation;

use Shopware\Api\Config\Definition\ConfigFormTranslationDefinition;
use Shopware\Api\Entity\Write\WrittenEvent;

class ConfigFormTranslationWrittenEvent extends WrittenEvent
{
    public const NAME = 'config_form_translation.written';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDefinition(): string
    {
        return ConfigFormTranslationDefinition::class;
    }
}
