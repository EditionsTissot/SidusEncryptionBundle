<?php

namespace Sidus\EncryptionBundle\Doctrine\Type\Behavior;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Sidus\EncryptionBundle\Doctrine\ValueEncrypterInterface;

trait EncryptType
{
    private ValueEncrypterInterface $valueEncrypter;
    
    public function convertToPHPValue($value, AbstractPlatform $platform): string
    {
        if ($value === null) {
            $value = '';
        }
        // Decode value previously encoded in base64 for database storage
        $value = base64_decode($value);
        
        return $this->valueEncrypter->decrypt($value);
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if ($value === null) {
            $value = '';
        }
        $value = $this->valueEncrypter->encrypt($value);
    
        // Encoding to base64 to avoid issue when storing binary strings
        return base64_encode($value);
    }
    
    public function setValueEncrypter(ValueEncrypterInterface $valueEncrypter): void
    {
        $this->valueEncrypter = $valueEncrypter;
    }
}
