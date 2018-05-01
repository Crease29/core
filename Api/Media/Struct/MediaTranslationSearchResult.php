<?php declare(strict_types=1);

namespace Shopware\Api\Media\Struct;

use Shopware\Api\Entity\Search\SearchResultInterface;
use Shopware\Api\Entity\Search\SearchResultTrait;
use Shopware\Api\Media\Collection\MediaTranslationBasicCollection;

class MediaTranslationSearchResult extends MediaTranslationBasicCollection implements SearchResultInterface
{
    use SearchResultTrait;
}
