<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../xampp/htdocs/projekt/includes/owner_functions.php';

class OwnerFunctionsTest extends TestCase
{
    // Tests if password and confirm password don't match
    public function testPasswordsDoNotMatch()
    {
        $connMock = $this->createMock(mysqli::class);
        $result = registerOwner($connMock, "user", "pw1", "pw2", "Full Name", "email@example.com", "123456789", "2000-01-01");

        $this->assertEquals("Passwords do not match!", $result);
    }

    // Tests for successful registration
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
            "2000-01-01",
            42
        );

        $this->assertTrue($result);
    }

    // Tests if username or mobile number already exists
    public function testUserAlreadyExists()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('get_result')->willReturn(new class {
            public $num_rows = 1;
        });
        $stmtMock->method('execute')->willReturn(true);

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);

        $result = registerOwner(
            $connMock,
            "existinguser",
            "pw",
            "pw",
            "Full Name",
            "email@example.com",
            "123456789",
            "2000-01-01",
            42
        );

        $this->assertEquals("Username or mobile number already exists.", $result);
    }

    // Tests SELECT prepare failure
    public function testSelectPrepareFailure()
    {
        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn(false);

        $result = registerOwner(
            $connMock,
            "user",
            "pw",
            "pw",
            "Full Name",
            "email@example.com",
            "123456789",
            "2000-01-01",
            42
        );

        $this->assertEquals("Error preparing SELECT user.", $result);
    }

    // Tests SELECT execute failure
    public function testSelectExecuteFailure()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('execute')->willReturn(false);

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);

        $result = registerOwner(
            $connMock,
            "user",
            "pw",
            "pw",
            "Full Name",
            "email@example.com",
            "123456789",
            "2000-01-01",
            42
        );

        $this->assertEquals("Database error during SELECT execute.", $result);
    }

    // Tests INSERT login prepare failure
    public function testInsertLoginPrepareFailure()
    {
        $stmtSelectMock = $this->createMock(mysqli_stmt::class);
        $stmtSelectMock->method('get_result')->willReturn(new class {
            public $num_rows = 0;
        });
        $stmtSelectMock->method('execute')->willReturn(true);

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturnCallback(function ($query) use ($stmtSelectMock) {
            if (str_starts_with($query, 'SELECT')) return $stmtSelectMock;
            return false;
        });

        $result = registerOwner(
            $connMock,
            "user",
            "pw",
            "pw",
            "Full Name",
            "email@example.com",
            "123456789",
            "2000-01-01",
            42
        );

        $this->assertEquals("Error preparing INSERT login.", $result);
    }

    // Tests INSERT login execute failure
    public function testInsertLoginExecuteFailure()
    {
        $stmtSelectMock = $this->createMock(mysqli_stmt::class);
        $stmtSelectMock->method('get_result')->willReturn(new class {
            public $num_rows = 0;
        });
        $stmtSelectMock->method('execute')->willReturn(true);

        $stmtLoginInsertMock = $this->createMock(mysqli_stmt::class);
        $stmtLoginInsertMock->method('execute')->willReturn(false);

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturnCallback(function ($query) use ($stmtSelectMock, $stmtLoginInsertMock) {
            if (str_starts_with($query, 'SELECT')) return $stmtSelectMock;
            return $stmtLoginInsertMock;
        });

        $result = registerOwner(
            $connMock,
            "user",
            "pw",
            "pw",
            "Full Name",
            "email@example.com",
            "123456789",
            "2000-01-01",
            42
        );

        $this->assertEquals("Registration failed at login insertion.", $result);
    }

    // Tests INSERT owner prepare failure
    public function testInsertOwnerPrepareFailure()
    {
        $stmtSelectMock = $this->createMock(mysqli_stmt::class);
        $stmtSelectMock->method('get_result')->willReturn(new class {
            public $num_rows = 0;
        });
        $stmtSelectMock->method('execute')->willReturn(true);

        $stmtLoginInsertMock = $this->createMock(mysqli_stmt::class);
        $stmtLoginInsertMock->method('execute')->willReturn(true);

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturnCallback(function ($query) use ($stmtSelectMock, $stmtLoginInsertMock) {
            if (str_starts_with($query, 'SELECT')) return $stmtSelectMock;
            if (str_starts_with($query, 'INSERT INTO login')) return $stmtLoginInsertMock;
            return false;
        });

        $result = registerOwner(
            $connMock,
            "user",
            "pw",
            "pw",
            "Full Name",
            "email@example.com",
            "123456789",
            "2000-01-01",
            42
        );

        $this->assertEquals("Error preparing INSERT owner.", $result);
    }

    // Tests INSERT owner execute failure
    public function testInsertOwnerExecuteFailure()
    {
        $stmtSelectMock = $this->createMock(mysqli_stmt::class);
        $stmtSelectMock->method('get_result')->willReturn(new class {
            public $num_rows = 0;
        });
        $stmtSelectMock->method('execute')->willReturn(true);

        $stmtLoginInsertMock = $this->createMock(mysqli_stmt::class);
        $stmtLoginInsertMock->method('execute')->willReturn(true);

        $stmtOwnerInsertMock = $this->createMock(mysqli_stmt::class);
        $stmtOwnerInsertMock->method('execute')->willReturn(false); 

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturnCallback(function ($query) use ($stmtSelectMock, $stmtLoginInsertMock, $stmtOwnerInsertMock) {
            if (str_starts_with($query, 'SELECT')) return $stmtSelectMock;
            if (str_starts_with($query, 'INSERT INTO login')) return $stmtLoginInsertMock;
            return $stmtOwnerInsertMock;
        });

        $result = registerOwner(
            $connMock,
            "user",
            "pw",
            "pw",
            "Full Name",
            "email@example.com",
            "123456789",
            "2000-01-01",
            42
        );

        $this->assertEquals("Registration failed at owner insertion.", $result);
    }
}
