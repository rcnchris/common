<?php
/**
 * Fichier Url.php du 26/01/2019
 * Description : Fichier de la classe Url
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
 * Class Url
 *
 * @category URL
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
class Url
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private static $help = [
        'Facilite la manipulations d\'URL au format texte',
    ];

    /**
     * URL au format texte
     *
     * @var string|null
     */
    private $url;

    /**
     * Constructeur
     *
     * @param string|null $url URL au format texte
     */
    public function __construct($url = null)
    {
        $this->set($url);
    }

    /**
     * Obtenir l'URL au format texte
     *
     * @return null|string
     */
    public function __toString()
    {
        return $this->url;
    }

    /**
     * Obtenir une partie de l'URL
     *
     * @param string $key Nom d'une clé retournée par parse_url()
     *
     * @return mixed|null|Items
     */
    public function __get($key)
    {
        return $this->parse()->get($key);
    }

    /**
     * Définir l'URL
     *
     * @param null|string $url URL au format texte
     */
    public function set($url)
    {
        $this->url = filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * Obtenir les parties de l'URL dans une instance de <code>Items</code>
     *
     * @return Items
     */
    public function parse()
    {
        return new Items(parse_url($this->url));
    }

    /**
     * Obtenir la liste des queries dans une instance de <code>Items</code>
     *
     * @return Items
     */
    public function queries()
    {
        $query = $this->parse()->get('query');
        $queries = [];
        if ($query) {
            $query = trim($query, '?');
            $items = explode('&', $query);
            foreach ($items as $item) {
                $itemParts = explode('=', $item);
                $queries[$itemParts[0]] = $itemParts[1];
            }
        }
        return new Items($queries);
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
