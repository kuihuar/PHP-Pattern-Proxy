<?php
require __DIR__ . '/../vendor/autoload.php';

class MockSubject
{
    const MESSAGE = "Hello from %s with a value arg of %d";

    public function mockCall($param)
    {
        return sprintf(self::MESSAGE, __CLASS__, $param);
    }
}

class Foo
{
    public function setSubjectObject() { }
}