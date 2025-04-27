<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../xampp/htdocs/projekt/includes/membership_functions.php';

class MembershipFunctionsTest extends TestCase
{
    // Tests successful gym detail fetch
    public function testFetchGymDetailsSuccess()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('get_result')->willReturn(new class {
            public function fetch_assoc() {
                return ['gym_name' => 'Test Gym'];
            }
        });

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);

        $gym = fetchGymDetails($connMock, 1);

        $this->assertIsArray($gym);
        $this->assertEquals('Test Gym', $gym['gym_name']);
    }

    // Tests fetch gym details failure
    public function testFetchGymDetailsFailure()
    {
        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn(false);

        $gym = fetchGymDetails($connMock, 1);

        $this->assertNull($gym);
    }

    // Tests hasActiveMembership returning true
    public function testHasActiveMembershipTrue()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('get_result')->willReturn(new class {
            public $num_rows = 1;
        });

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);

        $hasMembership = hasActiveMembership($connMock, 1, 1);

        $this->assertTrue($hasMembership);
    }

    // Tests hasActiveMembership returning false
    public function testHasActiveMembershipFalse()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('get_result')->willReturn(new class {
            public $num_rows = 0;
        });

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);

        $hasMembership = hasActiveMembership($connMock, 1, 1);

        $this->assertFalse($hasMembership);
    }

    // Tests successful membership purchase
    public function testPurchaseMembershipSuccess()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('execute')->willReturn(true);

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);

        $purchase = purchaseMembership($connMock, 1, 1, 'Test User', 30);

        $this->assertTrue($purchase);
    }

    // Tests failed membership purchase
    public function testPurchaseMembershipFailure()
    {
        $stmtMock = $this->createMock(mysqli_stmt::class);
        $stmtMock->method('execute')->willReturn(false);

        $connMock = $this->createMock(mysqli::class);
        $connMock->method('prepare')->willReturn($stmtMock);

        $purchase = purchaseMembership($connMock, 1, 1, 'Test User', 30);

        $this->assertFalse($purchase);
    }
}
