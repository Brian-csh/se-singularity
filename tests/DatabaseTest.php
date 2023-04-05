<?php
require __DIR__ . "/../api/config/Database.php";
class DatabaseTest extends \PHPUnit\Framework\TestCase
{
    // public function assertNotEqualsCustom($expected, $actual, $message = '')
    // {
    //     if ($expected === $actual) {
    //         $defaultMessage = sprintf(
    //             'Failed asserting that %s matches expected %s.',
    //             var_export($actual, true),
    //             var_export($expected, true)
    //         );
    //         if ($message !== '') {
    //             $message = $message . "\n" . $defaultMessage;
    //         } else {
    //             $message = $defaultMessage;
    //         }
    //         $this->fail($message);
    //     }
    // }
    public function testConnect()
    {
        $database = new Database();
        $this->assertNotEquals(null, $database->connect());
    }
}
