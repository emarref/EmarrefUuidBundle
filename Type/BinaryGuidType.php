<?php

namespace Ramble\UuidBundle\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

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

        $value = unpack('H*', $value);

        $value = array_shift($value);

        $value = strrev($value);

        $parts = array();

        preg_match('/^([a-f0-9]{8})([a-f0-9]{4})([a-f0-9]{4})([a-f0-9]{4})([a-f0-9]{12})$/', $value, $parts);

        array_shift($parts);

        $value = implode('-', $parts);

        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        $value = str_replace('-', '', $value);

        $value = strrev($value);

        $value = pack('H*', $value);

        return $value;
    }
}