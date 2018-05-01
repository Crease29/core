<?php declare(strict_types=1);

namespace Shopware\Api\Order\Struct;

use Shopware\Api\Order\Collection\OrderStateTranslationBasicCollection;

class OrderStateDetailStruct extends OrderStateBasicStruct
{
    /**
     * @var OrderStateTranslationBasicCollection
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = new OrderStateTranslationBasicCollection();
    }

    public function getTranslations(): OrderStateTranslationBasicCollection
    {
        return $this->translations;
    }

    public function setTranslations(OrderStateTranslationBasicCollection $translations): void
    {
        $this->translations = $translations;
    }
}
