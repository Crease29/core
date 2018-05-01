<?php declare(strict_types=1);

namespace Shopware\Cart\Cart;

use Shopware\Cart\Cart\Struct\Cart;
use Shopware\Context\Struct\StorefrontContext;
use Shopware\Framework\Struct\StructCollection;

class CartCollector
{
    /**
     * @var CartCollectorInterface[]
     */
    private $collectors;

    public function __construct(iterable $collectors)
    {
        $this->collectors = $collectors;
    }

    public function collect(Cart $cart, StorefrontContext $context): StructCollection
    {
        $fetchCollection = new StructCollection();
        foreach ($this->collectors as $collector) {
            $collector->prepare($fetchCollection, $cart, $context);
        }

        $dataCollection = new StructCollection();
        foreach ($this->collectors as $collector) {
            $collector->fetch($dataCollection, $fetchCollection, $context);
        }

        return $dataCollection;
    }
}
