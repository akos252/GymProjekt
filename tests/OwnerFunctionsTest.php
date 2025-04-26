<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../xampp/htdocs/projekt/includes/owner_functions.php';

use PHPUnit\Framework\TestCase;

class OwnerFunctionsTest extends TestCase
{
    // Tests if password and confirm password dont match, return should be "Passwords do not match!"
    public function testPasswordsDoNotMatch()
    {
        $connMock = $this->createMock(mysqli::class);
        $result = registerOwner($connMock, "user", "pw1", "pw2", "Full Name", "email@example.com", "123456789", "1990-01-01");

        $this->assertEquals("Passwords do not match!", $result);
    }

    //Tests for successful registration, checks for existing user, succcessful insert into login table and successful insert into owner table. Return should be "true".
    public function testSuccessfulRegistration()
    {
        $stmtSelectMock = $this->createMock(mysqli_stmt::class);
        $stmtSelectMock->method('get_result')->willReturn(new class {
            public $num_rows = 0;
        });
        $stmtSelectMock->method('execute')->willReturn(true);

        $stmtLoginInsertMock = $this->createMock(mysqli_stmt::class);
        $stmtLoginInsertMock->method('execute')->willReturn(true);

        $stmtOwnerInsertMock = $this->createMock(mysqli_stmt::class);
        $stmtOwnerInsertMock->method('execute')->willReturn(true);

        $connMock = $this->createMock(mysqli::class);

        $connMock->method('prepare')->willReturnCallback(function ($query) use ($stmtSelectMock, $stmtLoginInsertMock, $stmtOwnerInsertMock) {
            if (str_starts_with($query, 'SELECT')) return $stmtSelectMock;
            if (str_starts_with($query, 'INSERT INTO login')) return $stmtLoginInsertMock;
            return $stmtOwnerInsertMock;
        });

        $result = registerOwner(
            $connMock,
            "newuser",
            "pw",
            "pw",
            "Full Name",
            "email@example.com",
            "123456789",
            "1990-01-01",
            42
        );

        $this->assertTrue($result);
    }
}