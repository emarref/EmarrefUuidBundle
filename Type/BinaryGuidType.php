<?php

namespace Ramble\UuidBundle\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Ramble\UuidBundle\Uuid;

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

        return Uuid\Mysql::uuidToBinary($value);
    }
}