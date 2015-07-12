<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;

class ExampleTest extends TestCase
{
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
        //dd($ar);
        $this->post('/gitlab', compact('contents'))
             ->seeJson([
                'created' => true,
             ]);
    }
}
