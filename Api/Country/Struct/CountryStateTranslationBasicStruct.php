<?php declare(strict_types=1);

namespace Shopware\Api\Country\Struct;

use Shopware\Api\Entity\Entity;

class CountryStateTranslationBasicStruct extends Entity
{
    /**
     * @var string
     */
    protected $countryStateId;

    /**
     * @var string
     */
    protected $languageId;

    /**
     * @var string
     */
    protected $name;

    public function getCountryStateId(): string
    {
        return $this->countryStateId;
    }

    public function setCountryStateId(string $countryStateId): void
    {
        $this->countryStateId = $countryStateId;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
