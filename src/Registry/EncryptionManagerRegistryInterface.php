<?php

namespace Sidus\EncryptionBundle\Registry;

use Sidus\EncryptionBundle\Entity\CryptableInterface;
use Sidus\EncryptionBundle\Entity\UserEncryptionProviderInterface;
use Sidus\EncryptionBundle\Manager\EncryptionManagerInterface;

/**
 * Registry for all available encryption managers (one for each adapter).
 */
interface EncryptionManagerRegistryInterface
{
    /**
     * @return EncryptionManagerInterface[]
     */
    public function getEncryptionManagers(): array;
    
    public function getEncryptionManagerForEntity(CryptableInterface $entity): EncryptionManagerInterface;

    public function getEncryptionManagerForUser(UserEncryptionProviderInterface $user): EncryptionManagerInterface;
    
    public function getEncryptionManager(string $code): EncryptionManagerInterface;

    public function hasEncryptionManager(string $code): bool;
    
    public function getDefaultEncryptionManager(): EncryptionManagerInterface;
}
