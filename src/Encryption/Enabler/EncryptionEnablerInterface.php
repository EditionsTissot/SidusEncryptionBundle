<?php

namespace Sidus\EncryptionBundle\Encryption\Enabler;

interface EncryptionEnablerInterface
{
    /**
     * Enable the encryption for the current request.
     */
    public function enableEncryption(): void;
    
    /**
     * Disable the encryption for the current request.
     */
    public function disableEncryption(): void;
    
    /**
     * Return if the encryption is enabled in the current request.
     *
     * @return bool
     */
    public function isEncryptionEnabled(): bool;
}
