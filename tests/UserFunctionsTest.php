<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../xampp/htdocs/projekt/includes/user_functions.php';

class UserFunctionsTest extends TestCase
{
    public function testPasswordsDoNotMatch()
    {
        $connMock = $this->createMock(mysqli::class);
        $result = registerUser($connMock, "user", "pass1", "pass2", "123", "2000-01-01");

        $this->assertEquals("Passwords do not match!", $result);
    }

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

    public function testSuccessfulRegistration()
    {
        // MOCK get_result: return 0 results (user nem létezik)
        $stmtCheckMock = $this->createMock(mysqli_stmt::class);
        $stmtCheckMock->method('get_result')->willReturn(new class {
            public $num_rows = 0;
        });

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
}
