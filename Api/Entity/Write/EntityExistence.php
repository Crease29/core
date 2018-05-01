<?php declare(strict_types=1);

namespace Shopware\Api\Entity\Write;

use Shopware\Api\Entity\EntityDefinition;

/**
 * Defines the current state of an entity in relation to the parent-child inheritance and
 * existence in the storage or command queue.
 */
class EntityExistence
{
    /**
     * @var array
     */
    protected $primaryKey;

    /**
     * @var bool
     */
    protected $exists;

    /**
     * @var string|EntityDefinition
     */
    protected $definition;

    /**
     * @var bool
     */
    protected $isChild;

    /**
     * @var bool
     */
    protected $wasChild;

    public function __construct(
        string $definition,
        array $primaryKey,
        bool $exists,
        bool $isChild,
        bool $wasChild
    ) {
        $this->primaryKey = $primaryKey;
        $this->exists = $exists;
        $this->definition = $definition;
        $this->isChild = $isChild;
        $this->wasChild = $wasChild;
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public function getPrimaryKey(): array
    {
        return $this->primaryKey;
    }

    public function isChild(): bool
    {
        return $this->isChild;
    }

    public function wasChild(): bool
    {
        return $this->wasChild;
    }

    public function getDefinition(): string
    {
        return $this->definition;
    }

    public function childChangedToParent(): bool
    {
        if (!$this->wasChild()) {
            return false;
        }

        return !$this->isChild();
    }
}
