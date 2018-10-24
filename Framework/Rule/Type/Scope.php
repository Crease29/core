<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Rule\Type;

use Shopware\Core\Framework\Struct\Struct;

class Scope extends Struct
{
    const IDENTIFIER_GLOBAL = 'global';
    const IDENTIFIER_CHECKOUT = 'CheckoutRuleScope';
    const IDENTIFIER_CART = 'CartRuleScope';
    const IDENTIFIER_LINEITEM = 'LineItemRuleScope';

    /** @var string */
    protected $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}