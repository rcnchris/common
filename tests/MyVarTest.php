<?php
/**
 * Fichier MyVarTest.php du 28/01/2019
 * Description : Fichier de la classe MyVarTest
 *
 * PHP version 5
 *
 * @category New
 *
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @link     https://github.com/rcnchris On Github
 */
use Rcnchris\Common\Folder;
use Rcnchris\Common\Items;
use Rcnchris\Common\MyVar;
use Tests\Rcnchris\Common\BaseTestCase;

class MyVarTest extends BaseTestCase
{
    /**
     * Variables de test
     *
     * @var array
     */
    public $vars = [];

    /**
     * @var array
     */
    public $item;

    /**
     * @param $var
     *
     * @return Myvar
     */
    private function makeVar($var)
    {
        return new Myvar($var);
    }

    public function setUp()
    {
        $this->vars = [
            'string' => 'ola les gens',
            'integer' => 12,
            'double' => 12.56,
            'array' => [
                ['name' => 'Mathis', 'year' => 2007, 'genre' => 'male'],
                ['name' => 'Raphaël', 'year' => 2007, 'genre' => 'male'],
                ['name' => 'Clara', 'year' => 2009, 'genre' => 'female']
            ],
            'object' => new Items([
                ['name' => 'Mathis', 'year' => 2007, 'genre' => 'male'],
                ['name' => 'Raphaël', 'year' => 2007, 'genre' => 'male'],
                ['name' => 'Clara', 'year' => 2009, 'genre' => 'female']
            ]),
            'resource' => curl_init('http://fake.com')
        ];
        $this->item = ['name' => 'Mathis', 'year' => 2007, 'genre' => 'male'];
    }

    public function testInstance()
    {
        $this->ekoTitre('Common - MyVar');
        $this->assertInstanceOf(Myvar::class, $this->makeVar('ola'));
    }

    public function testHelp()
    {
        $this->assertHasHelp($this->makeVar('ola'));
    }

    public function testGetType()
    {
        foreach ($this->vars as $type => $value) {
            $this->assertEquals(
                $type
                , $this->makeVar($value)->getType()
            );
        }
    }

    public function testIsType()
    {
        foreach ($this->vars as $type => $value) {
            $methodName = 'is' . ucfirst($type);
            $this->assertTrue(
                $this->makeVar($value)->$methodName()
                , "Le type vérifié ne répond pas correctement ($methodName)"
            );
        }
    }

    public function testLength()
    {
        $this->assertEquals(
            12
            , $this->makeVar($this->vars['string'])->length()
            , "La longueur de la chaîne de caractères est incorrecte"
        );

        $this->assertEquals(
            3,
            $this->makeVar($this->vars['array'])->length(),
            "La longueur du tableau est incorrecte"
        );

        $this->assertEquals(
            2,
            $this->makeVar($this->vars['integer'])->length(),
            "La longueur de l'entier est incorrecte"
        );

        $this->assertEquals(
            5,
            $this->makeVar($this->vars['double'])->length(),
            "La longueur du double est incorrecte"
        );

        $this->assertEquals(
            0,
            $this->makeVar($this->vars['resource'])->length(),
            "La longueur de la ressource est incorrecte"
        );
    }

    public function testGetResourceType()
    {
        $this->assertEquals(
            'curl'
            , $this->makeVar($this->vars['resource'])->getResourceType()
            , "Une resource curl n'est pas considérée comme telle"
        );
        $this->assertFalse(
            $this->makeVar($this->vars['array'])->getResourceType()
            , 'Un tableau retourne un type de ressource'
        );
    }

    public function testIsNum()
    {
        $this->assertTrue(
            $this->makeVar($this->vars['double'])->isNum()
            , "Une valeur double n'est pas considérée comme numérique"
        );

        $this->assertFalse(
            $this->makeVar($this->vars['string'])->isNum()
            , "Une chaîne de caractères sans chiffre est considérée comme numérique"
        );
    }

    public function testToString()
    {
        $this->assertEquals(
            $this->vars['string']
            , (string)$this->makeVar($this->vars['string'])
            , "Une chaîne de caractères n'est pas égale à elle même"
        );

        $this->assertEquals(
            json_encode($this->vars['array'])
            , (string)$this->makeVar($this->vars['array'])
            , "L'export du tableau au format string est incorrect"
        );

        $this->assertEquals(
            serialize(['ola', 'ole', 'oli'])
            , (string)$this->makeVar(new Items(['ola', 'ole', 'oli']))
            , "L'export de l'objet au format string est incorrect"
        );

        $this->assertEquals(
            '12'
            , (string)$this->makeVar($this->vars['integer'])
            , "L'export de l'entier au format string est incorrect"
        );
    }

