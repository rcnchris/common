<?php
use Rcnchris\Common\Curl;
use Rcnchris\Common\Image;
use Rcnchris\Common\Items;
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
        'user' => 'https://randomuser.me/api',
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
        $this->ekoInfo("Obtenir un utilisateur aléatoire");
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
        $this->ekoInfo("Ajouter des parties");
        $curl = $this->makeCurl($this->baseUrls['geo']);

        $this->assertInstanceOf(Curl::class, $curl);
        $curl->withParts('regions');
        $response = $curl->exec('Liste des régions');
        $this->assertInternalType('string', $response);
        $this->assertEquals(200, $curl->getInfos('http_code'));
    }

    public function testExecWithSeparateParts()
    {
        $this->ekoInfo("Ajouter plusieurs parties");
        $curl = $this->makeCurl($this->baseUrls['geo']);

        $this->assertInstanceOf(Curl::class, $curl);
        $curl->withParts('regions/93');
        $response = $curl->exec('Infos sur PACA');
        $this->assertInternalType('string', $response);
        $this->assertEquals(200, $curl->getInfos('http_code'));
    }

    public function testExecWithArrayParts()
    {
        $this->ekoInfo("Ajouter plusieurs parties dans un tableau");
        $curl = $this->makeCurl($this->baseUrls['geo']);

        $this->assertInstanceOf(Curl::class, $curl);
        $curl->withParts(['regions', '93']);
        $response = $curl->exec('Infos sur PACA');
        $this->assertInternalType('string', $response);
        $this->assertEquals(200, $curl->getInfos('http_code'));
    }

    public function testExecWithParams()
    {
        $this->ekoInfo("Ajouter des paramètres");
        $curl = $this->makeCurl($this->baseUrls['geo']);

        $this->assertInstanceOf(Curl::class, $curl);
        $curl->withParts('departements')->withParams(['code' => 83], true);
        $response = $curl->exec('Départements');
        $this->assertInternalType('string', $response);
        $this->assertEquals(200, $curl->getInfos('http_code'));
    }

    public function testExecWithUserAgent()
    {
        $this->ekoInfo("Utiliser un navigateur particulier");
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:66.0) Gecko/20100101 Firefox/66.0';
        $curl = $this->makeCurl($this->baseUrls['geo']);
        $response = $curl->withParts('regions')->withUserAgent($ua)->exec('Région avec navigateur');
        $this->assertInternalType('string', $response);
        $this->assertEquals(200, $curl->getInfos('http_code'));
    }

    public function testGetUrl()
    {
        $this->ekoInfo("Obtenir l'URL courante");
        $curl = $this->makeCurl($this->baseUrls['geo']);
        $this->assertEquals($this->baseUrls['geo'], $curl->getUrl());
        $this->assertEquals($this->baseUrls['geo'], $curl->getUrl(true));
    }

    public function testSetBaseUrl()
    {
        $this->ekoInfo("Définir l'URL de base");
        $curl = $this->makeCurl($this->baseUrls['geo']);
        $curl->withParts('regions');
        $this->assertNotEquals($curl->getUrl(), $curl->getBaseUrl());
        $curl->setBaseUrl();
        $this->assertEquals($this->baseUrls['geo'], $curl->getBaseUrl());
    }

    public function testGetContentType()
    {
        $this->ekoInfo("Obtenir le Content-type de la réponse");
        $curl = $this->makeCurl($this->baseUrls['geo']);
        $curl->withParts('regions')->exec('Régions');
        $this->assertEquals('application/json', $curl->getContentType());
    }

    public function testToObjectWithTextHtml()
    {
        $this->ekoInfo("Obtenir du texte à partir d'une réponse au format text/html");
        $curl = $this->makeCurl('http://php.net/manual/fr/');
        $curl->exec();
        $this->assertInternalType('string', $curl->toObject());
    }

    public function testToObjectWithJson()
    {
        $this->ekoInfo("Obtenir un objet " . Items::class . " à partir d'une réponse au format application/json");
        $curl = $this->makeCurl($this->baseUrls['user']);
        $curl->exec();
        $this->assertInstanceOf(Items::class, $curl->toObject());
    }

    public function testToObjectWithPrettyJson()
    {
        $curl = $this->makeCurl($this->baseUrls['user']);
        $curl->withParams(['format' => 'pretty'])->exec();
        $this->assertInstanceOf(Items::class, $curl->toObject());
    }

    public function testToObjectWithCsv()
    {
        $this->ekoInfo("Obtenir un objet " . Items::class . " à partir d'une réponse au format text/csv");
        $curl = $this->makeCurl($this->baseUrls['user']);
        $curl->withParams(['format' => 'csv'])->exec();
        $this->assertInstanceOf(Items::class, $curl->toObject());
    }

    public function testToObjectWithXml()
    {
        $this->ekoInfo("Obtenir un objet " . \SimpleXMLElement::class . " à partir d'une réponse au format text/xml");
        $curl = $this->makeCurl($this->baseUrls['user']);
        $curl->withParams(['format' => 'xml'])->exec();
        $this->assertInstanceOf(\SimpleXMLElement::class, $curl->toObject());
    }

    public function testToObjectWithYml()
    {
        $this->ekoInfo("Obtenir un objet " . Items::class . " à partir d'une réponse au format yml");
        $curl = $this->makeCurl($this->baseUrls['user']);
        $curl->withParams(['format' => 'yml'])->exec();
        $this->assertInstanceOf(Items::class, $curl->toObject());
    }

    public function testToObjectWithImage()
    {
        $this->ekoInfo("Obtenir un objet " . Image::class . " à partir d'une réponse au format image/jpeg");
        $curl = $this->makeCurl($this->baseUrls['image']);
        $curl->exec();
        $this->assertInstanceOf(Image::class, $curl->toObject());
    }
}
