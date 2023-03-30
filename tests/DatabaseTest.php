<?php
use api\config\Database;
class DatabaseTest extends \PHPUnit\Framework\TestCase
{
    public function testConnect()
    {
        $database = new Database;
        $this->assertEquals(null, $database->connect());
    }
}
