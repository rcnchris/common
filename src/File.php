<?php
/**
 * Fichier File.php du 22/01/2019
 * Description : Fichier de la classe File
 *
 * PHP version 5
 *
 * @category New
 *
 * @package  Rcnchris\Core\Tools
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @link     https://github.com/rcnchris On Github
 */

namespace Rcnchris\Common;

/**
 * Class File
 *
 * @category Fichiers et dossiers
 *
 * @package  Rcnchris\Core\Tools
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris GPL
 *
 * @version  Release: <1.0.0>
 *
 * @link     https://github.com/rcnchris on Github
 */
class File implements \Countable
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private static $help = [
        "Facilite la manipulation des fichiers",
    ];

    /**
     * Singleton
     *
     * @var self
     */
    private static $instance;

    /**
     * Chemin du fichier
     *
     * @var string
     */
    private $path;

    /**
     * Instance des informations du fichier
     *
     * @var \SplFileInfo
     */
    private $infos;

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
     * Retourne l'instance *Singleton* de cette classe.
     *
     * @staticvar Singleton $instance L'instance *Singleton* de la classe.
     *
     * @param string $path Chemin du fichier/dossier
     *
     * @return self
     */
    public static function getInstance($path = null)
    {
        if (null === self::$instance || !is_null($path)) {
            self::$instance = new self($path);
        }
        return self::$instance;
    }

    /**
     * Obtenir le type Mime du fichier de l'instance
     *
     * @return string
     */
    public function getMime()
    {
        return mime_content_type($this->path);
    }

    /**
     * Vérifier s'il s'agît d'un dossier
     *
     * @return bool
     */
    public function isDir()
    {
        return $this->getInfos()->isDir();
    }

    /**
     * Constructeur non public afin d'éviter la création d'une nouvelle instance du *Singleton* via l'opérateur `new`
     *
     * @param string $path Chemin du fichier/dossier
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Obtenir le contenu d'un fichier dans une chaîne de caractères
     *
     * @return string
     */
    public function getContent()
    {
        return file_get_contents($this->path);
    }

    /**
     * Obtenir le contenu d'un fichier dans un tableau
     *
     * @return array
     */
    public function toArray()
    {
        return file($this->path);
    }

    /**
     * Obtenir une ligne d'un fichier par son numéro
     *
     * @param int $lineNumber Numéro de ligne
     *
     * @return null|string
     */
    public function getLine($lineNumber)
    {
        $a = $this->toArray();
        $lineNumber--;
        if (array_key_exists($lineNumber, $a)) {
            return $a[$lineNumber];
        }
        return null;
    }

    /**
     * Obtenir les informations sur un fichier
     *
     * @return \SplFileInfo
     */
    public function getInfos()
    {
        if (is_null($this->infos)) {
            $this->infos = new \SplFileInfo($this->path);
        }
        return $this->infos;
    }


    /**
     * Obtenir le contenu au format texte
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->isDir()) {
            return '';
        }
        return $this->getContent();
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
        return $this->isDir()
            ? count(array_slice(scandir($this->path), 2))
            : count($this->toArray());
    }
}
