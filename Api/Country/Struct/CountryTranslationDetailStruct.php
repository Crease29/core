<?php declare(strict_types=1);

namespace Shopware\Api\Country\Struct;

use Shopware\Api\Language\Struct\LanguageBasicStruct;

class CountryTranslationDetailStruct extends CountryTranslationBasicStruct
{
    /**
     * @var CountryBasicStruct
     */
    protected $country;

    /**
     * @var LanguageBasicStruct
     */
    protected $language;

    public function getCountry(): CountryBasicStruct
    {
        return $this->country;
    }

    public function setCountry(CountryBasicStruct $country): void
    {
        $this->country = $country;
    }

    public function getLanguage(): LanguageBasicStruct
    {
        return $this->language;
    }

    public function setLanguage(LanguageBasicStruct $language): void
    {
        $this->language = $language;
    }
}
