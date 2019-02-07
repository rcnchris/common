<?php
use Rcnchris\Common\Image;
use Tests\Rcnchris\Common\BaseTestCase;

class ImageTest extends BaseTestCase
{

    /**
     * Chemin des images de tests
     *
     * @var string
     */
    private $dirPath;

    /**
     * Instance du dossier des images de tests
     *
     * @var \Directory
     */
    private $imgDir;

    /**
     * Liste des images du dossier img
     *
     * @var array
     */
    private $files;

    /**
     * @var Image
     */
    private $img;

    public function setUp()
    {
        parent::setUp();
        $this->dirPath = $this->pathFiles . '/img';
        $this->imgDir = dir($this->dirPath);
        $this->files = [];
        while (false !== ($item = $this->imgDir->read())) {
            $fileName = $this->dirPath . DIRECTORY_SEPARATOR . $item;
            if (is_file($fileName)) {
                $this->files[] = $fileName;
            }
        }
        $file = $this->files[array_rand($this->files)];
        $this->img = $this->makeImage($file);
    }

    /**
     * @param mixed|null $source
     *
     * @return Image
     */
    public function makeImage($source = null)
    {
        return new Image($source);
    }

    public function testHelp()
    {
        $this->ekoTitre('Common - Image');
        $this->assertHasHelp($this->makeImage());
    }

    public function testInstanceWithPathFile()
    {
        $file = $this->pathFiles . '/img/bob_marley_santa-barbara79.jpg';
        $img = $this->makeImage($file);
        $this->assertInstanceOf(Image::class, $img);
        $this->assertEquals(
            $file,
            $img->getPath(),
            "Le chemin n'est pas celui d'origine"
        );
    }

    public function testInstanceWithObject()
    {
        $file = $this->pathFiles . '/img/bob_marley_santa-barbara79.jpg';
        $img = $this->makeImage($file);
        $this->assertInstanceOf(Image::class, $this->makeImage($img));
        $this->assertEquals(
            $file, $img->getPath(), "Le chemin n'est pas celui d'origine"
        );
    }

    public function testSetSourceWithEmptyParameter()
    {
        $this->expectException(\Exception::class);
        $this->makeImage()->setSource(null);
    }

    public function testGet()
    {
        $this->assertInstanceOf(
            \Intervention\Image\Image::class, $this->img->get(),
            "La fonction get doit retourner l'instance d'Intervention"
        );
    }

    public function testGetDirname()
    {
        $this->assertEquals(
            $this->pathFiles . '/img', $this->img->getDirname(),
            "Le chemin de l'image n'est pas celui attendu"
        );
    }

    public function testGetBasename()
    {
        $fileName = $this->img->getBasename();
        $this->assertContains(
            $this->img->getDirname() . DIRECTORY_SEPARATOR . $fileName, $this->files,
            "Le nom du fichier est introuvable dans la liste des fichiers"
        );
    }

    public function testGetExtension()
    {
        $this->assertInternalType(
            'string',
            $this->img->getExtension(),
            "L'extension n'est pas celle attendue"
        );
    }

    public function testGetWidth()
    {
        $this->assertInternalType(
            'integer',
            $this->img->getWidth(),
            "La largeur n'est pas celle attendue"
        );
    }

    public function testGetHeight()
    {
        $this->assertInternalType(
            'integer',
            $this->img->getHeight(),
            "La hauteur n'est pas celle attendue"
        );
    }

    public function testGetSize()
    {
        $this->assertInternalType(
            'integer',
            $this->img->getSize(),
            "La taille du fichier n'a pas le type attendu"
        );
    }

    public function testGetMime()
    {
        $this->assertInternalType(
            'string',
            $this->img->getMime(),
            "Le type mime est incorrect"
        );
    }

    public function testGetExifs()
    {
        $img = $this->makeImage($this->files[2]);
        $this->assertNotEmpty(
            $img->getExifs(),
            "Cette image est censée avoir des données exif"
        );
        $this->assertInternalType(
            'integer', $img->getExifs('FileDateTime'),
            "Le type attendu d'une donnée exif est incorrect"
        );
        $this->assertInstanceOf(
            \stdClass::class,
            $img->getExifs(null, true),
            "L'objet attendu est incorrect"
        );
    }

    public function testGetEncode()
    {
        $this->assertInstanceOf(
            Image::class,
            $this->img->getEncode(),
            "L'objet attendu est incorrect"
        );
    }

    public function testToString()
    {
        $this->assertInternalType(
            'string',
            (string)$this->img,
            "Le type attendu est incorrect"
        );
    }

    public function testMakeThumb()
    {
        $file = $this->pathFiles . '/img/CamarroSS.gif';
        $img = $this->makeImage($file);
        $thumb = $img->makeThumb();
        $this->assertInstanceOf(
            Image::class,
            $thumb,
            "L'objet attendu est incorrect"
        );
        $this->assertEquals(
            150,
            $thumb->getWidth(),
            "La largeur attendue est incorrecte"
        );
    }

    public function testSave()
    {
        $newFile = $this->pathFiles . '/img/save_' . $this->img->getBasename();
        $newImg = $this->img->save($newFile);
        $this->assertInstanceOf(
            Image::class,
            $newImg,
            "L'objet attendu est incorrect"
        );
        $this->assertTrue(file_exists($newFile));
        unlink($newFile);
    }

    public function testHtml()
    {
        $html = '<img src="' . $this->img->getEncode() . '" alt="test">';
        $this->assertSimilar($html, $this->img->html('test'));
    }
}
