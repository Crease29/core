<?php declare(strict_types=1);

namespace Shopware\Api\Media\Collection;

use Shopware\Api\Entity\EntityCollection;
use Shopware\Api\Media\Struct\MediaAlbumTranslationBasicStruct;

class MediaAlbumTranslationBasicCollection extends EntityCollection
{
    /**
     * @var MediaAlbumTranslationBasicStruct[]
     */
    protected $elements = [];

    public function get(string $id): ? MediaAlbumTranslationBasicStruct
    {
        return parent::get($id);
    }

    public function current(): MediaAlbumTranslationBasicStruct
    {
        return parent::current();
    }

    public function getMediaAlbumIds(): array
    {
        return $this->fmap(function (MediaAlbumTranslationBasicStruct $mediaAlbumTranslation) {
            return $mediaAlbumTranslation->getMediaAlbumId();
        });
    }

    public function filterByMediaAlbumId(string $id): self
    {
        return $this->filter(function (MediaAlbumTranslationBasicStruct $mediaAlbumTranslation) use ($id) {
            return $mediaAlbumTranslation->getMediaAlbumId() === $id;
        });
    }

    public function getLanguageIds(): array
    {
        return $this->fmap(function (MediaAlbumTranslationBasicStruct $mediaAlbumTranslation) {
            return $mediaAlbumTranslation->getLanguageId();
        });
    }

    public function filterByLanguageId(string $id): self
    {
        return $this->filter(function (MediaAlbumTranslationBasicStruct $mediaAlbumTranslation) use ($id) {
            return $mediaAlbumTranslation->getLanguageId() === $id;
        });
    }

    protected function getExpectedClass(): string
    {
        return MediaAlbumTranslationBasicStruct::class;
    }
}
