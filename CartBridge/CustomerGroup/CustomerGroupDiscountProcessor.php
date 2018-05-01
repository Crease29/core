<?php
declare(strict_types=1);
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\CartBridge\CustomerGroup;

use Shopware\Api\Customer\Collection\CustomerGroupDiscountBasicCollection;
use Shopware\Cart\Cart\CartProcessorInterface;
use Shopware\Cart\Cart\Struct\CalculatedCart;
use Shopware\Cart\Cart\Struct\Cart;
use Shopware\Cart\LineItem\CalculatedLineItem;
use Shopware\Cart\LineItem\Discount;
use Shopware\Cart\Price\PercentagePriceCalculator;
use Shopware\Context\Struct\StorefrontContext;
use Shopware\Framework\Struct\StructCollection;

class CustomerGroupDiscountProcessor implements CartProcessorInterface
{
    /**
     * @var PercentagePriceCalculator
     */
    private $percentagePriceCalculator;

    public function __construct(PercentagePriceCalculator $percentagePriceCalculator)
    {
        $this->percentagePriceCalculator = $percentagePriceCalculator;
    }

    public function process(
        Cart $cart,
        CalculatedCart $calculatedCart,
        StructCollection $dataCollection,
        StorefrontContext $context
    ): void {
        if (!$context->getCustomer()) {
            return;
        }

        $goods = $calculatedCart->getCalculatedLineItems()->filterGoods();

        if ($goods->count() === 0) {
            return;
        }

        $prices = $goods->getPrices();

        /** @var CustomerGroupDiscountBasicCollection $discountCollection */
        $discountCollection = $dataCollection->get(self::class);

        if (!$discountCollection) {
            return;
        }

        $discount = $discountCollection->getDiscountForCartAmount(
            $prices->sum()->getTotalPrice(),
            $context->getCustomer()->getGroup()->getId()
        );

        if (!$discount) {
            return;
        }

        $discount = $this->percentagePriceCalculator->calculate($discount, $prices, $context);
        $calculatedCart->getCalculatedLineItems()->add(
            new CalculatedLineItem('customer-group-discount', $discount, 1, 'customer-group-discount')
        );
    }
}
