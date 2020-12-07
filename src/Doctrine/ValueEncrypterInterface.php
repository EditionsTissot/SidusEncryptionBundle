<?php

namespace Sidus\EncryptionBundle\Doctrine;

interface ValueEncrypterInterface
{
    public function encrypt(string $value): string;

    public function decrypt(string $value): string;
}
