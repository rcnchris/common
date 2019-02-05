<?php
/**
 * Fichier BaseTestCase.php du 25/01/2019
 * Description : Fichier de la classe BaseTestCase
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
namespace Tests\Rcnchris\Common;

use PHPUnit\Framework\TestCase;


/**
 * Class BaseTestCase
 *
 * @category New
 *
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @version  Release: <1.0.0>
 *
 * @link     https://github.com/rcnchris on Github
 */
class BaseTestCase extends TestCase
{
    /**
     * Mapping des interfaces et leur méthodes
     *
     * @var array
     */
    protected $mapMethodsInterfaces = [
        'ArrayAccess' => ['offsetExists', 'offsetGet', 'offsetSet', 'offsetUnset'],
        'Countable' => ['count'],
        'IteratorAggregate' => ['getIterator'],
        'Serializable' => ['serialize', 'unserialize'],
    ];

    /**
     * Affiche un titre coloré dans la console
     *
     * @param string $titre Titre
     * @param bool   $isTest
     */
    protected function ekoTitre($titre = '', $isTest = false)
    {
        $methods = get_class_methods(get_class($this));
        $tests = array_map(function ($method) {
            return substr($method, 0, 4) === 'test' ? $method : null;
        }, $methods);
        $tests = count(array_filter($tests));
        if ($isTest === true) {
            $tests--;
        }
        $parts = explode(' - ', $titre);
        echo "\n\033[0;36m{$parts[0]}\033[m - {$parts[1]} (\033[0;32m$tests\033[m)\n";
    }

    /**
     * Obtenir le chemin racine du projet
     *
     * @return string
     */
    protected function rootPath()
    {
        return dirname(__DIR__);
    }

    /**
     * Supprime les espaces et retours à la ligne
     *
     * @param $string
     *
     * @return string
     */
    protected function trim($string)
    {
        $lines = explode(PHP_EOL, $string);
        $lines = array_map('trim', $lines);
        return implode('', $lines);
    }

    /**
     * Compare deux expressions en utilisant le trim de cette classe
     *
     * @param string $expected Chaîne de caractères à comparer
     * @param string $actual   Chaîne de caractères à comparer
     */
    protected function assertSimilar($expected, $actual)
    {
        $this->assertEquals($this->trim($expected), $this->trim($actual));
    }

    /**
     * Vérifie la présence d'une liste de méthodes dans un objet
     *
     * @param object $object  Instance de l'objet
     * @param array  $methods Liste des méthodes dont i lfaut vérifier la présence
     */
    public function assertObjectHasMethods($object, array $methods)
    {
        foreach ($methods as $methodName) {
            $this->assertTrue(
                method_exists($object, $methodName),
                "La méthode $methodName n'existe pas dans la clase " . get_class($object)
            );
        }
    }

    /**
     * Vérifie le comportement d'un objet qui implémente ArrayAccess
     *
     * ### Exemple
     * - `$this->assertArrayAccess($result, 1, ['id' => 2, 'title' => 'Page'], ['Exists', 'Get']);`
     *
     * @param object     $object  Instance de l'objet à tester
     * @param string     $key     Nom d'une clé du tableau
     * @param mixed      $expect  Valeur attendue
     * @param array|null $methods Liste des méthodes à tester
     */
    protected function assertArrayAccess($object, $key, $expect, array $methods = [])
    {
        $interfaceName = 'ArrayAccess';
        $class = get_class($object);

        $this->assertArrayHasKey(
            $interfaceName, class_implements($object),
            "L'instance de $class n'implémente pas l'interface $interfaceName"
        );

        if (empty($methods)) {
            $methods = $this->mapMethodsInterfaces[$interfaceName];
        } else {
            $methods = array_map(function ($method) {
                return 'offset' . ucfirst($method);
            }, $methods);
        }

        foreach ($methods as $method) {
            if ($method === 'offsetExists') {
                $this->assertTrue(
                    isset($object[$key]),
                    "Le comportement de $interfaceName est incorrect dans le cas $method pour $class"
                );
            }
            if ($method === 'offsetGet') {
                $this->assertEquals(
                    $expect,
                    $object[$key],
                    "Le comportement de $interfaceName est incorrect dans le cas $method pour $class"
                );
            }
            if ($method === 'offsetSet') {
                $object[$key] = $expect;
                $this->assertEquals(
                    $expect,
                    $object[$key],
                    "Le comportement de $interfaceName est incorrect dans le cas $method pour $class"
                );
            }
            if ($method === 'offsetUnset') {
                unset($object[$key]);
                $this->assertFalse(
                    isset($object[$key]),
                    "Le comportement de $interfaceName est incorrect dans le cas $method pour $class"
                );
            }
        }
    }

    public function assertHasHelp($o)
    {
        $this->assertObjectHasAttribute('help', $o);
        $this->assertObjectHasMethods($o, ['help']);
        $this->assertInternalType('string', $o->help());
        $this->assertInternalType('array', $o->help(false));
    }

    /**
     * Vérifie la présene d'une liste de clés dans un tableau
     *
     * @param array $keys  Liste des clés à vérifier
     * @param array $array Tableau à vérifier
     */
    public function assertArrayHasKeys(array $keys, array $array)
    {
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $array, "La clé $key n'existe pas dans le tableau");
        }
    }
}
