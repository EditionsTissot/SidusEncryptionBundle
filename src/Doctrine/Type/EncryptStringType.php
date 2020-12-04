<?php

namespace Sidus\EncryptionBundle\Doctrine\Type;

use Doctrine\DBAL\Types\StringType;
use Sidus\EncryptionBundle\Doctrine\Type\Behavior\EncryptType;

class EncryptStringType extends StringType implements EncryptTypeInterface
{
    use EncryptType;

    public function getName(): string
    {
        return 'encrypt_string';
    }
}
