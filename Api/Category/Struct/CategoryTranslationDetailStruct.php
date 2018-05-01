<?php declare(strict_types=1);

namespace Shopware\Api\Category\Struct;

use Shopware\Api\Language\Struct\LanguageBasicStruct;

class CategoryTranslationDetailStruct extends CategoryTranslationBasicStruct
{
    /**
     * @var CategoryBasicStruct
     */
    protected $category;

    /**
     * @var LanguageBasicStruct
     */
    protected $language;

    public function getCategory(): CategoryBasicStruct
    {
        return $this->category;
    }

    public function setCategory(CategoryBasicStruct $category): void
    {
        $this->category = $category;
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
