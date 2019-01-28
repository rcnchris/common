<?php

use Rcnchris\Common\File;
use Tests\Rcnchris\Common\BaseTestCase;

class FileTest extends BaseTestCase
{
    public function makeFile($path)
    {
        return File::getInstance($path);
    }

    public function testInstance()
    {
        $this->ekoTitre('Common - File');
        $this->assertInstanceOf(File::class, File::getInstance(__FILE__));
        $this->assertInstanceOf(File::class, new File(__FILE__));
    }

    public function testHasHelp()
    {
        $this->assertHasHelp($this->makeFile(__FILE__));
    }

    public function testGetMimeWithPhpFile()
    {
        $this->assertEquals('text/x-php', $this->makeFile(__FILE__)->getMime());
    }

    public function testGetMimeWithDir()
    {
        $this->assertEquals('directory', $this->makeFile($this->rootPath())->getMime());
    }

    public function testIsDir()
    {
        $this->assertTrue($this->makeFile(__DIR__)->isDir());
        $this->assertFalse($this->makeFile(__FILE__)->isDir());
    }

    public function testGetContent()
    {
        $this->assertInternalType('string', $this->makeFile(__FILE__)->getContent());
    }

    public function testGetContentWithDir()
    {
        $this->assertEquals('', $this->makeFile(__DIR__)->getContent());
    }

    public function testGetContentWithUrl()
    {
        $this->assertInternalType('string', $this->makeFile('http://localhost/')->getContent());
    }

    public function testGetContentToArray()
    {
        $this->assertInternalType('array', $this->makeFile(__FILE__)->toArray());
    }

    public function testGetContentToArrayWithUrl()
    {
        $this->assertInternalType('array', $this->makeFile('http://localhost/')->toArray());
    }

    public function testGetLine()
    {
        $this->assertInternalType('string', $this->makeFile(__FILE__)->getLine(66));
    }

    public function testGetLineMissing()
    {
        $this->assertNull($this->makeFile(__FILE__)->getLine(99999));
    }

    public function testMagicToString()
    {
        $this->assertInternalType('string', (string)$this->makeFile(__FILE__));
    }

    public function testMagicToStringWithDir()
    {
        $this->assertInternalType('string', (string)$this->makeFile(__DIR__));
    }

    public function testCountableInterface()
    {
        $this->assertInternalType('integer', $this->makeFile($this->rootPath() . '/tests/')->count());
    }
}
