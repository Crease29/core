<?php declare(strict_types=1);

namespace Shopware\Api\Config\Collection;

use Shopware\Api\Config\Struct\ConfigFormTranslationBasicStruct;
use Shopware\Api\Entity\EntityCollection;

class ConfigFormTranslationBasicCollection extends EntityCollection
{
    /**
     * @var ConfigFormTranslationBasicStruct[]
     */
    protected $elements = [];

    public function get(string $id): ? ConfigFormTranslationBasicStruct
    {
        return parent::get($id);
    }

    public function current(): ConfigFormTranslationBasicStruct
    {
        return parent::current();
    }

    public function getConfigFormIds(): array
    {
        return $this->fmap(function (ConfigFormTranslationBasicStruct $configFormTranslation) {
            return $configFormTranslation->getConfigFormId();
        });
    }

    public function filterByConfigFormId(string $id): self
    {
        return $this->filter(function (ConfigFormTranslationBasicStruct $configFormTranslation) use ($id) {
            return $configFormTranslation->getConfigFormId() === $id;
        });
    }

    public function getLocaleIds(): array
    {
        return $this->fmap(function (ConfigFormTranslationBasicStruct $configFormTranslation) {
            return $configFormTranslation->getLocaleId();
        });
    }

    public function filterByLocaleId(string $id): self
    {
        return $this->filter(function (ConfigFormTranslationBasicStruct $configFormTranslation) use ($id) {
            return $configFormTranslation->getLocaleId() === $id;
        });
    }

    protected function getExpectedClass(): string
    {
        return ConfigFormTranslationBasicStruct::class;
    }
}
