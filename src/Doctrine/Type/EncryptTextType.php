<?php

namespace Sidus\EncryptionBundle\Doctrine\Type;

use Doctrine\DBAL\Types\TextType;
use Sidus\EncryptionBundle\Doctrine\Type\Behavior\EncryptType;

class EncryptTextType extends TextType implements EncryptTypeInterface
{
    use EncryptType;

    public function getName()
    {
        return 'encrypt_text';
    }
}
