<?php
namespace Tests\Rcnchris\Common;

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    /**
     * Affichage console bavard
     * 0 : Rien
     * 1 : Les titres
     * 2 : Les titres et les infos
     */
    const VERBOSE = 2;

    /**
     * Chemin des fichiers de tests
     *
     * @var string
     */
    protected $pathFiles = __DIR__ . '/files';

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
     * Nom des méthodes magiques de PHP
     *
     * @var array
     */
    protected $magicMethods = [
        '__get',
        '__call'
    ];

    /**
     * Affiche un titre coloré dans la console
     *
     * @param string $titre Titre
     * @param bool   $isTest
     *
     * @return void
     */
    protected function ekoTitre($titre = '', $isTest = false)
    {
        if ($this::VERBOSE < 1) {
            return null;
        }
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
     * Affiche un message d'information à la console
     *
     * @param string $content Contenu du message
     *
     * @return void
     */
    protected function ekoInfo($content)
    {
        if ($this::VERBOSE < 2) {
            return null;
        }
        echo "\n\033[0;35m{$content}\033[m \n";
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
     * Vérifier qu'un objet implémente l'interface Countable
     *
     * @param object $object Objet à instance de l'objet à vérifier
     * @param int    $count  Valeur attendue
     */
    protected function assertImplementCountable($object, $count)
    {
        $interfaceName = 'Countable';
        $class = get_class($object);
        $this->ekoInfo($class . " implémente Countable");
        $this->assertArrayHasKey(
            $interfaceName,
            class_implements($object),
            "L'instance de $class n'implémente pas l'interface $interfaceName"
        );
        $this->assertObjectHasMethods($object, ['count']);
        $this->assertEquals($count, $object->count());
    }

    /**
     * Vérifier qu'un objet implémente l'interface IteratorAggregate
     *
     * @param object $object Objet à vérifier
     */
    protected function assertImplementIteratorAggregate($object)
    {
        $interfaceName = 'IteratorAggregate';
        $class = get_class($object);
        $this->ekoInfo($class . " implémente IteratorAggregate");
        $this->assertArrayHasKey(
            $interfaceName,
            class_implements($object),
            "L'instance de $class n'implémente pas l'interface $interfaceName"
        );
        $this->assertObjectHasMethods($object, ['getIterator']);
        $tab = [];
        foreach ($object as $k => $v) {
            $tab[$k] = $v;
        }
    }

    /**
     * Vérifie le comportement d'un objet qui implémente ArrayAccess
     *
     * ### Exemple
     * - `$this->assertImplementArrayAccess($result, 1, ['id' => 2, 'title' => 'Page'], ['Exists', 'Get']);`
     *
     * @param object $object Instance de l'objet à tester
     * @param string $key    Nom d'une clé du tableau
     * @param mixed  $expect Valeur attendue
     */
    protected function assertImplementArrayAccess($object, $key, $expect)
    {
        $interfaceName = 'ArrayAccess';
        $class = get_class($object);
        $this->ekoInfo($class . " implémente ArrayAccess");

        $this->assertArrayHasKey(
            $interfaceName,
            class_implements($object),
            "L'instance de $class n'implémente pas l'interface $interfaceName"
        );

        $methods = $this->mapMethodsInterfaces[$interfaceName];

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

    /**
     * Vérifie la présence et le fonctionnement de la méthode Help d'un objet
     *
     * @param object $o Objet à vérifier
     */
    public function assertHasHelp($o)
    {
        $this->ekoInfo(get_class($o) . " possède la méthode 'help'");
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