    public function testGet()
    {
        foreach ($this->vars as $type => $value) {
            $this->assertEquals(
                $this->vars[$type],
                $this->makeVar($value)->get(),
                "La valeur obtenue est différente de la valeur initiale"
            );
        }

        $this->assertEquals(
            'Mathis',
            $this->makeVar(['name' => 'Mathis', 'year' => 2007, 'genre' => 'male'])->get('name'),
            "La valeur de la clé demandée est incorrecte"
        );

        $o = new \stdClass();
        $o->name = 'Mathis';
        $o->year = 2007;
        $o->genre = 'male';
        $this->assertEquals(
            'Mathis'
            , $this->makeVar($o)->get('name')
            , "La valeur de la clé demandée à l'objet est incorrecte"
        );

        $this->assertFalse(
            $this->makeVar($o)->get('fake')
            , "L'appel d'une clé, méthode ou propriété sur l'objet est incorrect"
        );

        $this->assertInternalType('integer', $this->makeVar($this->vars['object'])->get('count'));
    }

    public function testGetMethods()
    {
        $this->assertNotEmpty(
            $this->makeVar(new Items(['ola', 'ole', 'oli']))->getMethods()
            , "La liste des méthodes de l'objet est incorrecte"
        );

        $this->assertFalse(
            $this->makeVar($this->vars['string'])->getMethods()
            , "La liste des méthodes de l'objet est incorrecte"
        );

        $this->assertNotEmpty(
            $this->makeVar($this)->getMethods(true),
            "La liste des méthodes de l'objet est incorrecte"
        );

    }

    public function testGetParent()
    {
        $this->assertEquals(
            BaseTestCase::class
            , $this->makeVar($this)->getParent()
            , "La classe parente de l'objet ne devrait pas être vide"
        );

        $this->assertFalse(
            $this->makeVar($this->vars['string'])->getParent()
            , "La classe parente d'une chaîne de caractères ne devrait pas exister"
        );
    }

    public function testGetImplements()
    {
        $this->assertNotEmpty(
            $this->makeVar(new Items(['ola', 'ole', 'oli']))->getImplements()
            , "La liste des implémentations de l'objet ne devrait pas être vide"
        );

        $this->assertFalse(
            $this->makeVar($this->vars['string'])->getImplements()
            , "La liste des implémentations de l'objet devrait être vide"
        );
    }

    public function testGetTraits()
    {
        $this->assertEmpty(
            $this->makeVar($this)->getTraits()
            , "Cet objet ne devrait pas avoir de traits"
        );

        $this->assertFalse(
            $this->makeVar('ola')->getTraits()
            , "Une chaîne de caractères ne peut pas avoir de trait"
        );
    }

    public function testGetProperties()
    {
        $this->assertFalse(
            $this->makeVar($this->vars['string'])->getProperties()
            , "Une chaîne de caractères ne peut pas retourner des propriétés"
        );

        $o = new Folder(__DIR__);
        $this->assertEquals(
            ['path' => __DIR__]
            , $this->makeVar($o)->getProperties()
            , "Les propriétés retournées par un objet sont incorrectes"
        );

        $this->assertNotEmpty(
            $this->makeVar($this)->getProperties()
            , "La liste des propriétés de l'objet est incorrecte"
        );

        $this->assertEquals(
            ['name', 'year', 'genre']
            , $this->makeVar($this->item)->getProperties()
            , "La liste des propriétés du tableau ne correspond pas aux noms de clés du tableau"
        );
    }

    public function testGetClass()
    {
        $c = new Items('ola, ole, oli');
        $this->assertEquals('Rcnchris\Common\Items', $this->makeVar($c)->getClass());
        $this->assertEquals('Items', $this->makeVar($c)->getClass(true));
    }

    public function testToInt()
    {
        $this->assertInternalType('integer', $this->makeVar('123')->toInt());
    }

    public function testToBool()
    {
        $this->assertInternalType('bool', $this->makeVar('1')->toBool());
    }
}
