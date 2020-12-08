<?php

namespace Sidus\EncryptionBundle\Tests\PHPUnit\Doctrine;

use Monolog\Logger;
use Sidus\EncryptionBundle\Doctrine\ValueEncrypter;
use Sidus\EncryptionBundle\Doctrine\ValueEncrypterInterface;
use Sidus\EncryptionBundle\Encryption\Aes256GcmSodiumEncryptionAdapter;
use Sidus\EncryptionBundle\Encryption\Enabler\EncryptionEnabler;
use Sidus\EncryptionBundle\Encryption\EncryptionAdapterInterface;
use Sidus\EncryptionBundle\Encryption\Rijndael256MCryptEncryptionAdapter;
use Sidus\EncryptionBundle\Encryption\XChaChaPolySodiumEncryptionAdapter;
use Sidus\EncryptionBundle\Manager\EncryptionManager;
use Sidus\EncryptionBundle\Registry\EncryptionManagerRegistry;
use Sidus\EncryptionBundle\Registry\EncryptionManagerRegistryInterface;
use Sidus\EncryptionBundle\Session\CipherKeyStorage;
use Sidus\EncryptionBundle\Tests\PHPUnit\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class ValueEncrypterTest extends TestCase
{
    private ValueEncrypterInterface $encrypter;
    private EncryptionManagerRegistryInterface $registry;
    
    /**
     * @dataProvider encryptDataProvider
     */
    public function testEncrypt(string $value): void
    {
        $adapterCode = XChaChaPolySodiumEncryptionAdapter::getCode();
        $encryptedValue = $this->encrypter->encrypt($value);
        $this->assertStringStartsWith($adapterCode, $encryptedValue);
    
        $decryptedValue = $this->encrypter->decrypt($encryptedValue);
        $this->assertEquals($value, $decryptedValue);
    }
    
    /**
     * @dataProvider encryptDataProvider
     */
    public function testEncryptWithMCryptEncryptedString(string $value): void
    {
        $mcrypt = $this->registry->getEncryptionManager(Rijndael256MCryptEncryptionAdapter::getCode());
    
        $encryptedString = $mcrypt->encryptString($value);
        $encryptedStringWithSodium = $this->registry->getEncryptionManager(XChaChaPolySodiumEncryptionAdapter::getCode());
        $decryptedValue = $this->encrypter->decrypt($encryptedString);

        $this->assertEquals($value, $decryptedValue);
        $this->assertNotEquals($encryptedStringWithSodium, $decryptedValue);
    }
    
    protected function setUp(string $defaultAdapterCode = null): void
    {
        $enabler = new EncryptionEnabler();
        $session = new Session(new MockFileSessionStorage());
        $storage = new CipherKeyStorage($session);
    
        $managers = [];
        $adapters = [
            new Aes256GcmSodiumEncryptionAdapter(),
            new Rijndael256MCryptEncryptionAdapter(),
            new XChaChaPolySodiumEncryptionAdapter(),
        ];
    
        /** @var EncryptionAdapterInterface $adapter */
        foreach ($adapters as $adapter) {
            $storage->setCipherKey($adapter->generateKey());
            $managers[] = new EncryptionManager($adapter, $storage);
        }
        
        $this->registry = new EncryptionManagerRegistry(
            XChaChaPolySodiumEncryptionAdapter::getCode(), new \ArrayIterator($managers)
        );
        $this->encrypter = new ValueEncrypter($this->registry, new Logger('encryption'), $enabler);
    }
}
