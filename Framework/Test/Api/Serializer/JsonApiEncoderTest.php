<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Test\Api\Serializer;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Media\Aggregate\MediaFolder\MediaFolderDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Content\Rule\RuleDefinition;
use Shopware\Core\Framework\Api\Exception\UnsupportedEncoderInputException;
use Shopware\Core\Framework\Api\Serializer\JsonApiEncoder;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\Test\Api\Serializer\fixtures\SerializationFixture;
use Shopware\Core\Framework\Test\Api\Serializer\fixtures\TestBasicStruct;
use Shopware\Core\Framework\Test\Api\Serializer\fixtures\TestBasicWithExtension;
use Shopware\Core\Framework\Test\Api\Serializer\fixtures\TestBasicWithToManyRelationships;
use Shopware\Core\Framework\Test\Api\Serializer\fixtures\TestBasicWithToOneRelationship;
use Shopware\Core\Framework\Test\Api\Serializer\fixtures\TestCollectionWithSelfReference;
use Shopware\Core\Framework\Test\Api\Serializer\fixtures\TestCollectionWithToOneRelationship;
use Shopware\Core\Framework\Test\Api\Serializer\fixtures\TestInternalFieldsAreFiltered;
use Shopware\Core\Framework\Test\Api\Serializer\fixtures\TestMainResourceShouldNotBeInIncluded;
use Shopware\Core\Framework\Test\DataAbstractionLayer\Field\DataAbstractionLayerFieldTestBehaviour;
use Shopware\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\AssociationExtension;
use Shopware\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\ExtendableDefinition;
use Shopware\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\ExtendedDefinition;
use Shopware\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\ScalarRuntimeExtension;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Shopware\Core\System\User\UserDefinition;

class JsonApiEncoderTest extends TestCase
{
    use KernelTestBehaviour;
    use DataAbstractionLayerFieldTestBehaviour;

    /**
     * @var JsonApiEncoder
     */
    private $encoder;

    protected function setUp(): void
    {
        $this->encoder = new JsonApiEncoder();
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

    /**
     * @dataProvider emptyInputProvider
     */
    public function testEncodeWithEmptyInput($input): void
    {
        $this->expectException(UnsupportedEncoderInputException::class);

        $this->encoder->encode($this->getContainer()->get(ProductDefinition::class), $input, SerializationFixture::API_BASE_URL);
    }

    public function complexStructsProvider(): array
    {
        return [
            [$this->getContainer()->get(MediaDefinition::class), new TestBasicStruct()],
            [$this->getContainer()->get(UserDefinition::class), new TestBasicWithToManyRelationships()],
            [$this->getContainer()->get(MediaDefinition::class), new TestBasicWithToOneRelationship()],
            [$this->getContainer()->get(MediaFolderDefinition::class), new TestCollectionWithSelfReference()],
            [$this->getContainer()->get(MediaDefinition::class), new TestCollectionWithToOneRelationship()],
            [$this->getContainer()->get(RuleDefinition::class), new TestInternalFieldsAreFiltered()],
            [$this->getContainer()->get(UserDefinition::class), new TestMainResourceShouldNotBeInIncluded()],
        ];
    }

    /**
     * @dataProvider complexStructsProvider
     */
    public function testEncodeComplexStructs(EntityDefinition $definition, SerializationFixture $fixture): void
    {
        $actual = $this->encoder->encode($definition, $fixture->getInput(), SerializationFixture::API_BASE_URL);

        static::assertEquals($fixture->getAdminJsonApiFixtures(), json_decode($actual, true));
    }

    /**
     * Not possible with dataprovider
     * as we have to manipulate the container, but the dataprovider run before all tests
     */
    public function testEncodeStructWithExtension(): void
    {
        $this->registerDefinition(ExtendableDefinition::class, ExtendedDefinition::class);
        $extendableDefinition = new ExtendableDefinition();
        $extendableDefinition->addExtension(new AssociationExtension());
        $extendableDefinition->addExtension(new ScalarRuntimeExtension());

        $extendableDefinition->compile($this->getContainer()->get(DefinitionInstanceRegistry::class));
        $fixture = new TestBasicWithExtension();

        $actual = $this->encoder->encode($extendableDefinition, $fixture->getInput(), SerializationFixture::API_BASE_URL);

        static::assertEquals($fixture->getAdminJsonApiFixtures(), json_decode($actual, true));
    }
}
