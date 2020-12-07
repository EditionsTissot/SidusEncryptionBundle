<?php

namespace Sidus\EncryptionBundle\Doctrine\Type;

use Sidus\EncryptionBundle\Doctrine\ValueEncrypterInterface;

interface EncryptTypeInterface
{
    public function setValueEncrypter(ValueEncrypterInterface $valueEncrypter): void;
}
