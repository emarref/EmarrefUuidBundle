<?php

namespace Emarref\Bundle\UuidBundle\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Emarref\Bundle\UuidBundle\Uuid;

class BinaryGuidType extends Type
{
    const BINARY_GUID = 'binary_guid';

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return sprintf('BINARY(%d)', $fieldDeclaration['length']);
    }

    public function getName()
    {
        return self::BINARY_GUID;
    }

    public function convertToPhpValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return $value;
        }

        return Uuid\Mysql::binaryToUuid($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        if (!Uuid\Mysql::isUuid($value)) {
            throw new \InvalidArgumentException(sprintf('Value "%s" is not a UUID.', $value));
        }

        return Uuid\Mysql::uuidToBinary($value);
    }
}