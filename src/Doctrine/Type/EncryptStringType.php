<?php

namespace Sidus\EncryptionBundle\Doctrine\Type;

use Doctrine\DBAL\Types\TextType;
use Sidus\EncryptionBundle\Doctrine\Type\Behavior\EncryptType;

class EncryptStringType extends TextType implements EncryptTypeInterface
{
    use EncryptType;

    public function getName(): string
    {
        return 'encrypt_string';
    }
}
