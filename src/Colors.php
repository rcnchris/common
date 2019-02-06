<?php
/**
 * Fichier Colors.php du 06/02/2019
 * Description : Fichier de la classe Colors
 *
 * PHP version 5
 *
 * @category Couleurs
 *
 * @package  Core\Colors
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @link     https://github.com/rcnchris On Github
 */

namespace Rcnchris\Common;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Class Colors
 * <ul>
 * <li>Fournit une palette de couleurs nommées</li>
 * <li>Facilite la manipulation des couleurs</li>
 * </ul>
 *
 * @category Couleurs
 *
 * @package  Core\Colors
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @version  Release: <1.0.0>
 *
 * @link     https://github.com/rcnchris on Github
 */
class Colors implements Countable, ArrayAccess, IteratorAggregate
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private $help = [
        'Fournit une liste de couleurs nommées',
        'Facilite la manipulation des couleurs',
    ];

    /**
     * Liste des couleurs
     *
     * @var array
     */
    private $colors = [
        'aliceblue' => '#F0F8FF',
        'antiquewhite' => '#FAEBD7',
        'aquamarine' => '#7FFFD4',
        'azure' => '#F0FFFF',
        'beige' => '#F5F5DC',
        'bisque' => '#FFE4C4',
        'black' => '#000000',
        'blanchedalmond' => '#FFEBCD',
        'blue' => '#0000FF',
        'blueviolet' => '#8A2BE2',
        'brown' => '#A52A2A',
        'burlywood' => '#DEB887',
        'cadetblue' => '#5F9EA0',
        'chartreuse' => '#7FFF00',
        'chocolate' => '#D2691E',
        'coral' => '#FF7F50',
        'cornflowerblue' => '#6495ED',
        'cornsilk' => '#FFF8DC',
        'crimson' => '#DC143C',
        'cyan' => '#00FFFF',
        'darkblue' => '#00008B',
        'darkcyan' => '#008B8B',
        'darkgoldenrod' => '#B8860B',
        'darkgray' => '#A9A9A9',
        'darkgreen' => '#006400',
        'darkkhaki' => '#BDB76B',
        'darkmagenta' => '#8B008B',
        'darkolivegreen' => '#556B2F',
        'darkorange' => '#FF8C00',
        'darkorchid' => '#9932CC',
        'darkred' => '#8B0000',
        'darksalmon' => '#E9967A',
        'darkseagreen' => '#8DBC8F',
        'darkslateblue' => '#483D8B',
        'darkslategray' => '#2F4F4F',
        'darkturquoise' => '#00DED1',
        'darkviolet' => '#9400D3',
        'deeppink' => '#FF1493',
        'deepskyblue' => '#00BFFF',
        'dimgray' => '#696969',
        'dodgerblue' => '#1E90FF',
        'firebrick' => '#B22222',
        'floralwhite' => '#FFFAF0',
        'forestgreen' => '#228B22',
        'fuchsia' => '#FF00FF',
        'gainsboro' => '#DCDCDC',
        'ghostwhite' => '#F8F8FF',
        'gold' => '#FFD700',
        'goldenrod' => '#DAA520',
        'gray' => '#808080',
        'graylight' => '#CCCCCC',
        'green' => '#008000',
        'greenyellow' => '#ADFF2F',
        'honeydew' => '#F0FFF0',
        'hotpink' => '#FF69B4',
        'indianred' => '#CD5C5C',
        'indigo' => '#4B0082',
        'ivory' => '#FFFFF0',
        'khaki' => '#F0E68C',
        'lavender' => '#E6E6FA',
        'lavenderblush' => '#FFF0F5',
        'lawngreen' => '#7CFC00',
        'lemonchiffon' => '#FFFACD',
        'lightblue' => '#ADD8E6',
        'lightcoral' => '#F08080',
        'lightcyan' => '#E0FFFF',
        'lightgoldenrodyellow' => '#FAFAD2',
        'lightgreen' => '#90EE90',
        'lightgrey' => '#D3D3D3',
        'lightpink' => '#FFB6C1',
        'lightsalmon' => '#FFA07A',
        'lightseagreen' => '#20B2AA',
        'lightskyblue' => '#87CEFA',
        'lightslategray' => '#778899',
        'lightsteelblue' => '#B0C4DE',
        'lightyellow' => '#FFFFE0',
        'lime' => '#00FF00',
        'limegreen' => '#32CD32',
        'linen' => '#FAF0E6',
        'maroon' => '#800000',
        'mediumaquamarine' => '#66CDAA',
        'mediumblue' => '#0000CD',
        'mediumorchid' => '#BA55D3',
        'mediumpurple' => '#9370DB',
        'mediumseagreen' => '#3CB371',
        'mediumslateblue' => '#7B68EE',
        'mediumspringgreen' => '#00FA9A',
        'mediumturquoise' => '#48D1CC',
        'mediumvioletred' => '#C71585',
        'midnightblue' => '#191970',
        'mintcream' => '#F5FFFA',
        'mistyrose' => '#FFE4E1',
        'moccasin' => '#FFE4B5',
        'navajowhite' => '#FFDEAD',
        'navy' => '#000080',
        'oldlace' => '#FDF5E6',
        'olive' => '#808000',
        'orange' => '#FFA500',
        'orangered' => '#FF4500',
        'orchid' => '#DA70D6',
        'palegoldenrod' => '#EEE8AA',
        'palegreen' => '#98FB98',
        'paleturquoise' => '#AFEEEE',
        'palevioletred' => '#DB7093',
        'papayawhip' => '#FFEFD5',
        'peachpuff' => '#FFDAB9',
        'peru' => '#CD853F',
        'pink' => '#FFC8CB',
        'plum' => '#DDA0DD',
        'powderblue' => '#B0E0E6',
        'purple' => '#800080',
        'red' => '#FF0000',
        'rosybrown' => '#BC8F8F',
        'royalblue' => '#4169E1',
        'saddlebrown' => '#8B4513',
        'salmon' => '#FA8072',
        'sandybrown' => '#F4A460',
        'seagreen' => '#2E8B57',
        'seashell' => '#FFF5EE',
        'sienna' => '#A0522D',
        'silver' => '#C0C0C0',
        'skyblue' => '#87CEEB',
        'slateblue' => '#6A5ACD',
        'snow' => '#FFFAFA',
        'springgreen' => '#00FF7F',
        'steelblue' => '#4682B4',
        'tan' => '#D2B48C',
        'teal' => '#008080',
        'thistle' => '#D8BFD8',
        'tomato' => '#FF6347',
        'turquoise' => '#40E0D0',
        'violet' => '#EE82EE',
        'wheat' => '#F5DEB3',
        'white' => '#FFFFFF',
        'whitesmoke' => '#F5F5F5',
        'yellow' => '#FFFF00',
        'yellowgreen' => '#9ACD32'
    ];

    /**
     * Constructeur
     *
     * ### Exemple
     * - `$colors = new Colors();`
     * - `$colors = new Colors($palette)`;
     *
     * @param array $colors Liste des couleurs dans un tableau dont la clé est le nom de la couleur et la valeur le
     *                      code héxadécimal
     */
    public function __construct(array $colors = [])
    {
        if (!empty($colors)) {
            $this->colors = $colors;
        }
    }

    /**
     * Obtenir une couleur
     *
     * ### Exemple
     * - `$colors->aquamarine`;
     *
     * @param string $key Clé d'une couleur
     *
     * @return array|bool|string
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Obtenir le nom, le code héxadécimal ou les valeurs RGB d'une couleur de la liste
     *
     * ### Exemple
     * - `$colors->get('aquamarine');`
     * - `$colors->get('#12DE71');`
     * - `$colors->get('aquamarine', true);`
     *
     * @param string    $name  Nom ou code de la couleur
     * @param bool|null $toRgb Obtenir les valeurs RGB de la couleur dans un tableau
     *
     * @return array|bool|string
     * @throws \Exception
     */
    public function get($name, $toRgb = false)
    {
        $color = null;
        if ($name[0] === '#') {
            $color = array_search(strtoupper($name), $this->colors);
            if ($toRgb) {
                $color = $this->hexaToRgb($this->get($color));
            }
        } elseif ($this->has($name)) {
            $color = $this->colors[$name];
            if ($toRgb) {
                $color = $this->hexaToRgb($color);
            }
        }
        return $color;
    }

    /**
     * Vérifie la présence d'une couleur dans la liste
     *
     * ### Exemple
     * - `$colors->has('aquamarine');`
     *
     * @param string $name Nom de la couleur
     *
     * @return bool
     */
    public function has($name)
    {
        return $name[0] === '#'
            ? in_array(strtoupper($name), $this->colors)
            : array_key_exists(strtolower($name), $this->colors);
    }

    /**
     * Obtenir les valeurs RGB depuis un code couleur héxadécimal
     *
     * ### Exemple
     * - `$colors->colorToRgb('#45EF4B');`
     *
     * @param string $hexa Code héxadécimal sur 7 caractères
     * @param bool|null   $tostring Si vrai, retourne une chaîne de cractères : rgb(0, 0, 0)
     *
     * @return array
     * @throws \Exception
     */
    public function hexaToRgb($hexa, $tostring = false)
    {
        $hexa = strtolower($hexa);
        if ($hexa[0] != '#' || strlen($hexa) != 7) {
            throw new \Exception('code héxadécimal incorrect : ' . $hexa);
        }
        $ret = [
            'r' => hexdec(substr($hexa, 1, 2)),
            'g' => hexdec(substr($hexa, 3, 2)),
            'b' => hexdec(substr($hexa, 5, 2))
        ];
        if ($tostring) {
            return 'rgb('
            . hexdec(substr($hexa, 1, 2))
            . ', ' . hexdec(substr($hexa, 3, 2))
            . ', ' . hexdec(substr($hexa, 5, 2))
            . ')';
        }
        return $ret;
    }

    /**
     * Obtenir la liste des couleurs
     *
     * ### Exemple
     * - `$colors->getList();`
     * - `$colors->getList(true);`
     *
     * @param bool|null $inverse Les codes héxadécimaux deviennent les clés
     *
     * @return array
     */
    public function toArray($inverse = false)
    {
        if ($inverse) {
            return array_flip($this->colors);
        }
        return $this->colors;
    }

    /**
     * Ajoute ou remplace une couleur
     *
     * ### Exemple
     * - `$colors->addColor('deeplilac', '#BD58JU');`
     *
     * @param string $name Nom de la couleur
     * @param string $hexa Code héxadécimal de la couleur
     *
     * @return self
     */
    public function addColor($name, $hexa)
    {
        $this->colors[$name] = strtoupper($hexa);
        return $this;
    }

    /**
     * Définir une nouvelle palette de couleurs
     *
     * ### Exemple
     * - `$colors->setColors(['deeplilac' => '#ABC45D']);`
     *
     * @param array $palette Tableau des nouvelles couleurs
     */
    public function setColors(array $palette)
    {
        if (!empty($palette)) {
            $this->colors = $palette;
        }
    }

    /**
     * Obtenir une ou plusieurs couleurs aléatoires
     *
     * ### Exemple
     * - `$colors->rand();`
     * - `$colors->rand(3);`
     * - `$colors->rand(1, true);`
     *
     * @param int|null  $nb      Nombre de couleurs à obtenir
     * @param bool|null $inverse Retourne le code héxadécimal au lieu du nom
     *
     * @return string
     */
    public function rand($nb = 1, $inverse = false)
    {
        return array_rand($this->toArray($inverse), $nb);
    }

    /**
     * Obtenir l'aide de cette classe
     *
     * @param bool|null $text Si faux, c'est le tableau qui est retourné
     *
     * @return array|string
     */
    public function help($text = true)
    {
        if ($text) {
            return join('. ', $this->help);
        }
        return $this->help;
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
        return count($this->colors);
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
        $this->colors[$offset] = $value;
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
        unset($this->colors[$offset]);
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
        return new ArrayIterator($this->colors);
    }
}
