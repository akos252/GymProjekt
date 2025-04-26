<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../xampp/htdocs/projekt/includes/user_functions.php';

class UserFunctionsTest extends TestCase
{
    // Tests if password and confirm password don't match
    public function testPasswordsDoNotMatch()
    {
        $connMock = $this->createMock(mysqli::class);
        $result = registerUser($connMock, "user", "pass1", "pass2", "123", "2000-01-01");

        $this->assertEquals("Passwords do not match!", $result);
    }

    // Tests if user already exists, checks for existing username or mobilenum in database
    public function testUserAlreadyExists()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('get_result')->willReturn(new class {
            public $num_rows = 1;
        });

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);

        $stmtMock->method('bind_param')->willReturn(true);
        $stmtMock->method('execute')->willReturn(true);

        $result = registerUser($connMock, "existinguser", "pass", "pass", "123", "2000-01-01");

        $this->assertEquals("Username or mobile number already exists. Choose another!", $result);
    }

    // Tests for successful registration, checks for existing user and successful insert into login table
    public function testSuccessfulRegistration()
    {
        $stmtCheckMock = $this->createMock(mysqli_stmt::class);
        $stmtCheckMock->method('get_result')->willReturn(new class {
            public $num_rows = 0;
        });
        $stmtCheckMock->method('execute')->willReturn(true);
    
        $stmtInsertMock = $this->createMock(mysqli_stmt::class);
        $stmtInsertMock->method('execute')->willReturn(true);
    
        $connMock = $this->createMock(mysqli::class);
    
        $connMock->method('prepare')->willReturnCallback(function($query) use ($stmtCheckMock, $stmtInsertMock) {
            if (str_starts_with($query, 'SELECT')) {
                return $stmtCheckMock;
            } else {
                return $stmtInsertMock;
            }
        });
    
        $result = registerUser($connMock, "newuser", "pass", "pass", "123", "2000-01-01");
    
        $this->assertTrue($result);
    }

    // Tests if SELECT query preparation fails
    public function testSelectPrepareFailure()
    {
        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn(false);

        $result = registerUser($connMock, "user", "pass", "pass", "123", "2000-01-01");

        $this->assertNotTrue($result);
    }

    // Tests if SELECT execution fails
    public function testSelectExecuteFailure()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('bind_param')->willReturn(true);
        $stmtMock->method('execute')->willReturn(false);

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);

        $result = registerUser($connMock, "user", "pass", "pass", "123", "2000-01-01");

        $this->assertNotTrue($result);
    }

    // Tests if INSERT query preparation fails
    public function testInsertPrepareFailure()
    {
        $stmtCheckMock = $this->createMock(mysqli_stmt::class);
        $stmtCheckMock->method('get_result')->willReturn(new class {
            public $num_rows = 0;
        });
        $stmtCheckMock->method('execute')->willReturn(true);

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturnCallback(function($query) use ($stmtCheckMock) {
            if (str_starts_with($query, 'SELECT')) {
                return $stmtCheckMock;
            }
            return false;
        });

        $result = registerUser($connMock, "user", "pass", "pass", "123", "2000-01-01");

        $this->assertNotTrue($result);
    }

    // Tests if INSERT execution fails 
    public function testInsertExecuteFailure()
    {
        $stmtCheckMock = $this->createMock(mysqli_stmt::class);
        $stmtCheckMock->method('get_result')->willReturn(new class {
            public $num_rows = 0;
        });
        $stmtCheckMock->method('execute')->willReturn(true);

        $stmtInsertMock = $this->createMock(mysqli_stmt::class);
        $stmtInsertMock->method('execute')->willReturn(false);

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturnCallback(function($query) use ($stmtCheckMock, $stmtInsertMock) {
            if (str_starts_with($query, 'SELECT')) {
                return $stmtCheckMock;
            } else {
                return $stmtInsertMock;
            }
        });

        $result = registerUser($connMock, "user", "pass", "pass", "123", "2000-01-01");

        $this->assertEquals("Registration failed. Try again!", $result);
    }
}
