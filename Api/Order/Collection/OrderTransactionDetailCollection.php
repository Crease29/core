<?php declare(strict_types=1);

namespace Shopware\Api\Order\Collection;

use Shopware\Api\Order\Struct\OrderTransactionDetailStruct;
use Shopware\Api\Payment\Collection\PaymentMethodBasicCollection;

class OrderTransactionDetailCollection extends OrderTransactionBasicCollection
{
    /**
     * @var OrderTransactionDetailStruct[]
     */
    protected $elements = [];

    public function getPaymentMethods(): PaymentMethodBasicCollection
    {
        return new PaymentMethodBasicCollection(
            $this->fmap(function (OrderTransactionDetailStruct $orderTransaction) {
                return $orderTransaction->getPaymentMethod();
            })
        );
    }

    protected function getExpectedClass(): string
    {
        return OrderTransactionDetailStruct::class;
    }
}
