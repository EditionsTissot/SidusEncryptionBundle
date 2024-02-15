<?php
/*
 * This file is part of the Sidus/EncryptionBundle package.
 *
 * Copyright (c) 2015-2018 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sidus\EncryptionBundle\Session;

use Sidus\EncryptionBundle\Exception\EmptyCipherKeyException;
use Sidus\EncryptionBundle\Exception\EmptyOwnershipIdException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Handles cipher key storage across multiple request
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class CipherKeyStorage implements CipherKeyStorageInterface
{
    protected const SESSION_CIPHER_KEY = 'sidus.encryption.cipherkey';
    protected const SESSION_OWNERSHIP_KEY = 'sidus.encryption.ownership';

    protected RequestStack $request;
    protected string $cipherKey;
    protected $encryptionOwnershipId;

    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }
    
    private function getSession(): SessionInterface
    {
        return $this->request->getSession();
    }

    /**
     * Set the current cipher-key used for encryption/decryption
     *
     * @param string $cipherKey
     *
     * @throws EmptyCipherKeyException
     */
    public function setCipherKey(string $cipherKey): void
    {
        if (!trim($cipherKey)) {
            throw new EmptyCipherKeyException('Trying to set an empty cipher key');
        }
        $this->cipherKey = $cipherKey;
        $this->getSession()->set(self::SESSION_CIPHER_KEY, bin2hex($cipherKey));
    }

    /**
     * Get the current cipher-key used for encryption/decryption
     *
     * @throws EmptyCipherKeyException
     *
     * @return string
     */
    public function getCipherKey(): string
    {
        if (!isset($this->cipherKey) || !$this->cipherKey) {
            $this->cipherKey = hex2bin($this->getSession()->get(self::SESSION_CIPHER_KEY));
        }
        if (!trim($this->cipherKey)) {
            throw new EmptyCipherKeyException('Empty cipher key');
        }

        return $this->cipherKey;
    }

    /**
     * Set the ownership ID in session
     *
     * @param mixed $encryptionOwnershipId
     *
     * @throws EmptyOwnershipIdException
     */
    public function setEncryptionOwnershipId($encryptionOwnershipId): void
    {
        if (!trim($encryptionOwnershipId)) {
            throw new EmptyOwnershipIdException('Trying to set an empty ownership identifier');
        }
        $this->encryptionOwnershipId = $encryptionOwnershipId;
        $this->getSession()->set(self::SESSION_OWNERSHIP_KEY, bin2hex($encryptionOwnershipId));
    }

    /**
     * Returns the ownership ID loaded in session
     *
     * @throws EmptyOwnershipIdException
     *
     * @return mixed
     */
    public function getEncryptionOwnershipId(): string
    {
        if (!$this->encryptionOwnershipId) {
            $this->encryptionOwnershipId = hex2bin($this->getSession()->get(self::SESSION_OWNERSHIP_KEY));
        }
        if (!trim($this->encryptionOwnershipId)) {
            throw new EmptyOwnershipIdException('Empty ownership identifier');
        }

        return $this->encryptionOwnershipId;
    }
}
