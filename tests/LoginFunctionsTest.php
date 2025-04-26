<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../xampp/htdocs/projekt/includes/login_functions.php';

use PHPUnit\Framework\TestCase;

class LoginFunctionsTest extends TestCase
{
    //Test if login is successful. Checks for correct username and password. Return should be['id' => 1, 'username' => 'testuser', 'user_type' => 'member'].
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

    //Tests if login is unsuccessful. Checks for wrong username or password. Return should be "Invalid credentials".
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
}
