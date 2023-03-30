<?php
class DatabaseTest extends \PHPUnit\Framework\TestCase
{
    public function testConnect()
    {
        $database = new api\config\Database;
        $this->assertEquals(null, $database->connect());
    }
}
