<?php declare(strict_types=1);

namespace Shopware\Api\Entity\Search\Query;

class RangeQuery extends Query
{
    public const LTE = 'lte';

    public const LT = 'lt';

    public const GTE = 'gte';

    public const GT = 'gt';

    /**
     * @var string
     */
    protected $field;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @example
     *
     * new RangeQuery('price', [
     *      'gte' => 5.99,
     *      'lte' => 21.99
     * ])
     *
     * new RangeQuery('price', [
     *      'gt' => 5.99
     * ])
     *
     * @param string $field
     * @param array  $parameters
     */
    public function __construct(string $field, array $parameters = [])
    {
        $this->field = $field;
        $this->parameters = $parameters;
    }

    public function hasParameter(string $key)
    {
        return array_key_exists($key, $this->parameters);
    }

    public function getParameter(string $key)
    {
        if (!$this->hasParameter($key)) {
            return null;
        }

        return $this->parameters[$key];
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getFields(): array
    {
        return [$this->field];
    }
}
