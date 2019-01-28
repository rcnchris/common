<?php
/**
 * Fichier Text.php du 26/01/2019
 * Description : Fichier de la classe Text
 *
 * PHP version 5
 *
 * @category New
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
 * Class Text
 *
 * @category New
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
class Text
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private static $help = [
        'Facilite la manipulations des chaînes de caractères',
        'Statique et instanciable via <code>getInstance()</code>',
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
     * Obtenir le texte à gauche d'un caractère.
     *
     * @param string      $string  Caractère séparateur
     * @param string      $text    Texte à découper
     * @param string|null $default Valeur par défaut si le caractère n'est pas trouvé
     *
     * @return null|string
     */
    public static function getBefore($string, $text, $default = null)
    {
        $result = strstr($text, $string, true);
        return $result != '' ? $result : $default;
    }

    /**
     * Obtenir le texte à droite d'un caractère.
     *
     * @param string      $string  Caractère séparateur
     * @param string      $text    Texte à découper
     * @param string|null $default Valeur par défaut si le caractère n'est pas trouvé
     *
     * @return null|string
     */
    public static function getAfter($string, $text, $default = null)
    {
        $result = strstr($text, $string);
        return $result != '' ? substr($result, 1, strlen($result) - 1) : $default;
    }

    /**
     * Sérialise une variable
     *
     * @param mixed       $value  Variable à sérialiser
     * @param string|null $format Format de sérialisation
     *
     * @return string|void
     */
    public static function serialize($value, $format = null)
    {
        switch ($format) {
            case 'json':
                return json_encode($value);
            default:
                return serialize($value);
        }
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

    /**
     * Retourne une taille en Bits pour une valeur en octets
     *
     * ### Exemple
     * - `$ext->bitsSize(123456)`
     * - `$ext->bitsSize(123456, 2)`
     * - `123456|bitsSize(2)`
     *
     * @param int      $value Valeur en octets
     * @param int|null $round Arrondi
     *
     * @return string
     */
    public static function bitsToHumanSize($value, $round = 0)
    {
        $sizes = [' B', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB'];
        for ($i = 0; $value > 1024 && $i < count($sizes) - 1; $i++) {
            $value /= 1024;
        }
        return round($value, $round) . $sizes[$i];
    }

    /**
     * Obtenir les valeurs RGB à partir d'un code couleur héxadécimal
     *
     * @param string $hexa     Code héxadécimal
     * @param bool   $tostring Retourne, rgb(0, 0, 0)
     *
     * @return array|string
     * @throws \Exception
     */
    public static function hexaToRgb($hexa, $tostring = false)
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
     * Obtenir du code PHP coloré syntaxiquement
     *
     * @param string $source Code source PHP
     *
     * @return mixed
     */
    public static function showPhpSource($source)
    {
        $content = highlight_string("<?php " . $source . " ?>", true);
        return $content;
    }
}
