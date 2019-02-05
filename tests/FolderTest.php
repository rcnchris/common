<?php

use Rcnchris\Common\File;
use Rcnchris\Common\Folder;
use Rcnchris\Common\Items;
use Tests\Rcnchris\Common\BaseTestCase;

class FolderTest extends BaseTestCase
{
    public function testInstance()
    {
        $this->ekoTitre('Common - Folder');
        $path = dirname(__DIR__);
        $this->assertInstanceOf(Folder::class, Folder::getInstance($path));
        $this->assertInstanceOf(Folder::class, new Folder($path));
    }

    public function testHelp()
    {
        $this->assertHasHelp(Folder::getInstance(__DIR__));
    }

    public function testContentClass()
    {
        $this->assertInstanceOf(Items::class, Folder::getInstance(__DIR__)->content());
    }

    public function testToArray()
    {
        $this->assertInternalType('array', Folder::getInstance(__DIR__)->toArray());
    }

    public function testGetWithFolder()
    {
        $path = dirname(__DIR__);
        $this->assertInstanceOf(
            Folder::class,
            Folder::getInstance($path)->get('tests')
        );
    }

    public function testGetWithFile()
    {
        $path = __DIR__;
        $this->assertInstanceOf(
            File::class,
            Folder::getInstance($path)->get(basename(__FILE__))
        );
    }

    public function testGetWithFake()
    {
        $this->assertNull(Folder::getInstance(__DIR__)->get('fake'));
    }

    public function testCountable()
    {
        $this->assertInternalType('int', Folder::getInstance(dirname(__DIR__))->count());
    }
}
