<?php
/**
 * Fichier Image.php du 22/01/2018
 * Description : Fichier de la classe Image
 *
 * PHP version 5
 *
 * @category Images
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

use Intervention\Image\ImageManagerStatic;

/**
 * Class Image
 * <ul>
 * <li>Facilite la manipulation des images</li>
 * </ul>
 *
 * @category Images
 *
 * @package  Rcnchris\Common
 *
 * @author   Raoul <rcn.chris@gmail.com>
 *
 * @license  https://github.com/rcnchris/common GPL
 *
 * @version  Release: <1.0.0>
 * @since    Release: <1.0.3>
 *
 * @link     https://github.com/rcnchris/common on Github
 */
class Image
{
    /**
     * Aide de cette classe
     *
     * @var array
     */
    private static $help = [
        "Facilite la manipulation des images",
    ];

    /**
     * Image
     *
     * @var \Intervention\Image\Image
     */
    private $img;

    /**
     * Qualité par défaut
     *
     * @var int
     */
    private $quality = 90;

    /**
     * Constructeur
     *
     * @param mixed|null $source
     */
    public function __construct($source = null)
    {
        if (!is_null($source)) {
            $this->setSource($source);
        }
    }

    /**
     * Vider l'objet Intervention de la mémoire
     */
    public function __destruct()
    {
        unset($this->img);
    }

    /**
     * Définit la source de l'instance
     *
     * @param string|\Intervention\Image\Image $source Source de l'image
     *
     * @return self
     * @throws \Exception
     */
    public function setSource($source)
    {
        if (is_null($source)) {
            throw new \Exception("La source de l'image est vide");
        } elseif (is_string($source)) {
            $this->img = ImageManagerStatic::make($source);
        } elseif ($source instanceof Image) {
            $this->img = $source;
        }
        return $this;
    }

    /**
     * Obtenir l'instance de l'image
     *
     * @return \Intervention\Image\Image
     */
    public function get()
    {
        return $this->img;
    }

    /**
     * Obtenir le chemin du fichier
     *
     * @return string
     */
    public function getPath()
    {
        return $this->img->basePath();
    }

    /**
     * Obtenir uniquement le dossier
     *
     * @return string
     */
    public function getDirname()
    {
        return $this->img->dirname;
    }

    /**
     * Obtenir le nom du fichier
     *
     * @return string
     */
    public function getBasename()
    {
        return $this->img->basename;
    }

    /**
     * Obtenir la largeur de l'image
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->img->width();
    }

    /**
     * Obtenir la longueur de l'image
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->img->height();
    }

    /**
     * Obtenir l'extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->img->extension;
    }

    /**
     * Obtenir le type mime de l'image
     *
     * @return string
     */
    public function getMime()
    {
        return $this->img->mime;
    }

    /**
     * Obtenir la taille du fichier
     *
     * @return mixed
     */
    public function getSize()
    {
        return $this->img->filesize();
    }

    /**
     * Obtenir les données exifs
     *
     * ### Exemple
     * - ``
     *
     * @param string|null $key      Nom de la clé à retourner
     * @param bool|null   $toObject Obtenir le résultat dans un objet plutôt que dans un tableau
     *
     * @return mixed
     */
    public function getExifs($key = null, $toObject = false)
    {
        $data = $this->img->exif($key);
        if ($toObject && is_array($data)) {
            $o = new \stdClass();
            foreach ($data as $k => $value) {
                $o->$k = $value;
            }
            return $o;
        }
        return $data;
    }

    /**
     * Obtenir l'image encodée
     *
     * @return string
     */
    public function __toString()
    {
        return $this->img->__toString();
    }

    /**
     * Obtenir le code HTML pour afficher l'image
     *
     * @param string $alt Valeur de l'attribut 'alt'
     *
     * @return string
     */
    public function html($alt = 'Image')
    {
        $html = '<img src="' . $this->getEncode() . '" alt="' . $alt . '">';
        return $html;
    }

    /**
     * Obtenir une miniature
     *
     * ### Exemple
     * - `$img->makeThumb();`
     * - `$img->makeThumb(200);`
     *
     * @param int|null $size Largeur de l'image,
     *                       la longueur est adaptée pour garder les proportions
     *
     * @return self
     */
    public function makeThumb($size = 150)
    {
        $img = clone($this->img);
        // $fileName = $this->getDirname() . '/thumb_' . $this->getBasename();
        return new self($img
            ->orientate()
            ->interlace()
            ->resize($size, null, function ($constraint) {
                $constraint->aspectRatio();
            }));
    }

    /**
     * Obtenir une image encodée
     *
     * ### Exemple
     * - `$img->getEncode();`
     * - `$img->getEncode(null, 75);`
     *
     * @param string   $format
     * @param int|null $quality
     *
     * @return Image
     */
    public function getEncode($format = 'data-url', $quality = null)
    {
        $img = clone($this->img);
        return new self(
            $img->encode(
                $format,
                $this->getQuality($quality)
            )
        );
    }

    /**
     * Sauvegarde l'image dans un fichier indiqué par le premier paramètre
     *
     * ### Exemple
     * - `$img->save();`
     * - `$img->save('path/to/file');`
     *
     * @param string|null $path
     * @param int|null    $quality
     *
     * @return Image
     */
    public function save($path = null, $quality = null)
    {
        return new self(
            $this->img
                ->save(
                    is_null($path) ? $this->getPath() : $path,
                    $this->getQuality($quality)
                )
        );
    }

    /**
     * Obtenir la qualité par défaut si aucun paramètre définit
     *
     * ### Exemple
     * - `$this->getQuality();`
     * - `$this->getQuality(75);`
     *
     * @param int|null $quality Qualité de l'image
     *
     * @return int
     */
    private function getQuality($quality = null)
    {
        return is_null($quality) ? $this->quality : intval($quality);
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
