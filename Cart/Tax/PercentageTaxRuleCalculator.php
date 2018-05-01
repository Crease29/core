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

namespace Shopware\Cart\Tax;

use Shopware\Cart\Tax\Struct\CalculatedTax;
use Shopware\Cart\Tax\Struct\PercentageTaxRule;
use Shopware\Cart\Tax\Struct\TaxRule;
use Shopware\Cart\Tax\Struct\TaxRuleInterface;

class PercentageTaxRuleCalculator implements TaxRuleCalculatorInterface
{
    /**
     * @var TaxRuleCalculatorInterface
     */
    private $taxRuleCalculator;

    public function __construct(TaxRuleCalculatorInterface $taxRuleCalculator)
    {
        $this->taxRuleCalculator = $taxRuleCalculator;
    }

    public function supports(TaxRuleInterface $rule): bool
    {
        return $rule instanceof PercentageTaxRule;
    }

    public function calculateTaxFromGrossPrice(float $gross, TaxRuleInterface $rule): CalculatedTax
    {
        /* @var PercentageTaxRule $rule */
        return $this->taxRuleCalculator->calculateTaxFromGrossPrice(
            $gross / 100 * $rule->getPercentage(),
            new TaxRule($rule->getRate())
        );
    }

    public function calculateTaxFromNetPrice(float $net, TaxRuleInterface $rule): CalculatedTax
    {
        /* @var PercentageTaxRule $rule */
        return $this->taxRuleCalculator->calculateTaxFromNetPrice(
            $net / 100 * $rule->getPercentage(),
            new TaxRule($rule->getRate())
        );
    }
}
