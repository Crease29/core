<?php declare(strict_types=1);

namespace Shopware\Rest\Serializer;

use Shopware\Api\Entity\EntityDefinition;
use Shopware\Api\Entity\Field\Field;
use Shopware\Api\Entity\Field\FkField;
use Shopware\Api\Entity\Field\ManyToManyAssociationField;
use Shopware\Api\Entity\Field\ManyToOneAssociationField;
use Shopware\Api\Entity\Field\OneToManyAssociationField;
use Shopware\Api\Entity\FieldCollection;
use Shopware\Api\Entity\Write\Flag\Extension;
use Shopware\Api\Entity\Write\Flag\Required;
use Shopware\Framework\Serializer\StructDecoder;
use Shopware\Rest\Exception\MissingDataException;
use Shopware\Rest\Exception\MissingValueException;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Exception\UnsupportedException;

class JsonApiEncoder implements EncoderInterface
{
    public const FORMAT = 'jsonapi';

    /**
     * @var StructDecoder
     */
    private $structDecoder;

    /**
     * Properties that should not appear in the attributes of a resource
     *
     * @var array
     */
    private static $ignoredAttributes = [
        'id',
        '_class',
        'translations',
    ];

    public function __construct(StructDecoder $structDecoder)
    {
        $this->structDecoder = $structDecoder;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format): bool
    {
        return $format === self::FORMAT;
    }

    /**
     * {@inheritdoc}
     */
    public function encode($data, $format, array $context = [])
    {
        $data = $this->structDecoder->decode($data, 'struct');

        if (!is_iterable($data)) {
            throw new UnexpectedValueException('Input is not iterable.');
        }

        if (!array_key_exists('uri', $context)) {
            throw new BadMethodCallException('The context key "uri" is required.');
        }

        if (!array_key_exists('definition', $context)) {
            throw new BadMethodCallException(sprintf('The context key "definition" is required and must be an instance of %s.', EntityDefinition::class));
        }

        if (!array_key_exists('basic', $context)) {
            throw new BadMethodCallException('The context key "basic" is required to indicate which type of struct should be encoded.');
        }

        $response = [];

        if (array_key_exists('data', $context)) {
            $response = array_merge($response, $context['data']);
        }

        if (empty($data)) {
            $response['data'] = [];

            return json_encode($response);
        }

        if ($this->isCollection($data)) {
            $response = array_merge($response, $this->encodeCollection($data, $context));

            $primaryResourcesHashes = array_map(function (array $resource) {
                return $this->getResourceHash($resource);
            }, $response['data']);
        } else {
            $response = array_merge($response, $this->encodeEntity($data, $context));
            $primaryResourcesHashes = [$this->getResourceHash($response['data'])];
        }

        if (array_key_exists('included', $response) && \count($response)) {
            // reduce includes by removing primary resources
            $response['included'] = array_values(array_diff_key($response['included'], array_flip($primaryResourcesHashes)));
        }

        return json_encode($response);
    }

    /**
     * @param mixed $data
     * @param array $context
     *
     * @return array
     */
    public function encodeEntity($data, array $context = []): array
    {
        $attributes = [];
        $relationships = [];
        $includes = [];

        $objectContextUri = $context['uri'] . '/' . $this->camelCaseToSnailCase($context['definition']::getEntityName()) . '/' . $this->getIdentifier($data);

        /** @var FieldCollection $fields */
        $fields = $context['definition']::getFields();

        if ($context['basic'] === true) {
            $fields = $fields->getBasicProperties();
        }

        $missingProperties = [];

        foreach ($fields as $field) {
            if (\in_array($field->getPropertyName(), self::$ignoredAttributes, true)) {
                continue;
            }

            $key = $field->getPropertyName();

            try {
                $value = $this->getValue($field, $data);
            } catch (MissingValueException $exception) {
                if (!$field instanceof FkField && $field->is(Required::class)) {
                    $missingProperties[] = $exception->getFieldName();
                }

                $value = $this->getDefaultValue($field);
            }

            if ($field instanceof ManyToOneAssociationField) {
                $relationships[$key] = [
                    'data' => null,
                    'links' => [
                        'related' => $objectContextUri . '/' . $this->camelCaseToSnailCase($key),
                    ],
                ];

                if (!$value) {
                    continue;
                }

                $subContext = $context;
                $subContext['definition'] = $this->getReferenceDefinition($context['definition'], $key);
                $subContext['basic'] = true;

                $relationship = $this->extractRelationship($value, $subContext['definition']);
                $relationships[$key]['data'] = $relationship;

                $encoded = $this->encodeEntity($value, $subContext);
                $includes[$this->getResourceHash($relationship)] = $encoded['data'];

                if (\count($encoded['included'])) {
                    $includes = array_merge($includes, $encoded['included']);
                }
                continue;
            }

            if ($this->isToManyAssociation($context['definition'], $key)) {
                $relationships[$key] = [
                    'data' => [],
                    'links' => [
                        'related' => $objectContextUri . '/' . $this->camelCaseToSnailCase($key),
                    ],
                ];

                if (\count($value) === 0) {
                    continue;
                }

                foreach ($value as $resource) {
                    $subContext = $context;
                    $subContext['definition'] = $this->getReferenceDefinition($context['definition'], $key);
                    $subContext['basic'] = true;

                    $relationship = $this->extractRelationship($resource, $subContext['definition']);
                    $relationships[$key]['data'][] = $relationship;

                    $encoded = $this->encodeEntity($resource, $subContext);
                    $includes[$this->getResourceHash($relationship)] = $encoded['data'];

                    if (\count($encoded['included'])) {
                        $includes = array_merge($includes, $encoded['included']);
                    }
                }
                continue;
            }

            $attributes[$key] = $value;
        }

        if (\count($missingProperties) > 0) {
            throw new MissingDataException($missingProperties);
        }

        $context['uri'] = $objectContextUri;

        $object = [
            'id' => $this->getIdentifier($data),
            'type' => $context['definition']::getEntityName(),
            'links' => [
                'self' => $context['uri'],
            ],
        ];

        if (\count($attributes)) {
            $object['attributes'] = $attributes;
        }

        if (\count($relationships)) {
            $object['relationships'] = $relationships;
        }

        $response = [
            'data' => $object,
            'included' => $includes,
        ];

        return $response;
    }

