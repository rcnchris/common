<?php
use Rcnchris\Common\Curl;
use Tests\Rcnchris\Common\BaseTestCase;

class CurlTest extends BaseTestCase
{

    /**
     * @var Curl
     */
    private $curl;

    /**
     * URLs de base des APIs
     *
     * @var array
     */
    private $baseUrls = [
        'geo' => 'https://geo.api.gouv.fr',
        'user' => 'https://randomuser.me/api/',
        'image' => 'http://placekitten.com/200/300'
    ];

    public function setUp()
    {
        $this->curl = $this->makeCurl($this->baseUrls['geo']);
    }

    public function testHelp()
    {
        $this->ekoTitre('Common - Curl');
        $this->assertHasHelp($this->makeCurl($this->baseUrls['geo']));
    }

    /**
     * @param string|null $url
     *
     * @return Curl
     */
    public function makeCurl($url = null)
    {
        return new Curl($url);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Curl::class, $this->makeCurl());

        foreach ($this->baseUrls as $name => $url) {
            $curl = $this->makeCurl($url);
            $this->assertInstanceOf(Curl::class, $curl);
        }
    }

    public function testSetUrl()
    {
        $url = $this->baseUrls['geo'];
        $curl = $this->makeCurl();
        $curl->setUrl($url);
        $this->assertEquals($url, $curl->getBaseUrl());
        $this->assertEquals($url, $curl->getUrl());
    }

    public function testSetUrlWithWrongUrl()
    {
        $url = 'https://geo.api.go uv.fr';
        $curl = $this->makeCurl();
        $this->assertFalse($curl->setUrl($url));
    }

    public function testGetCurl()
    {
        $this->assertInternalType('resource', $this->curl->getCurl());
    }

    public function testParseUrl()
    {
        $this->assertInternalType('array', $this->curl->parseUrl());
        $this->assertEquals('https', $this->curl->parseUrl('scheme'));
        $this->assertNull($this->curl->parseUrl('fake'));
    }

    public function testGetParams()
    {
        $curl = $this->makeCurl('https://www.google.com/search?client=firefox-b-d&q=php');
        $this->assertInternalType('array', $curl->getParams());
        $this->assertInternalType('string', $curl->getParams(true));
    }

    public function testExec()
    {
        $curl = $this->makeCurl($this->baseUrls['user']);

        $this->assertInstanceOf(Curl::class, $curl);
        $this->assertFalse($curl->getParams());
        $response = $curl->exec('Utilisateur');
        $this->assertInternalType('string', $response);

        $this->assertInternalType('array', $curl->getLog());
        $this->assertCount(1, $curl->getLog());

        $this->assertEquals(200, $curl->getInfos('http_code'));
    }

    public function testExecWithParts()
    {
        $curl = $this->makeCurl($this->baseUrls['geo']);

        $this->assertInstanceOf(Curl::class, $curl);
        $curl->withParts('regions');
        $response = $curl->exec('Liste des régions');
        $this->assertInternalType('string', $response);
        $this->assertEquals(200, $curl->getInfos('http_code'));
    }

    public function testExecWithSeparateParts()
    {
        $curl = $this->makeCurl($this->baseUrls['geo']);

        $this->assertInstanceOf(Curl::class, $curl);
        $curl->withParts('regions/93');
        $response = $curl->exec('Infos sur PACA');
        $this->assertInternalType('string', $response);
        $this->assertEquals(200, $curl->getInfos('http_code'));
    }

    public function testExecWithArrayParts()
    {
        $curl = $this->makeCurl($this->baseUrls['geo']);

        $this->assertInstanceOf(Curl::class, $curl);
        $curl->withParts(['regions', '93']);
        $response = $curl->exec('Infos sur PACA');
        $this->assertInternalType('string', $response);
        $this->assertEquals(200, $curl->getInfos('http_code'));
    }

    public function testExecWithParams()
    {
        $curl = $this->makeCurl($this->baseUrls['geo']);

        $this->assertInstanceOf(Curl::class, $curl);
        $curl->withParts('departements')->withParams(['code' => 83], true);
        $response = $curl->exec('Départements');
        $this->assertInternalType('string', $response);
        $this->assertEquals(200, $curl->getInfos('http_code'));
    }

    public function testExecWithUserAgent()
    {
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:66.0) Gecko/20100101 Firefox/66.0';
        $curl = $this->makeCurl($this->baseUrls['geo']);
        $response = $curl->withParts('regions')->withUserAgent($ua)->exec('Région avec navigateur');
        $this->assertInternalType('string', $response);
        $this->assertEquals(200, $curl->getInfos('http_code'));
    }

    public function testGetUrl()
    {
        $curl = $this->makeCurl($this->baseUrls['geo']);
        $this->assertEquals($this->baseUrls['geo'], $curl->getUrl());
        $this->assertEquals($this->baseUrls['geo'], $curl->getUrl(true));
    }

    public function testSetBaseUrl()
    {
        $curl = $this->makeCurl($this->baseUrls['geo']);
        $curl->withParts('regions');
        $this->assertNotEquals($curl->getUrl(), $curl->getBaseUrl());
        $curl->setBaseUrl();
        $this->assertEquals($this->baseUrls['geo'], $curl->getBaseUrl());
    }
}
