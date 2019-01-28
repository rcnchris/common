<?php
/**
 * Fichier Folder.php du 27/01/2019
 * Description : Fichier de la classe Folder
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

use SplFileInfo;

/**
 * Class Folder
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
class Folder
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private static $help = [
        "Facilite la manipulation de dossiers",
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
     * @var array
     */
    private $content = [
        'files' => [],
        'folders' => []
    ];

    /**
     * Instance du répertoire
     *
     * @var \Directory
     */
    private $dir;

    /**
     * Retourne l'instance *Singleton* de cette classe.
     *
     * @staticvar Singleton $instance L'instance *Singleton* de la classe.
     *
     * @param string $path Chemin du dossier
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
     * Constructeur
     *
     * @param string $path
     *
     * @throws \Exception
     */
    public function __construct($path = __DIR__)
    {
        if (is_dir($path)) {
            $this->dir = dir($path);
            $this->path = $path;
        } elseif (is_file($path)) {
            $this->dir = dir(dirname($path));
            $this->path = $path;
        } else {
            throw new \Exception('Paramètre invalide');
        }
    }

    /**
     * Fermeture de l'instance \Directory.
     *
     * @return bool
     */
    public function __destroy()
    {
        $this->dir->close();
        return true;
    }

    /**
     * Est appelée pour lire des données depuis des propriétés inaccessibles.
     *
     * @param string $key Dossier ou fichier dans l'instance
     *
     * @return self|null
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Obtenir une nouvelle instance à partir d'un dossier ou fichier présent dans l'instance courante
     *
     * @param string $name Dossier ou fichier dans l'instance
     *
     * @return self|null
     */
    public function get($name)
    {
        return file_exists($this->path . DIRECTORY_SEPARATOR . $name)
            ? new self($this->path . DIRECTORY_SEPARATOR . $name)
            : null;
    }

    /**
     * Obtenir la taille du chemin de l'instance
     *
     * @return int|null|string
     */
    public function size()
    {
        return $this->getFileInfo($this->path)->getSize();
    }

    /**
     * Obtenir le contenu du dossier.
     *
     * @param string $key 'files', 'folders' ou les deux si null
     *
     * @return array
     */
    public function content($key = null)
    {
        while (false !== ($entry = $this->dir->read())) {
            $item = $this->path . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($item)) {
                $this->content['folders'][] = $entry;
            } elseif (is_file($item)) {
                $this->content['files'][] = $entry;
            }
        }
        sort($this->content['files']);
        sort($this->content['folders']);
        $this->content['folders'] = array_slice($this->content['folders'], 2);
        return $key
            ? $this->content[$key]
            : $this->content;
    }

    /**
     * Obtenir la liste des fichiers à la racine du dossier.
     *
     * @return mixed
     */
    public function files()
    {
        return $this->content('files');
    }

    /**
     * Obtenir les dossiers de la racine.
     *
     * @return array
     */
    public function folders()
    {
        return $this->content('folders');
    }

    /**
     * Vérifier si l'instance est un dossier
     *
     * @param string|null $path Chemin à vérifier
     *
     * @return bool
     */
    public function isFolder($path = null)
    {
        if (is_null($path)) {
            $path = $this->path;
        }
        return is_dir($path);
    }

    /**
     * Vérifier si l'instance est un fichier
     *
     * @param string|null $path Chemin à vérifier
     *
     * @return bool
     */
    public function isFile($path = null)
    {
        if (is_null($path)) {
            $path = $this->path;
        }
        return is_file($path);
    }

    /**
     * Vérifier la présence d'un fichier à la racine.
     *
     * @param string $name Nom du fichier
     *
     * @return bool
     */
    public function hasFile($name)
    {
        return in_array($name, $this->files(), true);
    }

    /**
     * Vérifier la présence d'un dossier à la racine.
     *
     * @param string $name Nom du dossier
     *
     * @return bool
     */
    public function hasFolder($name)
    {
        return in_array($name, $this->folders(), true);
    }

    /**
     * Obtenir la liste des extensions de l'instance
     *
     * @param null $name
     *
     * @return array
     */
    public function extensions($name = null)
    {
        $ret = [];
        foreach ($this->files() as $file) {
            $ret[] = substr($file, strpos($file, '.') + 1, strlen($file) - strpos($file, '.'));
        }
        return is_null($name)
            ? array_unique($ret)
            : in_array($name, $ret);
    }

    /**
     * Obtenir les informations d'un fichier
     *
     * @param string $name Nom complet du fichier
     *
     * @return SplFileInfo
     */
    public function getFileInfo($name)
    {
        return new SplFileInfo($name);
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
