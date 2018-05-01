<?php declare(strict_types=1);

namespace Shopware\Api\Shipping\Struct;

use Shopware\Api\Language\Struct\LanguageBasicStruct;

class ShippingMethodTranslationDetailStruct extends ShippingMethodTranslationBasicStruct
{
    /**
     * @var ShippingMethodBasicStruct
     */
    protected $shippingMethod;

    /**
     * @var LanguageBasicStruct
     */
    protected $language;

    public function getShippingMethod(): ShippingMethodBasicStruct
    {
        return $this->shippingMethod;
    }

    public function setShippingMethod(ShippingMethodBasicStruct $shippingMethod): void
    {
        $this->shippingMethod = $shippingMethod;
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
