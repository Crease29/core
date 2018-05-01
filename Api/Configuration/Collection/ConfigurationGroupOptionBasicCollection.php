<?php declare(strict_types=1);

namespace Shopware\Api\Configuration\Collection;

use Shopware\Api\Configuration\Struct\ConfigurationGroupDetailStruct;
use Shopware\Api\Configuration\Struct\ConfigurationGroupOptionBasicStruct;
use Shopware\Api\Entity\EntityCollection;

class ConfigurationGroupOptionBasicCollection extends EntityCollection
{
    /**
     * @var ConfigurationGroupOptionBasicStruct[]
     */
    protected $elements = [];

    public function get(string $id): ? ConfigurationGroupOptionBasicStruct
    {
        return parent::get($id);
    }

    public function current(): ConfigurationGroupOptionBasicStruct
    {
        return parent::current();
    }

    public function getConfigurationGroupIds(): array
    {
        return $this->fmap(function (ConfigurationGroupOptionBasicStruct $configurationGroupOption) {
            return $configurationGroupOption->getGroupId();
        });
    }

    public function filterByGroupId(string $id): self
    {
        return $this->filter(function (ConfigurationGroupOptionBasicStruct $configurationGroupOption) use ($id) {
            return $configurationGroupOption->getGroupId() === $id;
        });
    }

    public function getMediaIds(): array
    {
        return $this->fmap(function (ConfigurationGroupOptionBasicStruct $configurationGroupOption) {
            return $configurationGroupOption->getMediaId();
        });
    }

    public function filterByMediaId(string $id): self
    {
        return $this->filter(function (ConfigurationGroupOptionBasicStruct $configurationGroupOption) use ($id) {
            return $configurationGroupOption->getMediaId() === $id;
        });
    }

    public function getGroups(): ConfigurationGroupBasicCollection
    {
        return new ConfigurationGroupBasicCollection(
            $this->fmap(function (ConfigurationGroupOptionBasicStruct $configurationGroupOption) {
                return $configurationGroupOption->getGroup();
            })
        );
    }

    public function groupByConfigurationGroups(): ConfigurationGroupDetailCollection
    {
        $groups = new ConfigurationGroupDetailCollection();
        foreach ($this->elements as $element) {
            if ($groups->has($element->getGroupId())) {
                $group = $groups->get($element->getGroupId());
            } else {
                $group = ConfigurationGroupDetailStruct::createFrom($element->getGroup());
                $groups->add($group);
            }

            $group->getOptions()->add($element);
        }

        return $groups;
    }

    protected function getExpectedClass(): string
    {
        return ConfigurationGroupOptionBasicStruct::class;
    }
}
