<?php
use Rcnchris\Common\Items;
use Rcnchris\Common\Url;
use Tests\Rcnchris\Common\BaseTestCase;

class UrlTest extends BaseTestCase
{
    /**
     * @param string|null $url
     *
     * @return Url
     */
    public function makeUrl($url = null)
    {
        return new Url($url);
    }

    public function testInstanceWithoutParameter()
    {
        $this->ekoTitre('Common - URL');
        $this->assertInstanceOf(Url::class, $this->makeUrl());
    }

    public function testInstanceWith()
    {
        $this->assertInstanceOf(Url::class, $this->makeUrl('https://randomuser.me/api/'));
    }

    public function testHasHelp()
    {
        $this->assertHasHelp($this->makeUrl('http://php.net/manual/fr/'));
    }

    public function testMagicToString()
    {
        $url = $this->makeUrl('http://php.net/manual/fr/');
        $this->assertEquals('http://php.net/manual/fr/', (string)$url);
    }

    public function testMagicGet()
    {
        $url = $this->makeUrl('http://php.net/manual/fr/');
        $this->assertEquals('php.net', $url->host);
    }

    public function testQueries()
    {
        $url = $this->makeUrl('https://www.google.com/search?client=firefox-b-d&q=php');
        $this->assertInstanceOf(Items::class, $url->queries());
        $this->assertEquals(2, $url->queries()->count());
    }
}
