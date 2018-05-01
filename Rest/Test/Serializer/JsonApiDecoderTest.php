<?php declare(strict_types=1);

namespace Shopware\Rest\Test\Serializer;

use PHPUnit\Framework\TestCase;
use Shopware\Rest\Serializer\JsonApiDecoder;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class JsonApiDecoderTest extends TestCase
{
    /**
     * @var JsonApiDecoder
     */
    private $decoder;

    public function setUp()
    {
        $this->decoder = new JsonApiDecoder();
    }

    public function testSupportFormat(): void
    {
        $this->assertTrue($this->decoder->supportsDecoding('jsonapi'));
        $this->assertFalse($this->decoder->supportsDecoding('JSONAPI'));
        $this->assertFalse($this->decoder->supportsDecoding('yml'));
    }

    public function emptyInputProvider(): array
    {
        return [
            [null],
            ['string'],
            [1],
            [false],
            [new \DateTime()],
            [1.1],
        ];
    }

    public function inputWithoutDataOnRootProvider(): array
    {
        return [
            ['randomKey' => 'randomValue'],
            ['data' => 'foo'],
        ];
    }

    public function resourceIdentifierWIthInvalidStructureProvider(): array
    {
        return [
            [['data' => ['id' => 'some-id']]],
            [['data' => ['type' => 'some-type']]],
            [['data' => ['ids' => 'foo', 'types' => 'some-type']]],
            [['data' => [], 'included' => [['ids' => 'foo', 'types' => 'some-type']]]],
        ];
    }

    /**
     * @dataProvider emptyInputProvider
     *
     * @param mixed $input
     */
    public function testEncodeWithEmptyInput($input): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Input not a valid JSON:API data object.');

        $this->decoder->decode(json_encode($input), 'jsonapi');
    }

    /**
     * @param mixed $input
     * @dataProvider inputWithoutDataOnRootProvider
     */
    public function testInputWithoutDataOnRoot($input): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Input not a valid JSON:API data object.');

        $this->decoder->decode(json_encode($input), 'jsonapi');
    }

    /**
     * @param mixed $input
     * @dataProvider resourceIdentifierWIthInvalidStructureProvider
     */
    public function testResourceIdentifierWithInvalidStructure($input): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('A resource identifier must be an array containing "id" and "type".');

        $this->decoder->decode(json_encode($input), 'jsonapi');
    }

    public function testRelationshipWithoutMatchingInclude(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Resolving relationship "some-type(some-id)" failed due to non-existence.');

        $json = [
            'data' => [
                'id' => 1,
                'type' => 'bar',
                'relationships' => [
                    'someKey' => [
                        'data' => ['type' => 'some-type', 'id' => 'some-id'],
                    ],
                ],
            ],
            'included' => [],
        ];

        $this->decoder->decode(json_encode($json), 'jsonapi');
    }

    public function testRelationshipsWithMalformatData(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Relationships of a resource must be an array of relationship links.');

        $json = [
            'data' => [
                'id' => 1,
                'type' => 'bar',
                'relationships' => 'totally wrong',
            ],
            'included' => [],
        ];

        $this->decoder->decode(json_encode($json), 'jsonapi');
    }

    public function testRelationshipKeysMustNotBeNumeric(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Relationships of a resource must have a valid property name.');

        $json = [
            'data' => [
                'id' => 1,
                'type' => 'bar',
                'relationships' => [
                    ['data' => ['type' => 'some-type', 'id' => 'some-id']],
                ],
            ],
            'included' => [],
        ];

        $this->decoder->decode(json_encode($json), 'jsonapi');
    }

    public function testRelationshipBaseStructureType(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('A relationship link must be an array and contain the "data" property with a single or multiple resource identifiers.');

        $json = [
            'data' => [
                'id' => 1,
                'type' => 'bar',
                'relationships' => [
                    'foo' => 'bar',
                ],
            ],
            'included' => [],
        ];

        $this->decoder->decode(json_encode($json), 'jsonapi');
    }

    public function testRelationshipBaseStructureData(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('A relationship link must be an array and contain the "data" property with a single or multiple resource identifiers.');

        $json = [
            'data' => [
                'id' => 1,
                'type' => 'bar',
                'relationships' => [
                    'foo' => ['property' => 'some-value'],
                ],
            ],
            'included' => [],
        ];

        $this->decoder->decode(json_encode($json), 'jsonapi');
    }

    public function testAttributesMustBeAnArray(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('The attributes of a resource must be an array.');

        $json = [
            'data' => [
                'id' => 1,
                'type' => 'bar',
                'attributes' => 'foo',
            ],
        ];

        $this->decoder->decode(json_encode($json), 'jsonapi');
    }

    public function testDecodeStructWithoutRelationships(): void
    {
        $expected = [
            'uuid' => 'ALBUM-122',
            'parentUuid' => null,
            'position' => 12,
            'name' => 'Manufacturer',
        ];

        $json = [
            'data' => [
                'id' => 'ALBUM-122',
                'type' => 'media_album',
                'attributes' => [
                    'parentUuid' => null,
                    'position' => 12,
                    'name' => 'Manufacturer',
                ],
            ],
        ];

        $this->assertEquals($expected, $this->decoder->decode(json_encode($json), 'jsonapi'));
    }

    public function testDecodeStructWithRelationships(): void
    {
        $expected = [
            'uuid' => 'ALBUM-122',
            'parentUuid' => null,
            'position' => 12,
            'name' => 'Manufacturer',
            'media' => [
                'uuid' => 'MEDIA-7',
                'albumUuid' => 'ALBUM-50',
                'fileName' => 'teaser5040640f2861b.jpg',
                'mimeType' => 'image/jpg',
                'fileSize' => 93889,
                'metaData' => null,
                'userUuid' => null,
                'createdAt' => '2012-08-31T00:00:00+00:00',
                'updatedAt' => '2017-11-21T11:25:34+00:00',
                'name' => '2',
                'description' => '',
            ],
        ];

        $json = [
            'data' => [
                'id' => 'ALBUM-122',
                'type' => 'media_album',
                'attributes' => [
                    'parentUuid' => null,
                    'position' => 12,
                    'name' => 'Manufacturer',
                ],
                'relationships' => [
                    'media' => [
                        'data' => [
                            'id' => 'MEDIA-7',
                            'type' => 'media',
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'id' => 'MEDIA-7',
                    'type' => 'media',
                    'attributes' => [
                        'albumUuid' => 'ALBUM-50',
                        'fileName' => 'teaser5040640f2861b.jpg',
                        'mimeType' => 'image/jpg',
                        'fileSize' => 93889,
                        'metaData' => null,
                        'userUuid' => null,
                        'createdAt' => '2012-08-31T00:00:00+00:00',
                        'updatedAt' => '2017-11-21T11:25:34+00:00',
                        'name' => '2',
                        'description' => '',
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->decoder->decode(json_encode($json), 'jsonapi'));
    }

    public function testDecodeStructWithToManyRelationships(): void
    {
        $expected = [
            'media' => [
                [
                    'uuid' => 'MEDIA-7',
                    'albumUuid' => 'ALBUM-50',
                    'fileName' => 'teaser5040640f2861b.jpg',
                    'mimeType' => 'image/jpg',
                    'fileSize' => 93889,
                    'metaData' => null,
                    'userUuid' => null,
                    'createdAt' => '2012-08-31T00:00:00+00:00',
                    'updatedAt' => '2017-11-21T11:25:34+00:00',
                    'name' => '2',
                    'description' => '',
                    'album' => [
                        'uuid' => 'ALBUM-50',
                        'parentUuid' => 'ALBUM-2',
                        'position' => 3,
                        'createThumbnails' => false,
                        'thumbnailSize' => '',
                        'icon' => 'sprite-blue-folder',
                        'thumbnailHighDpi' => false,
                        'thumbnailQuality' => 90,
                        'thumbnailHighDpiQuality' => 60,
                        'createdAt' => '2017-11-21T11:25:46+00:00',
                        'updatedAt' => null,
                        'name' => 'Sonstiges',
                        'attributes' => [],
                    ],
                ],
            ],
            'uuid' => 'ALBUM-122',
            'parentUuid' => null,
            'position' => 12,
            'name' => 'Manufacturer',
        ];

        $json = [
            'data' => [
                'id' => 'ALBUM-122',
                'type' => 'media_album',
                'attributes' => [
                    'parentUuid' => null,
                    'position' => 12,
                    'name' => 'Manufacturer',
                ],
                'relationships' => [
                    'media' => [
                        'data' => [
                            ['id' => 'MEDIA-7', 'type' => 'media'],
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'id' => 'MEDIA-7',
                    'type' => 'media',
                    'attributes' => [
                        'albumUuid' => 'ALBUM-50',
                        'fileName' => 'teaser5040640f2861b.jpg',
                        'mimeType' => 'image/jpg',
                        'fileSize' => 93889,
                        'metaData' => null,
                        'userUuid' => null,
                        'createdAt' => '2012-08-31T00:00:00+00:00',
                        'updatedAt' => '2017-11-21T11:25:34+00:00',
                        'name' => '2',
                        'description' => '',
                    ],
                    'relationships' => [
                        'album' => [
                            'data' => [
                                'id' => 'ALBUM-50',
                                'type' => 'media_album',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 'ALBUM-50',
                    'type' => 'media_album',
                    'attributes' => [
                        'parentUuid' => 'ALBUM-2',
                        'position' => 3,
                        'createThumbnails' => false,
                        'thumbnailSize' => '',
                        'icon' => 'sprite-blue-folder',
                        'thumbnailHighDpi' => false,
                        'thumbnailQuality' => 90,
                        'thumbnailHighDpiQuality' => 60,
                        'createdAt' => '2017-11-21T11:25:46+00:00',
                        'updatedAt' => null,
                        'name' => 'Sonstiges',
                        'attributes' => [],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->decoder->decode(json_encode($json), 'jsonapi'));
    }

    public function testDecodeCollectionOfIncludedRelationships(): void
    {
        $expected = [
            'media' => [
                [
                    'uuid' => 'MEDIA-7',
                    'albumUuid' => 'ALBUM-50',
                    'fileName' => 'teaser5040640f2861b.jpg',
                    'mimeType' => 'image/jpg',
                    'fileSize' => 93889,
                    'metaData' => null,
                    'userUuid' => null,
                    'createdAt' => '2012-08-31T00:00:00+00:00',
                    'updatedAt' => '2017-11-21T11:25:34+00:00',
                    'name' => '2',
                    'description' => '',
                    'album' => [
                        [
                            'uuid' => 'ALBUM-50',
                            'parentUuid' => 'ALBUM-2',
                            'position' => 3,
                            'createThumbnails' => false,
                            'thumbnailSize' => '',
                            'icon' => 'sprite-blue-folder',
                            'thumbnailHighDpi' => false,
                            'thumbnailQuality' => 90,
                            'thumbnailHighDpiQuality' => 60,
                            'createdAt' => '2017-11-21T11:25:46+00:00',
                            'updatedAt' => null,
                            'name' => 'Sonstiges',
                            'attributes' => [],
                        ],
                        [
                            'uuid' => 'ALBUM-100',
                            'parentUuid' => 'ALBUM-2',
                            'position' => 3,
                            'createThumbnails' => false,
                            'thumbnailSize' => '',
                            'icon' => 'sprite-blue-folder',
                            'thumbnailHighDpi' => false,
                            'thumbnailQuality' => 90,
                            'thumbnailHighDpiQuality' => 60,
                            'createdAt' => '2017-11-21T11:25:46+00:00',
                            'updatedAt' => null,
                            'name' => 'Sonstiges',
                            'attributes' => [],
                        ],
                    ],
                ],
            ],
            'uuid' => 'ALBUM-122',
            'parentUuid' => null,
            'position' => 12,
            'name' => 'Manufacturer',
        ];

        $json = [
            'data' => [
                'id' => 'ALBUM-122',
                'type' => 'media_album',
                'attributes' => [
                    'parentUuid' => null,
                    'position' => 12,
                    'name' => 'Manufacturer',
                ],
                'relationships' => [
                    'media' => [
                        'data' => [
                            ['id' => 'MEDIA-7', 'type' => 'media'],
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'id' => 'MEDIA-7',
                    'type' => 'media',
                    'attributes' => [
                        'albumUuid' => 'ALBUM-50',
                        'fileName' => 'teaser5040640f2861b.jpg',
                        'mimeType' => 'image/jpg',
                        'fileSize' => 93889,
                        'metaData' => null,
                        'userUuid' => null,
                        'createdAt' => '2012-08-31T00:00:00+00:00',
                        'updatedAt' => '2017-11-21T11:25:34+00:00',
                        'name' => '2',
                        'description' => '',
                    ],
                    'relationships' => [
                        'album' => [
                            'data' => [
                                ['id' => 'ALBUM-50', 'type' => 'media_album'],
                                ['id' => 'ALBUM-100', 'type' => 'media_album'],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 'ALBUM-50',
                    'type' => 'media_album',
                    'attributes' => [
                        'parentUuid' => 'ALBUM-2',
                        'position' => 3,
                        'createThumbnails' => false,
                        'thumbnailSize' => '',
                        'icon' => 'sprite-blue-folder',
                        'thumbnailHighDpi' => false,
                        'thumbnailQuality' => 90,
                        'thumbnailHighDpiQuality' => 60,
                        'createdAt' => '2017-11-21T11:25:46+00:00',
                        'updatedAt' => null,
                        'name' => 'Sonstiges',
                        'attributes' => [],
                    ],
                ],
                [
                    'id' => 'ALBUM-100',
                    'type' => 'media_album',
                    'attributes' => [
                        'parentUuid' => 'ALBUM-2',
                        'position' => 3,
                        'createThumbnails' => false,
                        'thumbnailSize' => '',
                        'icon' => 'sprite-blue-folder',
                        'thumbnailHighDpi' => false,
                        'thumbnailQuality' => 90,
                        'thumbnailHighDpiQuality' => 60,
                        'createdAt' => '2017-11-21T11:25:46+00:00',
                        'updatedAt' => null,
                        'name' => 'Sonstiges',
                        'attributes' => [],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->decoder->decode(json_encode($json), 'jsonapi'));
    }

    public function testDecodeCollection(): void
    {
        $expected = [
            [
                'media' => [
                    [
                        'uuid' => 'MEDIA-7',
                        'albumUuid' => 'ALBUM-50',
                        'fileName' => 'teaser5040640f2861b.jpg',
                        'mimeType' => 'image/jpg',
                        'fileSize' => 93889,
                        'metaData' => null,
                        'userUuid' => null,
                        'createdAt' => '2012-08-31T00:00:00+00:00',
                        'updatedAt' => '2017-11-21T11:25:34+00:00',
                        'name' => '2',
                        'description' => '',
                        'album' => [
                            'uuid' => 'ALBUM-50',
                            'parentUuid' => 'ALBUM-2',
                            'position' => 3,
                            'createThumbnails' => false,
                            'thumbnailSize' => '',
                            'icon' => 'sprite-blue-folder',
                            'thumbnailHighDpi' => false,
                            'thumbnailQuality' => 90,
                            'thumbnailHighDpiQuality' => 60,
                            'createdAt' => '2017-11-21T11:25:46+00:00',
                            'updatedAt' => null,
                            'name' => 'Sonstiges',
                            'attributes' => [],
                        ],
                    ],
                ],
                'uuid' => 'ALBUM-122',
                'parentUuid' => null,
                'position' => 12,
                'name' => 'Manufacturer',
            ],
            [
                'media' => [
                    [
                        'uuid' => 'MEDIA-7',
                        'albumUuid' => 'ALBUM-50',
                        'fileName' => 'teaser5040640f2861b.jpg',
                        'mimeType' => 'image/jpg',
                        'fileSize' => 93889,
                        'metaData' => null,
                        'userUuid' => null,
                        'createdAt' => '2012-08-31T00:00:00+00:00',
                        'updatedAt' => '2017-11-21T11:25:34+00:00',
                        'name' => '2',
                        'description' => '',
                        'album' => [
                            'uuid' => 'ALBUM-50',
                            'parentUuid' => 'ALBUM-2',
                            'position' => 3,
                            'createThumbnails' => false,
                            'thumbnailSize' => '',
                            'icon' => 'sprite-blue-folder',
                            'thumbnailHighDpi' => false,
                            'thumbnailQuality' => 90,
                            'thumbnailHighDpiQuality' => 60,
                            'createdAt' => '2017-11-21T11:25:46+00:00',
                            'updatedAt' => null,
                            'name' => 'Sonstiges',
                            'attributes' => [],
                        ],
                    ],
                ],
                'uuid' => 'ALBUM-123',
                'parentUuid' => null,
                'position' => 13,
                'name' => 'Manufacturer',
            ],
        ];

        $json = [
            'data' => [
                [
                    'id' => 'ALBUM-122',
                    'type' => 'media_album',
                    'attributes' => [
                        'parentUuid' => null,
                        'position' => 12,
                        'name' => 'Manufacturer',
                    ],
                    'relationships' => [
                        'media' => [
                            'data' => [
                                ['id' => 'MEDIA-7', 'type' => 'media'],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 'ALBUM-123',
                    'type' => 'media_album',
                    'attributes' => [
                        'parentUuid' => null,
                        'position' => 13,
                        'name' => 'Manufacturer',
                    ],
                    'relationships' => [
                        'media' => [
                            'data' => [
                                ['id' => 'MEDIA-7', 'type' => 'media'],
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'id' => 'MEDIA-7',
                    'type' => 'media',
                    'attributes' => [
                        'albumUuid' => 'ALBUM-50',
                        'fileName' => 'teaser5040640f2861b.jpg',
                        'mimeType' => 'image/jpg',
                        'fileSize' => 93889,
                        'metaData' => null,
                        'userUuid' => null,
                        'createdAt' => '2012-08-31T00:00:00+00:00',
                        'updatedAt' => '2017-11-21T11:25:34+00:00',
                        'name' => '2',
                        'description' => '',
                    ],
                    'relationships' => [
                        'album' => [
                            'data' => [
                                'id' => 'ALBUM-50',
                                'type' => 'media_album',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 'ALBUM-50',
                    'type' => 'media_album',
                    'attributes' => [
                        'parentUuid' => 'ALBUM-2',
                        'position' => 3,
                        'createThumbnails' => false,
                        'thumbnailSize' => '',
                        'icon' => 'sprite-blue-folder',
                        'thumbnailHighDpi' => false,
                        'thumbnailQuality' => 90,
                        'thumbnailHighDpiQuality' => 60,
                        'createdAt' => '2017-11-21T11:25:46+00:00',
                        'updatedAt' => null,
                        'name' => 'Sonstiges',
                        'attributes' => [],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->decoder->decode(json_encode($json), 'jsonapi'));
    }
}
