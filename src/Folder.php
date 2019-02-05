<?php
/**
 * Fichier Folder.php du 27/01/2019
 * Description : Fichier de la classe Folder
 *
 * PHP version 5
 *
 * @category Dossier
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

use Countable;

/**
 * Class Folder
 * <ul>
 * <li>Facilite la manipulation des dossiers</li>
 * </ul>
 *
 * @category Folder
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
class Folder implements Countable
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private static $help = [
        "Facilite la manipulation d'un dossier",
    ];

    /**
     * Singleton
     *
     * @var self
     */
    private static $instance;

    /**
     * Chemin
     *
     * @var string
     */
    public $path;

    /**
     * Contenu du dossier
     *
     * @var Items
     */
    private $content;

    /**
     * Retourne l'instance *Singleton* de cette classe.
     *
     * @staticvar Singleton $instance L'instance *Singleton* de la classe.
     *
     * @param string $path Chemin du dossier
     *
     * @return self
     */
    public static function getInstance($path)
    {
        if (null === self::$instance || !is_null($path)) {
            self::$instance = new self($path);
        }
        return self::$instance;
    }

    /**
     * Constructeur
     *
     * @param string $path
     *
     * @throws \Exception
     */
    public function __construct($path)
    {
        $items = [];
        if ($folder = opendir($path)) {
            $this->path = realpath($path);
            while (false !== ($item = readdir($folder))) {
                if ($item !== '.' && $item !== '..') {
                    if (is_dir($this->path(true) . $item)) {
                        $items['folders'][] = $item;
                    } elseif (is_file($this->path(true) . $item)) {
                        $items['files'][] = $item;
                    }
                }
            }
            closedir($folder);
        }
        $this->content = new Items($items);
    }

    /**
     * Obtenir le chemin complet
     *
     * @param bool $withDirSep Ajoute un slash
     *
     * @return string
     */
    public function path($withDirSep = false)
    {
        $path = $this->path;
        if ($withDirSep) {
            $path .= DIRECTORY_SEPARATOR;
        }
        return $path;
    }

    /**
     * Obtenir le contenu dans une instance de <code>Items</code>
     *
     * @return Items
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * Obtenir un contenu dossier/fichier
     *
     * @param string $name Nom d'un fichier/dossier
     *
     * @return null|\Rcnchris\Common\File|\Rcnchris\Common\Folder
     */
    public function get($name)
    {
        if ($this->content()->has('folders') && $this->content()->get('folders')->hasValue($name)) {
            return new self($this->path() . '/' . $name);
        } elseif ($this->content()->has('files') && $this->content()->get('files')->hasValue($name)) {
            return new File($this->path(true) . $name);
        }
        return null;
    }

    /**
     * Obtenir le contenu dans un tableau
     *
     * @return array
     */
    public function toArray()
    {
        return $this->content()->toArray();
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
        $count = 0;
        if ($this->content()->has('folders')) {
            $count = $this->content()->get('folders')->count();
        }
        if ($this->content()->has('files')) {
            $count = $this->content()->get('files')->count();
        }
        return $count;
    }
}
