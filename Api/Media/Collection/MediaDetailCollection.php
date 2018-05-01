<?php declare(strict_types=1);

namespace Shopware\Api\Media\Collection;

use Shopware\Api\Media\Struct\MediaDetailStruct;
use Shopware\Api\User\Collection\UserBasicCollection;

class MediaDetailCollection extends MediaBasicCollection
{
    /**
     * @var MediaDetailStruct[]
     */
    protected $elements = [];

    public function getUsers(): UserBasicCollection
    {
        return new UserBasicCollection(
            $this->fmap(function (MediaDetailStruct $media) {
                return $media->getUser();
            })
        );
    }

    public function getTranslationIds(): array
    {
        $ids = [];
        foreach ($this->elements as $element) {
            foreach ($element->getTranslations()->getIds() as $id) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    public function getTranslations(): MediaTranslationBasicCollection
    {
        $collection = new MediaTranslationBasicCollection();
        foreach ($this->elements as $element) {
            $collection->fill($element->getTranslations()->getElements());
        }

        return $collection;
    }

    protected function getExpectedClass(): string
    {
        return MediaDetailStruct::class;
    }
}
