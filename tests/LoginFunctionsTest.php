<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../xampp/htdocs/projekt/includes/login_functions.php';

use PHPUnit\Framework\TestCase;

class LoginFunctionsTest extends TestCase
{
    // Test if login is successful checks for correct username and password
    public function testLoginSuccessful()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('get_result')->willReturn(new class {
            public function fetch_assoc() {
                return [
                    'id' => 1,
                    'username' => 'testuser',
                    'user_type' => 'member'
                ];
            }
        });
        $stmtMock->method('execute')->willReturn(true);

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);

        $result = loginUser($connMock, "testuser", "password");

        $this->assertIsArray($result);
        $this->assertEquals('testuser', $result['username']);
    }

    // Tests if login is unsuccessful checks for wrong username or password
    public function testLoginFailed()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('get_result')->willReturn(new class {
            public function fetch_assoc() {
                return null;
            }
        });
        $stmtMock->method('execute')->willReturn(true);

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);

        $result = loginUser($connMock, "wronguser", "wrongpass");

        $this->assertEquals("Invalid credentials", $result);
    }

    // Tests if prepare() fails
    public function testLoginPrepareFailure()
    {
        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn(false);

        $result = loginUser($connMock, "anyuser", "anypass");

        $this->assertEquals("Database error.", $result);
    }

    // Tests if execute() fails
    public function testLoginExecuteFailure()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('execute')->willReturn(false);
    
        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);
    
        $result = loginUser($connMock, "anyuser", "anypass");
    
        $this->assertEquals("Database error during execute.", $result);
    }

    // Tests if get_result() fails
    public function testLoginGetResultFailure()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('get_result')->willReturn(null);
    
        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);
    
        $result = loginUser($connMock, "anyuser", "anypass");
    
        $this->assertEquals("Database error getting result.", $result);
    }
    
}
