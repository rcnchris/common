<?php
/**
 * Fichier Debug.php du 26/01/2019
 * Description : Fichier de la classe Debug
 *
 * PHP version 5
 *
 * @category Debug
 *
 * @package  Rcnchris\Common
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @link     https://github.com/rcnchris On Github
 */

namespace Rcnchris\Common;

/**
 * Class Debug
 * <ul>
 * <li>Fournit des méthodes de debug</li>
 * <li>Toutes les méthodes qui peuvent retourner des listes, retournent une instance de <code>Items</code></li>
 * </ul>
 *
 * @category Debug
 *
 * @package  Rcnchris\Common
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @version  Release: <1.0.0>
 *
 * @link     https://github.com/rcnchris on Github
 */
class Debug
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private static $help = [
        'Fournit des méthodes de debug',
        'Toutes les méthodes qui peuvent retourner des listes, retournent une instance de <code>Items</code>'
    ];

    /**
     * Instance
     *
     * @var $this
     */
    private static $instance;

    /**
     * Obtenir une instance (Singleton)
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtenir le nom de la classe d'un objet à partir de son instance
     *
     * @param object    $object        Instance d'un objet
     * @param bool|null $withNamespace Retourner le nom court sans le namespace
     *
     * @return string
     * @throws \Exception
     */
    public static function getClass($object, $withNamespace = true)
    {
        if ($withNamespace) {
            return get_class(self::isObject($object, true));
        }
        return self::getClassShortName(self::isObject($object, true));
    }

    /**
     * Obtenir le nom de la classe sans le namespace
     *
     * @param object $object Instance d'un objet
     *
     * @return string
     */
    public static function getClassShortName($object)
    {
        $className = self::getClass($object);
        $parts = explode('\\', $className);
        return array_pop($parts);
    }

    /**
     * Obtenir les propriétés d'un objet
     *
     * @param object $object Instance d'un objet
     *
     * @return Items
     * @throws \Exception
     * @see http://php.net/manual/fr/function.get-object-vars.php
     */
    public static function getProperties($object)
    {
        return self::makeItems(get_object_vars(self::isObject($object, true)));
    }

    /**
     * Obtenir la liste des méthodes d'un objet à partir de son instance
     *
     * @param object $object Objet dont il faut lister les méthodes
     *
     * @return Items
     * @see http://php.net/manual/fr/function.get-class-methods.php
     */
    public static function getMethods($object)
    {
        return self::makeItems(get_class_methods(self::getClass($object)));
    }

    /**
     * Vérifier la présnce d'une méthode par son nom
     *
     * @param string $name Nom de la méthode de l'objet
     * @param object $o    Instance de l'objet
     *
     * @return bool
     */
    public static function hasMethod($name, $o)
    {
        return self::getMethods($o)->hasValue($name);
    }

    /**
     * Obtenir les méthodes des parents
     *
     * @param object|null $object
     *
     * @return Items
     */
    public static function getParentsMethods($object = null)
    {
        $methods = [];
        foreach (self::getParents($object) as $parent) {
            $methods[$parent] = get_class_methods($parent);
        }
        return self::makeItems($methods);
    }

    /**
     * Retourne la liste des parents
     *
     * @param object|null $object  Objet dont il faut retourner la liste des parents
     * @param bool|null   $reverse inverse l'ordre de retour du tableau
     *
     * @return Items
     * @see http://php.net/manual/fr/function.get-parent-class.php
     */
    public static function getParents($object = null, $reverse = false)
    {
        $parents = [];
        $value = self::getClass($object);
        while (get_parent_class($value) != null) {
            $parent = get_parent_class($value);
            $parents[] = $parent;
            $value = $parent;
        }
        if ($reverse) {
            return self::makeItems(array_reverse($parents));
        }
        return self::makeItems($parents);
    }

    /**
     * Obtenir la liste des interfaces utilisées
     *
     * @param null $object
     *
     * @return Items
     * @see http://php.net/manual/fr/function.class-uses.php
     */
    public static function getInterfaces($object = null)
    {
        return self::makeItems(array_keys(class_implements(self::getClass($object))));
    }

    /**
     * Obtenir la liste des traits utilisés
     *
     * @param null $object
     *
     * @return Items
     * @see http://php.net/manual/fr/function.class-uses.php
     */
    public static function getTraits($object)
    {
        return self::makeItems(array_keys(class_uses(self::getClass($object))));
    }

    /**
     * Obtenir les parties du namespace dans un tableau
     *
     * @param object $object Objet
     *
     * @return array
     */
    public static function namespaceSplit($object)
    {
        $class = get_class($object);
        $pos = strrpos($class, '\\');
        if ($pos === false) {
            return ['', $class];
        }

        return [substr($class, 0, $pos), substr($class, $pos + 1)];
    }

    /**
     * Obtenir le type d'une ou plusieurs variables
     *
     * @param mixed ...$vars Une variable ou une liste de variables
     *
     * @return string|Items
     */
    public static function getType(...$vars)
    {
        $types = array_map(function ($var) {
            return gettype($var);
        }, $vars);
        if (count($types) === 1) {
            return current($types);
        }
        return self::makeItems($types);
    }

    /**
     * Vérifie que la variable est un objet
     *
     * @param mixed     $variable      Variable qui doit être un objet
     * @param bool|null $withException Si vrai, une exception est levée en cas d'échec
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public static function isObject($variable, $withException = false)
    {
        $isObject = self::isType('object', $variable);

        if ($withException) {
            if (!$isObject) {
                throw new \Exception(
                    "Le type de la variable passé à la fonction doit être un objet. "
                    . "Là c'est : " . gettype($variable) . "... Pas bien..."
                );
            }
            return $variable;
        }
        return $isObject;
    }

    /**
     * Vérife que la variable soit un tableau
     *
     * @param mixed $var Variable
     *
     * @return bool
     */
    public static function isArray($var)
    {
        return self::isType('array', $var);
    }

    /**
     * Vérife que la variable soit un tableau
     *
     * @param mixed $var Variable
     *
     * @return bool
     */
    public static function isBool($var)
    {
        return self::isType('bool', $var);
    }

    /**
     * Vérifie la type d'une variable
     *
     * @param string $type Type de variable PHP
     * @param mixed  $var  Variable
     *
     * @return mixed
     * @throws \Exception
     */
    public static function isType($type, $var)
    {
        $methodName = 'is_' . strtolower($type);
        if (function_exists($methodName)) {
            return $methodName($var);
        }
        throw new \Exception("Le type demandé '$type', ne donne pas lieu à une fonction connue !");
    }

    /**
     * Obtenir une instance de Items
     *
     * @param array $items Liste d'éléments
     *
     * @return Items
     */
    public static function makeItems($items = [])
    {
        return new Items($items);
    }

    /**
     * Obtenir le code source d'un fichier.
     *
     * @param string    $file         Chemin complet du fichier
     * @param bool|null $htmlentities Convertit tous les caractères éligibles en entités HTML
     *
     * @return mixed|null|string
     * @see http://php.net/manual/fr/function.htmlentities.php
     * @see http://php.net/manual/fr/function.highlight-string.php
     */
    public static function showSource($file, $htmlentities = true)
    {
        $content = null;
        if (is_file($file)) {
            $parts = explode('.', $file);
            $ext = array_pop($parts);
            $content = file_get_contents($file);
            if (strtolower($ext) === 'php') {
                $content = highlight_string($content);
            }
        }
        return $htmlentities
            ? htmlentities($content)
            : $content;
    }

    /**
     * Obtenir l'aide de cette classe
     *
     * @param bool|null $text Si faux, c'est le tableau qui ets retourné
     *
     * @return array|string
     */
    public static function help($text = true)
    {
        if ($text) {
            return join('. ', self::$help);
        }
        return self::$help;
    }
}
