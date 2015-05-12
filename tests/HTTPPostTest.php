<?php

class HTTPPostTest extends TestCase
{
    public function test400ReponseWhenNoModeSet()
    {
        $this->call('POST', '/');

        $this->assertResponseStatus(400);
    }

    public function test501ResponseFromUnkownMode()
    {
        $this->call('POST', '/', ['hub_mode' => 'unkown']);

        $this->assertResponseStatus(501);
    }
}