    private function isCollection(array $array): bool
    {
        return array_keys($array) === range(0, \count($array) - 1);
    }

    /**
     * @param EntityDefinition|string $definition
     * @param string                  $fieldName
     *
     * @return bool
     */
    private function isToManyAssociation(string $definition, string $fieldName): bool
    {
        $field = $definition::getFields()->get($fieldName);

        if (!$field) {
            return false;
        }

        return $field instanceof ManyToManyAssociationField || $field instanceof OneToManyAssociationField;
    }

    /**
     * @param array $data
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function getIdentifier(array $data): string
    {
        if (!array_key_exists('id', $data) || !$data['id']) {
            throw new UnexpectedValueException('Could not determine identifier for object.');
        }

        return $data['id'];
    }

    /**
     * @param mixed                   $value
     * @param string|EntityDefinition $definition
     *
     * @return array
     */
    private function extractRelationship($value, string $definition): array
    {
        return [
            'id' => $this->getIdentifier($value),
            'type' => $definition::getEntityName(),
        ];
    }

    /**
     * @param mixed $data
     * @param array $context
     *
     * @return array
     */
    private function encodeCollection($data, array $context): array
    {
        $response = [
            'data' => [],
            'included' => [],
        ];

        foreach ($data as $resource) {
            $resource = $this->encodeEntity($resource, $context);

            $response['data'][] = $resource['data'];

            foreach ($resource['included'] as $include) {
                $key = $this->getResourceHash($include);

                if (array_key_exists($key, $response['included'])) {
                    continue;
                }

                $response['included'][$key] = $include;
            }
        }

        return $response;
    }

    private function camelCaseToSnailCase(string $input): string
    {
        $input = str_replace('_', '-', $input);

        return ltrim(strtolower(preg_replace('/[A-Z]/', '-$0', $input)), '-');
    }

    private function getResourceHash(array $resource): string
    {
        return md5(json_encode(['id' => $resource['id'], 'type' => $resource['type']]));
    }

    /**
     * @param string|EntityDefinition $definition
     * @param string                  $fieldName
     *
     * @throws NotEncodableValueException
     * @throws UnsupportedException
     *
     * @return EntityDefinition|string
     */
    private function getReferenceDefinition(string $definition, string $fieldName): string
    {
        $field = $definition::getFields()->get($fieldName);

        if (!$field) {
            throw new NotEncodableValueException(sprintf('Field "%s" was not found in definition "%s".', $fieldName, $definition));
        }

        if ($field instanceof OneToManyAssociationField) {
            return $field->getReferenceClass();
        } elseif ($field instanceof ManyToOneAssociationField) {
            return $field->getReferenceClass();
        } elseif ($field instanceof ManyToManyAssociationField) {
            return $field->getReferenceDefinition();
        }

        throw new UnsupportedException('Could not determine reference definition due to unknown association type.');
    }

    /**
     * @param Field $field
     * @param array $data
     *
     * @return mixed
     */
    private function getValue(Field $field, array $data)
    {
        if ($field->is(Extension::class)) {
            if (!array_key_exists('extensions', $data)) {
                throw new RuntimeException(sprintf('Expected data container to contain key "extensions". It is required for field "%s".', $field->getPropertyName()));
            }

            if (!array_key_exists($field->getPropertyName(), $data['extensions'])) {
                throw new MissingValueException(sprintf('extensions.%s', $field->getPropertyName()));
            }

            return $data['extensions'][$field->getPropertyName()];
        }

        if (!array_key_exists($field->getPropertyName(), $data)) {
            throw new MissingValueException($field->getPropertyName());
        }

        return $data[$field->getPropertyName()];
    }

    private function getDefaultValue(Field $field): ?array
    {
        if ($field instanceof ManyToManyAssociationField || $field instanceof OneToManyAssociationField) {
            return [];
        }

        return null;
    }
}
