<?php

use Rcnchris\Common\Folder;
use Tests\Rcnchris\Common\BaseTestCase;

class FolderTest extends BaseTestCase
{
    /**
     * @var Folder
     */
    private $folder;

    /**
     * @var Folder
     */
    private $folderFile;

    public function setUp()
    {
        $this->folder = $this->makeFolder();
        $this->folderFile = $this->makeFolder(__FILE__);
    }

    /**
     * Obtenir l'instance d'un Folder.
     *
     * @param string|null $path Emplacement (si null, racine du projet)
     *
     * @return Folder
     */
    public function makeFolder($path = null)
    {
        if (null === $path) {
            $path = $this->rootPath();
        }
        return Folder::getInstance($path);
    }

    public function testInstance()
    {
        $this->ekoTitre('Common - Folder');
        $this->assertInstanceOf(Folder::class, $this->folder);
    }

    public function testHelp()
    {
        $this->assertHasHelp($this->folder);
    }

    /**
     * Instancier correctement Folder.
     */
    public function testValidConstructor()
    {
        $this->assertInstanceOf(Folder::class, $this->folder);
        $this->assertInstanceOf(Folder::class, $this->folderFile);
        $this->assertAttributeInstanceOf(\Directory::class, 'dir', $this->folder);
        $this->assertAttributeInstanceOf(\Directory::class, 'dir', $this->folderFile);
    }

    /**
     * Instancier Folder avec un chemin invalide.
     */
    public function testInvalidConstructor()
    {
        $this->expectException(\Exception::class);
        $this->makeFolder('/fake/path');
    }

    public function testMagicMethodsGet()
    {
        $this->assertInstanceOf(Folder::class, $this->folder->tests);
    }

    public function testGet()
    {
        $this->assertInstanceOf(Folder::class, $this->folder->get('tests'));
    }

    public function testIsFolder()
    {
        $this->assertTrue($this->folder->isFolder());
        $this->assertFalse($this->folderFile->isFolder());
    }

    public function testIsFile()
    {
        $this->assertTrue($this->folderFile->isFile());
        $this->assertFalse($this->folder->isFile());
    }

    /**
     * Vérifier le retour de files avec contenu.
     */
    public function testGetFilesWithContent()
    {
        $this->assertNotEmpty($this->folder->files());
    }

    /**
     * Vérifier le retour de folders avec contenu.
     */
    public function testGetFolderWithContent()
    {
        $this->assertNotEmpty($this->folder->folders());
    }

    /**
     * Vérifier la présence d'un fichier qui existe.
     */
    public function testHasFileExists()
    {
        $this->assertTrue($this->folder->hasFile('composer.json'));
    }

    /**
     * Vérifier la présence d'un fichier qui n'existe pas.
     */
    public function testHasFileNotExists()
    {
        $this->assertFalse($this->folder->hasFile('fake.php'));
    }

    /**
     * Vérifier la présence d'un dossier qui existe.
     */
    public function testHasFolderExists()
    {
        $this->assertTrue($this->folder->hasFolder('public'));
    }

    /**
     * Vérifier la présence d'un dossier qui n'existe pas.
     */
    public function testHasFolderNotExists()
    {
        $this->assertFalse($this->folder->hasFolder('fakefolder'));
    }

    public function testExtensions()
    {
        $this->assertContains('json', $this->folder->extensions());
    }

    public function testSize()
    {
        $this->assertInternalType('integer', $this->folder->size());
        $this->assertInternalType('integer', $this->folderFile->size());
    }

    public function testDestroy()
    {
        $this->assertTrue($this->folder->__destroy());
    }

    public function testGetFileInfo()
    {
        $this->assertInstanceOf(SplFileInfo::class, $this->folder->getFileInfo('composer.json'));
    }
}