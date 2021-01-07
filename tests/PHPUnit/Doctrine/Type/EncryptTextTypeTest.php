<?php

namespace Sidus\EncryptionBundle\Tests\PHPUnit\Doctrine\Type;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Type;
use Monolog\Logger;
use Sidus\EncryptionBundle\Doctrine\Type\EncryptTextType;
use Sidus\EncryptionBundle\Doctrine\ValueEncrypter;
use Sidus\EncryptionBundle\Encryption\Aes256GcmSodiumEncryptionAdapter;
use Sidus\EncryptionBundle\Encryption\Enabler\EncryptionEnabler;
use Sidus\EncryptionBundle\Encryption\EncryptionAdapterInterface;
use Sidus\EncryptionBundle\Encryption\Rijndael256MCryptEncryptionAdapter;
use Sidus\EncryptionBundle\Encryption\XChaChaPolySodiumEncryptionAdapter;
use Sidus\EncryptionBundle\Manager\EncryptionManager;
use Sidus\EncryptionBundle\Registry\EncryptionManagerRegistry;
use Sidus\EncryptionBundle\Session\CipherKeyStorage;
use Sidus\EncryptionBundle\Tests\PHPUnit\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class EncryptTextTypeTest extends TestCase
{
    private Type $type;
    
    /**
     * @dataProvider encryptDataProvider
     */
    public function testConvertToPHPValue(string $value): void
    {
        $encryptedValue = $this->type->convertToDatabaseValue($value, new MySqlPlatform());
        $decryptedValue = $this->type->convertToPHPValue($encryptedValue, new MySqlPlatform());
        
        $this->assertEquals($value, $decryptedValue);
    }
    
    public function testGetName(): void
    {
        $this->assertEquals('encrypt_text', $this->type->getName());
    }
    
    protected function setUp(): void
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
        
        $registry = new EncryptionManagerRegistry(
            XChaChaPolySodiumEncryptionAdapter::getCode(), new \ArrayIterator($managers)
        );
        $encrypter = new ValueEncrypter($registry, new Logger('encryption'), $enabler, true);
        $this->type = new EncryptTextType();
        $this->type->setValueEncrypter($encrypter);
    }
}
