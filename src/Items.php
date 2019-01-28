<?php
/**
 * Fichier Items.php du 25/01/2019
 * Description : Fichier de la classe Items
 *
 * PHP version 5
 *
 * @category New
 *
 * @package Rcnchris\Common
 *
 * @author Raoul <rcn.chris@gmail.com>
 *
 * @license https://github.com/rcnchris GPL
 *
 * @link https://github.com/rcnchris On Github
 */

namespace Rcnchris\Common;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Class Items
 *
 * @category New
 *
 * @package Rcnchris\Common
 *
 * @author Raoul <rcn.chris@gmail.com>
 *
 * @license https://github.com/rcnchris GPL
 *
 * @version Release: <1.0.0>
 *
 * @link https://github.com/rcnchris on Github
 */
class Items implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private static $help = [
        "Gestion d'une liste de données",
    ];
    /**
     * Items
     *
     * @var array
     */
    private $items = [];

    /**
     * Constructeur
     *
     * Définit la variable `items` sous forme de tableau
     * en fonction du type d'origine des items
     *
     * @param mixed $items Liste des éléments à stocker
     *
     * @see http://php.net/manual/fr/function.get-object-vars.php
     */
    public function __construct($items = null)
    {
        if (is_string($items)) {
            $json = json_decode($items, true);
            if ($json) {
                $this->items = $json;
            } else {
                $items = str_replace(' ', '', $items);
                $this->items = explode(',', $items);
            }
            if ($this->count() === 1) {
                $this->items = current($this->items);
            }
        } elseif (is_array($items)) {
            $this->items = $items;
        } elseif ($items instanceof self) {
            $this->items = $items->toArray();
        } elseif (is_object($items)) {
            $datas = [];
            if (!in_array('ArrayAccess', class_implements($items))) {
                $items = get_object_vars($items);
            }
            foreach ($items as $properties => $value) {
                $datas[$properties] = $value;
            }
            $this->items = $datas;
        }
    }

    /**
     * Destructeur
     *
     * Vide la variable `items` de son contenu
     */
    public function __destruct()
    {
        $this->items = null;
    }

    /**
     * Sérialiser le contenu des items
     *
     * @return string
     *
     * @see http://php.net/manual/fr/function.serialize.php
     */
    public function __toString()
    {
        return serialize($this->items);
    }

    /**
     * Obtenir la valeur d'une clé lors de l'appel sous la forme d'un objet
     * - `$items->name`
     *
     * @param mixed $key Clé à retourner
     *
     * @return $this|null
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Sollicitée lors de l'écriture de données vers des propriétés inaccessibles.
     *
     * @param mixed      $key   Nom de la clé à mettre à jour
     * @param mixed|null $value Valeur de la clé
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Obtenir les items sous forme de tableau
     * - `$array = $items->toArray();`
     *
     * @param callable $filter
     *
     * @return array
     */
    public function toArray(callable $filter = null)
    {
        if (!is_null($filter)) {
            return array_filter($this->items, $filter);
        }
        return $this->items;
    }

    /**
     * Extrait une portion de tableau
     *
     * @param      $offset
     * @param null $length
     * @param null $key
     *
     * @return self
     */
    public function slice($offset, $length = null, $key = null)
    {
        return new self(
            array_slice(
                is_null($key)
                    ? $this->toArray()
                    : $this->get($key)->toArray(),
                $offset,
                $length,
                true
            )
        );
    }

    /**
     * Obtenir les items correspondant à un critère sur une clé et une valeur
     * - `$items->filter('name', 'Clara');`
     *
     * @param mixed      $key   Clé à tester
     * @param mixed|null $value Valeur à tester
     *
     * @return $this
     */
    public function filter($key, $value = null)
    {
        $result = $this->toArray(function ($i) use ($key, $value) {
//            if (is_null($value) && $i[$key] === $key) {
//                return $i;
//            }
            if ($i[$key] === $value) {
                return $i;
            }
            return null;
        });
        return new self($result);
    }

    /**
     * Dédoublonne un tableau
     *
     * @param null $key
     *
     * @return self
     * @see http://php.net/manual/fr/function.array-unique.php
     */
    public function distinct($key = null)
    {
        return new self(
            array_unique(
                is_null($key)
                    ? $this->toArray()
                    : $this->get($key)->toArray()
            )
        );
    }

    /**
     * Trier les valeurs
     *
     * @param mixed|null $key  Clé dont il faut trier les valeurs
     * @param bool|null  $desc Tri descendant
     *
     * @return self
     * @see http://php.net/manual/fr/function.sort.php
     * @see http://php.net/manual/fr/function.array-reverse.php
     */
    public function sort($key = null, $desc = false)
    {
        if (is_null($key)) {
            sort($this->items);
            $sort = $this->items;
        } else {
            $sort = $this->get($key)->toArray();
            sort($sort);
        }
        if ($desc) {
            $sort = array_reverse($sort);
        }
        return new self($sort);
    }

    /**
     * Applique une fonction sur les items ou une clé
     *
     * @param callable $callable
     * @param null     $key
     *
     * @return $this
     * @see http://php.net/manual/fr/function.array-map.php
     */
    public function map(callable $callable, $key = null)
    {
        if (!is_null($key)) {
            return new self(array_map($callable, $this->get($key)->toArray()));
        }
        return new self(array_map($callable, $this->items));
    }

    /**
     * Joindre les valeurs dans une chaîne de caractères avec séparateur
     *
     * @param string|null $glue Séparateur
     * @param mixed|null  $key  Clé dont il faut joindre les valeurs
     *
     * @return string
     */
    public function join($glue = null, $key = null)
    {
        if (is_null($glue)) {
            $glue = ', ';
        }
        if (!is_null($key)) {
            return implode($glue, $this->get($key)->toArray());
        }
        return implode($glue, $this->items);
    }

    /**
     * Agrandit le tableau des items avec une valeur
     *
     * @param int   $size  Nombre ditems à ajouter
     * @param mixed $value Valeur à ajouter
     *
     * @return $this
     */
    public function pad($size, $value)
    {
        return new self(array_pad($this->items, $size, $value));
    }

    /**
     * Obtenir les items au format JSON
     *
     * @param mixed|null $key Clé à retourner
     *
     * @return string
     */
    public function toJson($key = null)
    {
        return $key === null
            ? json_encode($this->toArray())
            : json_encode($this->get($key)->toArray());
    }

    /**
     * Obtenir la valeur d'une clé des items
     *
     * @param mixed     $key       Clé à retourner
     * @param bool|null $withPoint Si faux, les points ne sont pas considérés pour traverser la hiérachie du tableau
     *
     * @return mixed|null|self
     */
    public function get($key, $withPoint = true)
    {
        if ($withPoint) {
            $indexes = explode('.', $key);
            return $this->getValue($indexes, $this->items);
        } else {
            $value = $this->items[$key];
            return is_array($value)
                ? new self($value)
                : $value;
        }
    }

    /**
     * Ajoute ou définit une clé et sa valeur à la collection
     *
     * @param int|string|null $key
     * @param mixed|null      $value
     *
     * @return bool
     */
    public function set($key = null, $value = null)
    {
        if (is_null($key) && is_null($value)) {
            return false;
        }
        if (is_null($key)) {
            $this->items[] = $value;
            return true;
        }
        $this->items[$key] = $value;
        return true;
    }

    /**
     * Vérifie la présence d'une clé dans les items
     *
     * @param mixed     $key       Clé à vérifier
     * @param bool|null $withPoint Si faux, les points ne sont pas considérés pour traverser la hiérachie du tableau
     *
     * @return bool
     */
    public function has($key, $withPoint = true)
    {
        if ($this->get($key, $withPoint)) {
            return true;
        }
        return false;
    }

    /**
     * Vérifier la présence d'une valeur
     *
     * @param mixed      $value valeur à chercher
     * @param mixed|null $key   Clé dans laquelle chercher la valeur
     *
     * @return bool
     */
    public function hasValue($value, $key = null)
    {
        $values = is_null($key)
            ? $this->items
            : $this->get($key)->toArray();
        return in_array($value, $values);
    }

    /**
     * Obtenir la liste des clés de la liste
     *
     * @return self
     */
    public function keys()
    {
        if ($this->isEmpty() || is_bool($this->items)) {
            return null;
        }
        return new self(array_keys($this->items));
    }

    /**
     * Vérifie si la liste est vide
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * Vérifie que la liste n'est pas vide
     *
     * @return bool
     */
    public function notEmpty()
    {
        return !empty($this->items);
    }

    /**
     * Obtenir la plus petite valeur
     *
     * @param mixed|null $key Clé dont il faut retourner la plus petite valeur
     *
     * @return mixed|self
     */
    public function min($key = null)
    {
        $min = is_null($key)
            ? min($this->items)
            : min($this->get($key)->toArray());
        return is_array($min)
            ? new self($min)
            : $min;
    }

    /**
     * Obtenir la plus grande valeur
     *
     * @param mixed|null $key Clé dont il faut retourner la plus grande valeur
     *
     * @return mixed|self
     */
    public function max($key = null)
    {
        $max = is_null($key)
            ? max($this->items)
            : max($this->get($key)->toArray());
        return is_array($max)
            ? new self($max)
            : $max;
    }

    /**
     * Obtenir le premier élément des items
     *
     * @param bool|null $withPoint Si faux, Supprime la notation par point
     *
     * @return mixed|self
     * @see http://php.net/manual/fr/function.array-slice.php
     */
    public function first($withPoint = true)
    {
        $keys = $this->keys()->toArray();
        $firstKey = array_slice($keys, 0, 1);
        return $this->get($firstKey[0], $withPoint);
    }

    /**
     * Obtenir le dernier éléments des items
     *
     * @return self
     */
    public function last()
    {
        $keys = $this->keys()->toArray();
        $lastKey = array_pop($keys);
        return $this->get($lastKey);
    }

    /**
     * Extraire les valeurs d'une clé pour tous les items
     *
     * @param mixed      $key    Clé à extraire
     * @param mixed|null $indice Clé qui servira d'indice à la liste extraite
     *
     * @return self|null
     *
     * @see http://php.net/manual/fr/function.array-column.php
     */
    public function extract($key, $indice = null)
    {
        return new self(array_column($this->toArray(), $key, $indice));
    }

    /**
     * Fusionne le tableau passé en paramètre avec les items de l'instance
     *
     * ### Example
     * - `$items->merge(['Vert', 'Rouge', 'Bleu']);`
     *
     * @param array $array Tableau à fusionner avec les items
     *
     * @param bool|null $recurs Ajoute les valeurs avec des clés identiques
     *
     * @return self
     * @see http://php.net/manual/fr/function.array-merge.php
     */
    public function merge(array $array, $recurs = false)
    {
        if ($recurs) {
            return new self(array_merge_recursive($this->toArray(), $array));
        }
        return new self(array_merge($this->toArray(), $array));
    }

    /**
     * Inverser l'ordre des clés des items
     *
     * @param mixed|null $key          Clé dont il faut inverser l'ordre des clés
     * @param bool|null  $preserveKeys Si définit à TRUE, les clés numériques seront préservées.
     *                                 Les clés non-numériques ne seront pas affectées par cette configuration,
     *                                 et seront toujours préservées.
     *
     * @return self
     *
     * @see http://php.net/manual/fr/function.array-reverse.php
     */
    public function reverse($key = null, $preserveKeys = false)
    {
        $ret = is_null($key)
            ? array_reverse($this->toArray(), $preserveKeys)
            : array_reverse($this->get($key)->toArray(), $preserveKeys);
        return new self($ret);
    }

    /**
     * Inverse les clés et les valeurs
     *
     * @param mixed|null $key Clé dont il faut inverser les clés avec les valeurs
     *
     * @return self
     */
    public function flip($key = null)
    {
        if (!is_null($key)) {
            return new self(array_flip($this->get($key)->toArray()));
        }
        return new self(array_flip($this->toArray()));
    }

    /**
     * Faire la somme des valeurs
     *
     * @param mixed|null $key Clé dont i lfait sommer les valeurs
     *
     * @return number
     */
    public function sum($key = null)
    {
        return is_null($key)
            ? array_sum($this->toArray())
            : $this->get($key)->sum();
    }

    /**
     * Calcule le produit des valeurs du tableau
     *
     * @param mixed|null $key Clé dont le produit des valeurs doit être retourné
     *
     * @return float
     * @see http://php.net/manual/fr/function.array-product.php
     */
    public function product($key = null)
    {
        return is_null($key)
            ? array_product($this->items)
            : array_product($this->get($key)->toArray());
    }

    /**
     * Obtenir un nombre de clés de manière aléatoire
     *
     * @param int  $n   Nombre d'items à retourner, 1 âr défaut
     * @param null $key Clé des clés à retourner
     *
     * @return self
     */
    public function rand($n = 1, $key = null)
    {
        if (is_null($key)) {
            return $this->get(array_rand($this->items, $n));
        } else {
            return $this->get($key)->rand($n);
        }
    }

    /**
     * Compte le nombre de fois qu'une valeur est présente dans la liste
     *
     * @param mixed|null $key Clé dont il faut compter les valeurs
     *
     * @return self
     * @see http://php.net/manual/fr/function.array-count-values.php
     */
    public function countValues($key = null)
    {
        return is_null($key)
            ? new self(array_count_values($this->toArray()))
            : new self($this->get($key)->countValues());
    }

    /**
     * Obtenir le nom de la clé qui contient la valeur cherchée.
     * Recherche dans un tableau la clé associée à la première valeur.
     *
     * @param mixed      $search Valeur à chercher
     * @param mixed|null $key    Clé dans laquelle il faut chercher la valeur
     *
     * @return mixed
     * @see http://php.net/manual/fr/function.array-search.php
     */
    public function findKey($search, $key = null)
    {
        return is_null($key)
            ? array_search($search, $this->toArray())
            : array_search($search, $this->get($key)->toArray());
    }

    /**
     *  Calcule la différence de deux tableaux, en prenant uniquement les valeurs
     *
     * @param $k1
     * @param $k2
     *
     * @return self
     * @see http://php.net/manual/fr/function.array-diff.php
     */
    public function diffValues($k1, $k2)
    {
        return new self(array_diff($this->get($k1)->toArray(), $this->get($k2)->toArray()));
    }

    /**
     *  Calcule la différence de deux tableaux, en prenant aussi en compte les clés
     *
     * @param $k1
     * @param $k2
     *
     * @return self
     * @see http://php.net/manual/fr/function.array-diff-assoc.php
     */
    public function diffAssoc($k1, $k2)
    {
        return new self(array_diff_assoc($this->get($k1)->toArray(), $this->get($k2)->toArray()));
    }

    /**
     * Calcule la différence de deux tableaux en utilisant les clés pour comparaison
     *
     * @param $k1
     * @param $k2
     *
     * @return self
     * @see http://php.net/manual/fr/function.array-diff-key.php
     */
    public function diffKeys($k1, $k2)
    {
        return new self(array_diff_key($this->get($k1)->toArray(), $this->get($k2)->toArray()));
    }

    /**
     * Obtenir toutes les valeurs
     * de la clé 1 dont les valeurs existent dans la clé 2
     *
     * @param $k1
     * @param $k2
     *
     * @return self
     * @see http://php.net/manual/fr/function.array-intersect.php
     */
    public function intersectValues($k1, $k2)
    {
        return new self(array_intersect($this->get($k1)->toArray(), $this->get($k2)->toArray()));
    }

    /**
     * Obtenir toutes les valeurs
     * de la clé 1 dont les clés existent dans la clé 2
     *
     * @param $k1
     * @param $k2
     *
     * @return self
     * @see http://php.net/manual/fr/function.array-intersect-key.php
     */
    public function intersectKeys($k1, $k2)
    {
        return new self(array_intersect_key($this->get($k1)->toArray(), $this->get($k2)->toArray()));
    }

    /**
     * Change la casse de toutes les clés d'un tableau
     *
     * @param null $key
     * @param int  $case
     *
     * @return self
     *
     * @see http://php.net/manual/fr/function.array-change-key-case.php
     */
    public function changeKeyCase($key = null, $case = CASE_UPPER)
    {
        $ret = is_null($key)
            ? array_change_key_case($this->toArray(), $case)
            : array_change_key_case($this->get($key)->toArray(), $case);
        return new self($ret);
    }

    /**
     * Sépare un tableau en tableaux de taille inférieure
     *
     * @param int       $size         Nombre de clés par tableau
     * @param bool|null $preserveKeys Lorsque définit à `TRUE`, les clés seront préservées. Par défaut, vaut `FALSE` ce
     *                                qui réindexera le tableau résultant numériquement
     *
     * @return self
     *
     * @see http://php.net/manual/fr/function.array-chunk.php
     */
    public function chunk($size, $preserveKeys = true)
    {
        $ret = array_chunk($this->toArray(), $size, $preserveKeys);
        return new self($ret);
    }

    /**
     * Crée un tableau, dont les clés sont les valeurs de keys, et les valeurs sont les valeurs de values.
     *
     * @param array $keys   Clés des tableaux à fusioner
     * @param array $values Clés des tableaux à fusioner
     *
     * @return self
     * @see http://php.net/manual/fr/function.array-combine.php
     */
    public function combine($keys, $values)
    {
        return new self(array_combine($this->get($keys)->toArray(), $this->get($values)->toArray()));
    }

    /**
     * Obtenir les types de données par clé
     *
     * @param mixed|null $key Clé dont il faut retourner les types des valeurs
     *
     * @return self
     * @see http://php.net/manual/fr/function.gettype.php
     */
    public function typesMap($key = null)
    {
        if (is_null($key)) {
            //$map = array_map('gettype', $this->items);
            $map = $this->map('gettype');
        } else {
            //$map = array_map('gettype', $this->get($key)->toArray());
            $map = $this->map('gettype', $key);
        }
        return new self($map);
    }

    /**
     * Obtenir la valeur d'une clé du tableau (récursive) pour la notation avec point
     *
     * @param array $indexes
     * @param       $value
     *
     * @return self|null
     *
     * @see http://php.net/manual/fr/function.array-shift.php
     * @see http://php.net/manual/fr/function.array-key-exists.php
     * @see http://php.net/manual/fr/function.is-array.php
     */
    private function getValue(array $indexes, $value)
    {
        $key = array_shift($indexes);
        if (empty($indexes)) {
            if (!array_key_exists($key, $value)) {
                return null;
            }
            return is_array($value[$key])
                ? new self($value[$key])
                : $value[$key];
        } else {
            return $this->getValue($indexes, $value[$key]);
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($this->has($offset)) {
            unset($this->items[$offset]);
        }
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *       </p>
     *       <p>
     *       The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->toArray());
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *       <b>Traversable</b>
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Obtenir l'aide de cette classe
     *
     * @param bool|null $text Si faux, c'est le tableau qui est retourné
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
