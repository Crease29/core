<?php declare(strict_types=1);

namespace Shopware\Cart\Transaction;

use Shopware\Cart\Cart\CartProcessorInterface;
use Shopware\Cart\Cart\Struct\CalculatedCart;
use Shopware\Cart\Cart\Struct\Cart;
use Shopware\Cart\Price\Struct\CalculatedPrice;
use Shopware\Cart\Transaction\Struct\Transaction;
use Shopware\Context\Struct\StorefrontContext;
use Shopware\Framework\Struct\StructCollection;

class TransactionSeparatorProcessor implements CartProcessorInterface
{
    public function process(
        Cart $cart,
        CalculatedCart $calculatedCart,
        StructCollection $dataCollection,
        StorefrontContext $context
    ): void {
        $price = $calculatedCart->getPrice()->getTotalPrice();

        $calculatedCart->addTransaction(new Transaction(
            new CalculatedPrice(
                $price,
                $price,
                $calculatedCart->getPrice()->getCalculatedTaxes(),
                $calculatedCart->getPrice()->getTaxRules()
            ),
            $context->getPaymentMethod()->getId())
        );
    }
}
