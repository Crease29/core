<?php declare(strict_types=1);

namespace Shopware\Api\Entity\Write\Flag;

/**
 * Associated data with this flag, restricts the delete of the entity in case that a record with the primary key exists.
 */
class RestrictDelete extends Flag
{
}
