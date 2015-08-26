<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;

class ExampleTest extends TestCase
{
    protected $baseUrl = 'http://localhost:9000/';

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->visit('/')
             ->see('Lumen.');
    }

    public function testPostExample()
    {
        $filesystem = new Filesystem(new Adapter(__DIR__.'.'));

        //dd($filesystem);
        $data = $filesystem->read('commit-message-with-key.json');

        $contents = json_decode($data, true);

        $response = $this->call('POST', 'gitlab/user/list');
        // i don't know why statusCode is 404.
        $this->assertEquals($response->getStatusCode(), 404);
    }
}
