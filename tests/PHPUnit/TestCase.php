<?php

namespace Sidus\EncryptionBundle\Tests\PHPUnit;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function encryptDataProvider(): array
    {
        return [
            ['my_string'],
            ['0x4(-\'""ù*$ù*ù$ù$⁾)=+}*µ£$ê~^#\\/'],
            [$this->generateRandomString(2)],
            [$this->generateRandomString(100)],
            [$this->generateRandomString(1000)],
            [$this->generateRandomString(5000)],
            [$this->generateRandomString(1000000)],
        ];
    }
    
    protected function generateRandomString(int $length = 10): string
    {
        return bin2hex(random_bytes($length));
    }
}
