<?php

namespace Sidus\EncryptionBundle\Tests\PHPUnit\Doctrine\Type;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sidus\EncryptionBundle\Doctrine\Type\EncryptStringType;
use Sidus\EncryptionBundle\Doctrine\Type\EncryptTextType;
use Sidus\EncryptionBundle\Encryption\Enabler\EncryptionEnablerInterface;
use Sidus\EncryptionBundle\Manager\EncryptionManagerInterface;

class EncryptTextTypeTest extends TestCase
{
    public function testConvertToPHPValue(): void
    {
        [$type, $encryptionManager, $encryptionEnabler] = $this->createType();
        $encryptedString = '\X666';
        $platform = $this->createMock(MySqlPlatform::class);
        
        $encryptionEnabler
            ->expects($this->once())
            ->method('isEncryptionEnabled')
            ->willReturn(true)
        ;
        
        // The type SHOULD decrypt the encrypted string
        $encryptionManager
            ->expects($this->once())
            ->method('decryptString')
            ->with(base64_decode($encryptedString))
            ->willReturn('my_decrypted_string')
        ;
        
        $value = $type->convertToPHPValue($encryptedString, $platform);
        $this->assertEquals('my_decrypted_string', $value);
    }
    
    public function testConvertToPHPValueWithEncryptionDisabled(): void
    {
        [$type, $encryptionManager, $encryptionEnabler] = $this->createType();
        $encryptedString = '\X666';
        $platform = $this->createMock(MySqlPlatform::class);
        
        $encryptionEnabler
            ->expects($this->once())
            ->method('isEncryptionEnabled')
            ->willReturn(false)
        ;
        
        // The type SHOULD not encrypt the encrypted string if the encryption is disabled
        $encryptionManager
            ->expects($this->never())
            ->method('decryptString')
        ;
        
        $value = $type->convertToPHPValue($encryptedString, $platform);
        $this->assertEquals('\X666', $value);
    }
    
    public function testConvertToDatabaseValue(): void
    {
        [$type, $encryptionManager, $encryptionEnabler] = $this->createType();
        $string = 'my_string';
        $platform = $this->createMock(MySqlPlatform::class);
        
        $encryptionEnabler
            ->expects($this->once())
            ->method('isEncryptionEnabled')
            ->willReturn(true)
        ;
        
        // The type SHOULD decrypt the encrypted string
        $encryptionManager
            ->expects($this->once())
            ->method('encryptString')
            ->with($string)
            ->willReturn('my_encrypted_string')
        ;
        
        $value = $type->convertToDatabaseValue($string, $platform);
        $this->assertEquals(base64_encode('my_encrypted_string'), $value);
    }
    
    public function testConvertToDatabaseValueWithEncryptionDisabled(): void
    {
        [$type, $encryptionManager, $encryptionEnabler] = $this->createType();
        $string = 'my_string';
        $platform = $this->createMock(MySqlPlatform::class);
        
        $encryptionEnabler
            ->expects($this->once())
            ->method('isEncryptionEnabled')
            ->willReturn(false)
        ;
        
        // The type SHOULD not decrypt the encrypted string if the encryption is disabled
        $encryptionManager
            ->expects($this->never())
            ->method('encryptString')
        ;
        
        $value = $type->convertToDatabaseValue($string, $platform);
        $this->assertEquals('my_string', $value);
    }
    
    public function testGetName(): void
    {
        [$type] = $this->createType();
        
        $this->assertEquals('encrypt_text', $type->getName());
    }
    
    /**
     * @return EncryptStringType[]|MockObject[]
     */
    private function createType(): array
    {
        $encryptionManager = $this->createMock(EncryptionManagerInterface::class);
        $encryptionEnabler = $this->createMock(EncryptionEnablerInterface::class);
        
        $type = new EncryptTextType();
        $type->setEncryptionManager($encryptionManager);
        $type->setEncryptionEnabler($encryptionEnabler);
    
        return [$type, $encryptionManager, $encryptionEnabler];
    }
}
