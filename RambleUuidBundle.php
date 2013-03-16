<?php

namespace Ramble\UuidBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\DBAL\Types\Type;

class RambleUuidBundle extends Bundle
{
    public function boot()
    {
        Type::addType('binary_guid', 'Ramble\UuidBundle\Type\BinaryGuidType');
    }
}
