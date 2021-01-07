<?php

namespace Sidus\EncryptionBundle\Doctrine;

use Exception;
use Psr\Log\LoggerInterface;
use Sidus\EncryptionBundle\Encryption\Enabler\EncryptionEnablerInterface;
use Sidus\EncryptionBundle\Manager\EncryptionManagerInterface;
use Sidus\EncryptionBundle\Registry\EncryptionManagerRegistryInterface;
use Symfony\Component\String\ByteString;

class ValueEncrypter implements ValueEncrypterInterface
{
    private EncryptionManagerRegistryInterface $registry;
    private LoggerInterface $logger;
    private EncryptionEnablerInterface $encryptionEnabler;
    private bool $throwExceptions;
    
    public function __construct(
        EncryptionManagerRegistryInterface $registry,
        LoggerInterface $logger,
        EncryptionEnablerInterface $encryptionEnabler,
        bool $throwExceptions
    ) {
        $this->registry = $registry;
        $this->logger = $logger;
        $this->encryptionEnabler = $encryptionEnabler;
        $this->throwExceptions = $throwExceptions;
    }
    
    public function encrypt(string $value): string
    {
        // Allow to do not encrypt the value for the current request
        if (!$this->encryptionEnabler->isEncryptionEnabled()) {
            return $value;
        }
        $manager = $this->registry->getDefaultEncryptionManager();
        $value = $manager->getEncryptionAdapter()::getCode().'.'.$manager->encryptString($value);
    
        return $value;
    }
    
    public function decrypt(string $value): string
    {
        // Allow to do not decrypt the value for the current request
        if (!$this->encryptionEnabler->isEncryptionEnabled()) {
            return $value;
        }
        $manager = $this->extractManagerFromEncryptedValue($value);
        $value = (new ByteString($value))->replace($manager->getEncryptionAdapter()::getCode().'.', '')->toByteString();
    
        try {
            return $manager->decryptString($value);
        } catch (Exception $exception) {
            foreach ($this->registry->getEncryptionManagers() as $encryptionManager) {
                try {
                    return $encryptionManager->decryptString($value);
                } catch (Exception $exception) {
                }
            }
    
            if ($this->throwExceptions) {
                throw $exception;
            }
        }
    
        return $value;
    }
    
    private function extractManagerFromEncryptedValue(string $value): EncryptionManagerInterface
    {
        $split = (new ByteString($value))->split('.');
        
        if (count($split) < 3) {
            return $this->registry->getDefaultEncryptionManager();
        }
        $managerCode = $split[0].'.'.$split[1];
        
        if ($this->registry->hasEncryptionManager($managerCode)) {
            return $this->registry->getEncryptionManager($managerCode);
        }
        
        return $this->registry->getDefaultEncryptionManager();
    }
}
