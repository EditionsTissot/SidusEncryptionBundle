<?php

namespace Sidus\EncryptionBundle\Doctrine\Type;

use Sidus\EncryptionBundle\Encryption\Enabler\EncryptionEnablerInterface;
use Sidus\EncryptionBundle\Manager\EncryptionManagerInterface;

interface EncryptTypeInterface
{
    public function setEncryptionManager(EncryptionManagerInterface $encryptionManager): void;
    
    public function setEncryptionEnabler(EncryptionEnablerInterface $encryptionEnabler): void;
}
