<?php declare(strict_types=1);
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

namespace Shopware\Cart\Test\Tax;

use PHPUnit\Framework\TestCase;
use Shopware\Cart\Tax\Struct\CalculatedTax;
use Shopware\Cart\Tax\Struct\CalculatedTaxCollection;

class CalculatedTaxCollectionTest extends TestCase
{
    public const DUMMY_TAX_NAME = 'dummy-tax';

    public function testCollectionIsCountable(): void
    {
        $collection = new CalculatedTaxCollection();
        static::assertCount(0, $collection);
    }

    public function testCountReturnsCorrectValue(): void
    {
        $collection = new CalculatedTaxCollection([
            new CalculatedTax(10.99, 19, 1),
            new CalculatedTax(5.99, 14, 1),
            new CalculatedTax(1.99, 2, 1),
        ]);
        static::assertCount(3, $collection);
    }

    public function testAddFunctionAddsATax(): void
    {
        $collection = new CalculatedTaxCollection();
        $collection->add(
            new CalculatedTax(10.99, 19, 1)
        );

        static::assertEquals(
            new CalculatedTaxCollection([
                new CalculatedTax(10.99, 19, 1),
            ]),
            $collection
        );
    }

    public function testFillFunctionFillsTheCollection(): void
    {
        $collection = new CalculatedTaxCollection();
        $collection->fill([
            new CalculatedTax(5.50, 19, 1),
            new CalculatedTax(4.40, 18, 1),
            new CalculatedTax(3.30, 17, 1),
        ]);

        static::assertEquals(
            new CalculatedTaxCollection([
                new CalculatedTax(5.50, 19, 1),
                new CalculatedTax(4.40, 18, 1),
                new CalculatedTax(3.30, 17, 1),
            ]),
            $collection
        );
    }

    public function testTaxesCanBeGetterByTheirRate(): void
    {
        $collection = new CalculatedTaxCollection([
            new CalculatedTax(5.50, 19, 1),
            new CalculatedTax(4.40, 18, 1),
            new CalculatedTax(3.30, 17, 1),
        ]);
        static::assertEquals(
            new CalculatedTax(5.50, 19, 1),
            $collection->get(19)
        );
    }

    public function testTaxAmountCanBeSummed(): void
    {
        $collection = new CalculatedTaxCollection([
            new CalculatedTax(5.50, 19, 1),
            new CalculatedTax(4.40, 18, 1),
            new CalculatedTax(3.30, 17, 1),
        ]);
        static::assertSame(13.2, $collection->getAmount());
    }

    public function testIncrementFunctionAddsNewCalculatedTaxIfNotExist(): void
    {
        $collection = new CalculatedTaxCollection([
            new CalculatedTax(5.50, 19, 1),
        ]);

        $collection = $collection->merge(
            new CalculatedTaxCollection([new CalculatedTax(5.50, 18, 1)])
        );

        static::assertEquals(
            new CalculatedTaxCollection([
                new CalculatedTax(5.50, 19, 1),
                new CalculatedTax(5.50, 18, 1),
            ]),
            $collection
        );
    }

    public function testIncrementFunctionIncrementsExistingTaxes(): void
    {
        $collection = new CalculatedTaxCollection([
            new CalculatedTax(5.50, 19, 1),
        ]);
        $collection = $collection->merge(new CalculatedTaxCollection([
            new CalculatedTax(5.50, 19, 1),
        ]));

        static::assertEquals(
            new CalculatedTaxCollection([
                new CalculatedTax(11.00, 19, 2),
            ]),
            $collection
        );
    }

    public function testIncrementFunctionIncrementExistingTaxAmounts(): void
    {
        $collection = new CalculatedTaxCollection([
            new CalculatedTax(5.50, 19, 1),
            new CalculatedTax(5.50, 18, 1),
            new CalculatedTax(5.50, 17, 1),
        ]);

        $collection = $collection->merge(new CalculatedTaxCollection([
            new CalculatedTax(5.50, 19, 1),
            new CalculatedTax(5.50, 18, 1),
            new CalculatedTax(5.50, 17, 1),
        ]));

        static::assertEquals(
            new CalculatedTaxCollection([
                new CalculatedTax(11.00, 19, 2),
                new CalculatedTax(11.00, 18, 2),
                new CalculatedTax(11.00, 17, 2),
            ]),
            $collection
        );
    }

    public function testIncrementFunctionWorksWithEmptyCollection(): void
    {
        $collection = new CalculatedTaxCollection([
            new CalculatedTax(5.50, 19, 1),
            new CalculatedTax(5.50, 18, 1),
            new CalculatedTax(5.50, 17, 1),
        ]);
        $collection = $collection->merge(new CalculatedTaxCollection());

        static::assertEquals(
            new CalculatedTaxCollection([
                new CalculatedTax(5.50, 19, 1),
                new CalculatedTax(5.50, 18, 1),
                new CalculatedTax(5.50, 17, 1),
            ]),
            $collection
        );
    }

    public function testFillFunctionsFillsTheCollection(): void
    {
        $collection = new CalculatedTaxCollection();
        $collection->fill([
            new CalculatedTax(5.50, 19, 1),
            new CalculatedTax(5.50, 18, 1),
            new CalculatedTax(5.50, 17, 1),
        ]);

        static::assertEquals(new CalculatedTaxCollection([
            new CalculatedTax(5.50, 19, 1),
            new CalculatedTax(5.50, 18, 1),
            new CalculatedTax(5.50, 17, 1),
        ]), $collection);
    }

    public function testTaxesCanBeRemovedByRate(): void
    {
        $collection = new CalculatedTaxCollection([
            new CalculatedTax(5.50, 19, 1),
            new CalculatedTax(5.50, 18, 1),
            new CalculatedTax(5.50, 17, 1),
        ]);
        $collection->remove(19);

        static::assertEquals(new CalculatedTaxCollection([
            new CalculatedTax(5.50, 18, 1),
            new CalculatedTax(5.50, 17, 1),
        ]), $collection);
    }

    public function testClearFunctionRemovesAllTaxes(): void
    {
        $collection = new CalculatedTaxCollection([
            new CalculatedTax(5.50, 19, 1),
            new CalculatedTax(5.50, 18, 1),
            new CalculatedTax(5.50, 17, 1),
        ]);

        $collection->clear();
        static::assertEquals(new CalculatedTaxCollection(), $collection);
    }

    public function testGetOnEmptyCollection(): void
    {
        $collection = new CalculatedTaxCollection();
        static::assertNull($collection->get(19));
    }

    public function testRemoveElement(): void
    {
        $toRemove = new CalculatedTax(5.50, 18, 1);
        $collection = new CalculatedTaxCollection([
            new CalculatedTax(5.50, 19, 1),
            $toRemove,
            new CalculatedTax(5.50, 17, 1),
        ]);

        $collection->removeElement($toRemove);

        $this->assertEquals(
            new CalculatedTaxCollection([
                new CalculatedTax(5.50, 19, 1),
                new CalculatedTax(5.50, 17, 1),
            ]),
            $collection
        );
    }
}
