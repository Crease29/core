<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Test\Cart\LineItem\Group\Packager;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Cart\LineItem\Group\LineItemGroupPackagerInterface;
use Shopware\Core\Checkout\Cart\LineItem\Group\Packager\LineItemGroupUnitPriceGrossPackager;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Test\Cart\LineItem\Group\Helpers\Traits\LineItemTestFixtureBehaviour;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class LineItemGroupUnitPriceGrossPackagerTest extends TestCase
{
    use LineItemTestFixtureBehaviour;

    /**
     * @var LineItemGroupPackagerInterface
     */
    private $packager;

    /**
     * @var SalesChannelContext
     */
    private $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->packager = new LineItemGroupUnitPriceGrossPackager();

        $this->context = $this->getMockBuilder(SalesChannelContext::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * This test verifies that our key identifier is not touched without recognizing it.
     * Please keep in mind, if you change the identifier, there might still
     * be old keys in the SetGroup entities in the database of shops, that
     * try to execute a packager that does not exist anymore with this key.
     *
     * @test
     * @group lineitemgroup
     */
    public function testKey(): void
    {
        static::assertEquals('PRICE_UNIT_GROSS', $this->packager->getKey());
    }

    /**
     * This test verifies that we have finished building our
     * package, as soon as we have reached a gross unit price of 100.0.
     * This is archived with 2 items - but most important - only if taxes are considered.
     * Otherwise it would be 3 items.
     *
     * @test
     * @group lineitemgroup
     */
    public function testPackageDoneWhenSumReached(): void
    {
        $items = new LineItemCollection();
        $items->add($this->createProductItem(49, 19));
        $items->add($this->createProductItem(49, 19));
        $items->add($this->createProductItem(49, 19));
        $items->add($this->createProductItem(49, 19));
        $items->add($this->createProductItem(49, 19));
        $items->add($this->createProductItem(49, 19));
        $items->add($this->createProductItem(49, 19));

        /** @var LineItemCollection $packageItems */
        $packageItems = $this->packager->buildGroupPackage(100, $items, $this->context);

        // verify we have 2 items, then we have reached 100.0
        static::assertCount(2, $packageItems);
    }

    /**
     * This test verifies that our packager returns an empty result
     * if we dont have enough items to fill a group.
     *
     * @test
     * @group lineitemgroup
     */
    public function testResultEmptyIfNotEnoughItems(): void
    {
        $items = new LineItemCollection();
        $items->add($this->createProductItem(20.0, 19));
        $items->add($this->createProductItem(20.0, 19));
        $items->add($this->createProductItem(20.0, 19));
        $items->add($this->createProductItem(20.0, 19));

        /** @var LineItemCollection $packageItems */
        $packageItems = $this->packager->buildGroupPackage(100, $items, $this->context);

        // verify we have no results because min sum is not reached
        static::assertCount(0, $packageItems);
    }

    /**
     * This test verifies, that our packager does also work
     * with an empty list of items. We should also get an empty result list.
     *
     * @test
     * @group lineitemgroup
     */
    public function testNoItemsReturnsEmptyList(): void
    {
        $items = new LineItemCollection();

        /** @var LineItemCollection $packageItems */
        $packageItems = $this->packager->buildGroupPackage(100, $items, $this->context);

        static::assertCount(0, $packageItems);
    }

    /**
     * This test verifies, that our packager does also work
     * with an invalid negative count. In that case we want an empty result list.
     *
     * @test
     * @group lineitemgroup
     */
    public function testNegativeCountReturnsEmptyList(): void
    {
        $items = new LineItemCollection();

        /** @var LineItemCollection $packageItems */
        $packageItems = $this->packager->buildGroupPackage(-100, $items, $this->context);

        static::assertCount(0, $packageItems);
    }

    /**
     * This test verifies, that our packager does also work
     * with an invalid zero count. In that case we want an empty result list.
     *
     * @test
     * @group lineitemgroup
     */
    public function testZeroCountReturnsEmptyList(): void
    {
        $items = new LineItemCollection();

        /** @var LineItemCollection $packageItems */
        $packageItems = $this->packager->buildGroupPackage(0, $items, $this->context);

        static::assertCount(0, $packageItems);
    }

    /**
     * This test verifies, that our packager does ignore products
     * that have no calculated price.
     *
     * @test
     * @group lineitemgroup
     */
    public function testPriceNullIsIgnored(): void
    {
        $items = new LineItemCollection();

        $productNoPrice = $this->createProductItem(20.0, 19);
        $productNoPrice->setPrice(null);

        $items->add($productNoPrice);
        $items->add($this->createProductItem(20.0, 19));

        /** @var LineItemCollection $packageItems */
        $packageItems = $this->packager->buildGroupPackage(5, $items, $this->context);

        static::assertCount(1, $packageItems);
    }
}
