<?php declare(strict_types=1);

namespace Shopware\Api\Listing\Struct;

use Shopware\Api\Entity\Entity;
use Shopware\Api\Entity\Search\Sorting\FieldSorting;

class ListingSortingBasicStruct extends Entity
{
    /**
     * @var FieldSorting[]
     */
    protected $payload;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var string
     */
    protected $uniqueKey;

    /**
     * @var bool
     */
    protected $displayInCategories;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var \DateTime|null
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     */
    protected $updatedAt;

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getDisplayInCategories(): bool
    {
        return $this->displayInCategories;
    }

    public function setDisplayInCategories(bool $displayInCategories): void
    {
        $this->displayInCategories = $displayInCategories;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUniqueKey(): string
    {
        return $this->uniqueKey;
    }

    public function setUniqueKey(string $uniqueKey): void
    {
        $this->uniqueKey = $uniqueKey;
    }
}
