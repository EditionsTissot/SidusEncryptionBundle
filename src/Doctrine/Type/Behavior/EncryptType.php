<?php

namespace Sidus\EncryptionBundle\Doctrine\Type\Behavior;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Sidus\EncryptionBundle\Encryption\Enabler\EncryptionEnablerInterface;
use Sidus\EncryptionBundle\Manager\EncryptionManagerInterface;

trait EncryptType
{
    private EncryptionManagerInterface $encryptionManager;
    private EncryptionEnablerInterface $encryptionEnabler;
    
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        // Allow to do not decrypt the value for the current request
        if (!$this->encryptionEnabler->isEncryptionEnabled()) {
            return $value;
        }
        
        return $this->encryptionManager->decryptString(base64_decode($value));
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        // Allow to do not decrypt the value for the current request
        if (!$this->encryptionEnabler->isEncryptionEnabled()) {
            return $value;
        }
        $value = $this->encryptionManager->encryptString($value);
        
        return base64_encode($value);
    }
    
    public function setEncryptionManager(EncryptionManagerInterface $encryptionManager): void
    {
        $this->encryptionManager = $encryptionManager;
    }
    
    public function setEncryptionEnabler(EncryptionEnablerInterface $encryptionEnabler): void
    {
        $this->encryptionEnabler = $encryptionEnabler;
    }
}
