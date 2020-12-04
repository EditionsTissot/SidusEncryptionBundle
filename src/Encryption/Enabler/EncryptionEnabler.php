<?php

namespace Sidus\EncryptionBundle\Encryption\Enabler;

class EncryptionEnabler implements EncryptionEnablerInterface
{
    /**
     * By default the encryption is enabled.
     *
     * @var bool
     */
    private bool $enabled = true;
    
    public function enableEncryption(): void
    {
        $this->enabled = true;
    }
    
    public function disableEncryption(): void
    {
        $this->enabled = false;
    }
    
    public function isEncryptionEnabled(): bool
    {
        return $this->enabled;
    }
}
