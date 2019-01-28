<?php
use Rcnchris\Common\Env;
use Rcnchris\Common\Items;
use Tests\Rcnchris\Common\BaseTestCase;

class EnvTest extends BaseTestCase
{
    /**
     * @var Env
     */
    private $e;

    public function setUp()
    {
        $this->e = $this->makeEnvironnement($_SERVER);
    }

    /**
     * @param array|null $server
     *
     * @return Env
     */
    public function makeEnvironnement($server = null)
    {
        return Env::getInstance($server);
    }

    public function testInstance()
    {
        $this->ekoTitre('Common - Env');
        $this->assertInstanceOf(Env::class, Env::getInstance());
        $this->assertInstanceOf(Env::class, new Env());
    }

    public function testHelp()
    {
        $this->assertHasHelp($this->e);
    }

    public function testGet()
    {
        $this->assertInstanceOf(Items::class, $this->e->get());
    }

    public function testMagicGet()
    {
        $this->assertInstanceOf(Items::class, $this->e->argv);
    }

    public function testGetWithKey()
    {
        $this->assertInstanceOf(Items::class, $this->e->get('argv'));
    }

    public function testUname()
    {
        $this->assertSimilar(`uname -a`, $this->e->getUname());
    }

    public function testUnameWithOption()
    {
        $this->assertSimilar(`uname -r`, $this->e->getUname('r'));
    }

//    public function testServerName()
//    {
//        $e = $this->makeEnvironnement(['SERVER_NAME' => 'testserver']);
//        $this->assertEquals('testserver', $e->getServerName());
//    }

    public function testApacheModules()
    {
        if (function_exists('apache_get_modules')) {
            $this->assertNotEmpty($this->e->getApacheModules()->toArray());
        } else {
            $this->assertTrue(true);
        }
    }

    public function testApacheUser()
    {
        $this->assertSimilar(`whoami`, $this->e->getWebUser());
    }

    public function testMysqlVersion()
    {
        $this->assertSimilar(`mysql -V`, $this->e->getMysqlVersion());
    }

    public function testPhpVersion()
    {
        $this->assertSimilar(PHP_VERSION, $this->e->getPhpVersion(false));
        $this->assertInternalType('string', $this->e->getPhpVersion(true));
    }

    public function testIniFile()
    {
        $this->assertInternalType('string', $this->e->getPhpIniFile());
    }

    public function testIniFiles()
    {
        $this->assertInstanceOf(Items::class, $this->e->getPhpIniFiles());
    }

    public function testIniFilesWithParameter()
    {
        $this->assertInstanceOf(Items::class, $this->e->getPhpIniFiles('curl'));
    }

    public function testPhpExtensions()
    {
        $this->assertNotEmpty($this->e->getPhpExtensions()->toArray());
    }

    public function testPhpModules()
    {
        $this->assertNotEmpty($this->e->getPhpModules()->toArray());
    }

    public function testPdoDrivers()
    {
        $this->assertContains('mysql', $this->e->getPdoDrivers()->toArray());
    }

    public function testTimezone()
    {
        $this->assertEquals('UTC', $this->e->getTimezone());
    }

    public function testTimezones()
    {
        $this->assertContains('Europe/Paris', $this->e->getTimezones()->toArray());
    }

    public function testLocales()
    {
        $this->assertContains('fr_FR', $this->e->getLocales()->toArray());
    }

    public function testCharset()
    {
        $this->assertSimilar('UTF-8', $this->e->getCharset());
    }

    public function testPhpErrorReporting()
    {
        $this->assertInternalType('integer', $this->e->getPhpErrorReporting());
    }

    public function testSapiName()
    {
        $this->assertEquals('cli', $this->e->getSapi());
    }

    public function testGitVersion()
    {
        $this->assertInternalType('string', $this->e->getGitVersion());
    }

    public function testCurlVersion()
    {
        $this->assertInternalType('string', $this->e->getCurlVersion());
    }

    public function testComposerVersion()
    {
        $this->assertInternalType('string', $this->e->getComposerVersion());
    }

    public function testWkhtmltopdfVersion()
    {
        $this->assertInternalType('string', $this->e->getWkhtmltopdfVersion());
    }

    public function testGetConstants()
    {
        $this->assertInstanceOf(Items::class, $this->e->getConstants());
    }

    public function testGetConstantsWithKey()
    {
        $this->assertInstanceOf(Items::class, $this->e->getConstants('Core'));
    }

    public function testGetUserAgent()
    {
        $browsers = [
            'Firefox' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0',
            'Opera' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36 OPR/57.0.3098.116',
            'Edge' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36 Edge/17.17134',
            'Chrome' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
            'PhpStorm' => 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.52 Safari/537.36',
            'PhpStormREST' => 'Apache-HttpClient/4.3.2 (java 1.5)'
        ];
        $this->assertNull($this->e->getUserAgent());
        $this->assertEquals($browsers['PhpStormREST'], $this->e->getUserAgent($browsers['PhpStormREST'], true));
        $this->assertEquals('Firefox', $this->e->getUserAgent($browsers['Firefox']));
        $this->assertEquals('Opera', $this->e->getUserAgent($browsers['Opera']));
        $this->assertEquals('Edge', $this->e->getUserAgent($browsers['Edge']));
        $this->assertEquals('Chrome', $this->e->getUserAgent($browsers['Chrome']));
        $this->assertEquals('Chrome', $this->e->getUserAgent($browsers['PhpStorm']));

    }

    public function testGetServername()
    {
        $this->assertNull($this->e->getServerName());
    }

    public function testGetApacheAdmin()
    {
        if ($this->e->has('SERVER_ADMIN')) {
            $this->assertInternalType('string', $this->e->getServerAdmin());
        } else {
            $this->markTestSkipped('Clé SERVER_ADMIN absente');
        }
    }

    public function testGetApachePortListen()
    {
        if ($this->e->has('SERVER_PORT')) {
            $this->assertEquals(80, $this->e->getServerPortListen());
        } else {
            $this->markTestSkipped('Clé SERVER_PORT absente');
        }
    }

    public function testHasFunction()
    {
        $this->assertTrue($this->e->hasFunction('var_dump'));
        $this->assertFalse($this->e->hasFunction('apache_get_modules'));
    }

    public function testHas()
    {
        $this->assertFalse($this->e->has('SERVER_PORT'));
    }

    public function testGetNewTimezone()
    {
        $this->assertInstanceOf(\DateTimeZone::class, $this->e->getNewTimezone());
    }

    public function testGetNewTimezoneWithParameter()
    {
        $this->assertInstanceOf(\DateTimeZone::class, $this->e->getNewTimezone('Europe/Paris'));
    }

    public function testGetLocale()
    {
        $this->assertInstanceOf(\Locale::class, $this->e->getLocale());
    }
}
