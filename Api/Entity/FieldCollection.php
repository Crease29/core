<?php declare(strict_types=1);

namespace Shopware\Api\Entity;

use Shopware\Api\Entity\Field\AssociationInterface;
use Shopware\Api\Entity\Field\Field;
use Shopware\Api\Entity\Write\FieldAware\StorageAware;
use Shopware\Api\Entity\Write\Flag\ReadOnly;
use Shopware\Api\Entity\Write\Flag\WriteOnly;
use Shopware\Framework\Struct\Collection;

class FieldCollection extends Collection
{
    /**
     * @var string[]
     */
    public $mapping = [];
    /**
     * @var Field[]
     */
    protected $elements = [];

    public function __construct(array $elements = [])
    {
        foreach ($elements as $field) {
            $this->add($field);
        }
    }

    public function add(Field $field)
    {
        $this->elements[$field->getPropertyName()] = $field;
        if ($field instanceof StorageAware && !$field instanceof AssociationInterface) {
            $this->mapping[$field->getStorageName()] = $field->getPropertyName();
        }
    }

    public function get(string $propertyName): ?Field
    {
        return $this->elements[$propertyName] ?? null;
    }

    public function getBasicProperties(): self
    {
        return $this->filter(
            function (Field $field) {
                if ($field instanceof AssociationInterface) {
                    return $field->loadInBasic() && !$field->is(WriteOnly::class);
                }

                return true;
            }
        );
    }

    public function getDetailProperties(): self
    {
        return $this->filter(
            function (Field $field) {
                if ($field instanceof AssociationInterface) {
                    return !$field->is(WriteOnly::class);
                }

                return true;
            }
        );
    }

    public function getWritableFields(): self
    {
        return $this->filter(function (Field $field) {
            return !$field->is(ReadOnly::class);
        });
    }

    public function current(): Field
    {
        return parent::current();
    }

    public function getByStorageName(string $storageName): ?Field
    {
        if (!array_key_exists($storageName, $this->mapping)) {
            return null;
        }

        return $this->get($this->mapping[$storageName]);
    }

    public function filterByFlag(string $flagClass): self
    {
        return $this->filter(function (Field $field) use ($flagClass) {
            return $field->is($flagClass);
        });
    }
}
