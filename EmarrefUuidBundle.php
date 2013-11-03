<?php

namespace Emarref\Bundle\UuidBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\DBAL\Types\Type;

class EmarrefUuidBundle extends Bundle
{
    public function boot()
    {
        Type::addType('binary_guid', 'Emarref\Bundle\UuidBundle\Type\BinaryGuidType');
    }
}
