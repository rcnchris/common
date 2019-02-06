<?php

use Rcnchris\Common\Colors;
use Tests\Rcnchris\Common\BaseTestCase;

class ColorsTest extends BaseTestCase
{

    private $flatui = [
        'aloha' => '#1ABC9C',
        'ufogreen' => '#2ecc71',
        'flamboyant' => '#16a085',
        'islandgreen' => '#27ae60',
        'dayflower' => '#3498db',
        'amalficoast' => '#2980b9'
    ];

    public function makeColors(array $palette = [])
    {
        return new Colors($palette);
    }

    public function testInstance()
    {
        $this->ekoTitre('Common - Couleurs');
        $this->assertInstanceOf(
            Colors::class,
            $this->makeColors(),
            "L'instance obtenue et incorrecte"
        );
    }

    public function testHelp()
    {
        $this->assertHasHelp($this->makeColors());
    }

    public function testInstanceWithPalette()
    {
        $colors = $this->makeColors($this->flatui);
        $this->assertInstanceOf(
            Colors::class, $colors, "L'instance obtenue et incorrecte"
        );
        $this->assertCount(
            6,
            $colors->toArray(),
            "Le nombre de couleurs est incorrect lors de la définition à l'instance"
        );
    }

    public function testSetColors()
    {
        $colors = $this->makeColors();
        $palette = [
            'aliceblue' => '#F0F8FF',
            'aloha' => '#1ABC9C',
            'antiquewhite' => '#FAEBD7',
            'aqua' => '#00FFFF'
        ];
        $colors->setColors($palette);
        $this->assertCount(
            4,
            $colors->toArray(),
            "Le nombre de couleurs est incorrect lors de la définition à l'exécution"
        );
    }

    public function testAddColors()
    {
        $colors = $this->makeColors($this->flatui);
        $count = count($colors->toArray());
        $colors->addColor('deeplilac', '#9b59b6');
        $colors->addColor('moonshadow', '#8e44ad');
        $this->assertCount(
            $count + 2,
            $colors->toArray(),
            "Le nombre de couleurs est incorrect lors de l'ajout à l'exécution"
        );
        $this->assertEquals(
            '#9B59B6',
            $colors->get('deeplilac'),
            "Le code héxadécimal de la nouvelle couleur est incorrect"
        );
    }

    public function testAddColorsWithReplace()
    {
        $colors = $this->makeColors($this->flatui);
        $count = count($colors->toArray());
        $colors->addColor('aloha', '#9b59b6');
        $this->assertCount(
            $count,
            $colors->toArray(),
            "Le nombre de couleurs est incorrect lors de la modification d'une couleur"
        );

        $this->assertEquals(
            '#9B59B6',
            $colors->get('aloha'),
            "Le nouveau code héxadécimal de la nouvelle couleur 'aloha' est incorrect"
        );
    }

    public function testHas()
    {
        $colors = $this->makeColors($this->flatui);
        $this->assertTrue($colors->has('aloha'));
        $this->assertTrue($colors->has('#1abc9c'));
    }

    public function testGet()
    {
        $colors = $this->makeColors($this->flatui);
        $this->assertEquals(
            '#1ABC9C',
            $colors->get('aloha'),
            "Le code héxadécimal est incorrect pour la couleur 'aloha'"
        );
        $this->assertEquals(
            'aloha',
            $colors->get('#1abc9c'),
            "Le nom de la couleur est incorrect pour le code '#1abc9c'"
        );
        $this->assertEquals(
            ['r' => 26, 'g' => 188, 'b' => 156],
            $colors->get('aloha', true),
            "Les valeurs RGB sont incorrectes pour la couleur 'aloha'"
        );
        $this->assertEquals(
            ['r' => 26, 'g' => 188, 'b' => 156],
            $colors->get('#1abc9c', true),
            "Les valeurs RGB sont incorrectes pour le code '#1abc9c'"
        );
    }

    public function testHexaToRGB()
    {
        $colors = $this->makeColors($this->flatui);
        $this->assertEquals(
            ['r' => 26, 'g' => 188, 'b' => 156],
            $colors->hexaToRgb('#1ABC9C'),
            "Les valeurs RGB sont incorrectes pour le code '#1ABC9C'"
        );
    }

    public function testHexaToRGBWithWrongParameter()
    {
        $colors = $this->makeColors($this->flatui);
        $this->expectException(\Exception::class);
        $colors->hexaToRgb('1ABC9C');
    }

    public function testHexaToRGBToString()
    {
        $colors = $this->makeColors($this->flatui);
        $this->assertInternalType('string', $colors->hexaToRgb('#1ABC9C', true));
    }

    public function testHexaToRGBWithLongParameter()
    {
        $colors = $this->makeColors($this->flatui);

        $this->expectException(\Exception::class);
        $colors->hexaToRgb('#1ABC9CFF');
    }

    public function testHexaToRGBWithShortParameter()
    {
        $colors = $this->makeColors($this->flatui);

        $this->expectException(\Exception::class);
        $colors->hexaToRgb('#1A');
    }

    public function testtoArray()
    {
        $colors = $this->makeColors($this->flatui);

        $this->assertCount(
            6,
            $colors->toArray(),
            "Le nombre de valeurs est incorrect lors de la demande de la liste sans paramètre"
        );

        $this->assertArrayHasKey(
            'aloha',
            $colors->toArray(),
            "La couleur 'aloha' est censée se trouver dans la liste des clés"
        );

        $this->assertArrayHasKey(
            '#1ABC9C',
            $colors->toArray(true),
            "Le code couleur '#1ABC9C' est absent lors de la demande de la liste inversée est incorrect"
        );
    }

    public function testGetNbList()
    {
        $colors = $this->makeColors();
        $this->assertEquals(
            count($colors->toArray()),
            count($colors->toArray(true)),
            "Le nombre de couleurs est différent entre la liste normale et celle inversée"
        );
    }

    public function testGetRandomColors()
    {
        $colors = $this->makeColors($this->flatui);
        $this->assertContains(
            $colors->rand(),
            $colors->toArray(true),
            "La couleur aléatoire est absente de la liste des couleurs"
        );

        $randColors = $colors->rand(3);
        $this->assertCount(
            3,
            $randColors,
            "Le nombre de couleurs obtenu est incorect"
        );
        $this->assertContains(
            $randColors[0],
            $colors->toArray(true),
            "la première couleur obtenue est absente de la liste des couleurs"
        );

        $randColors = $colors->rand(1, true);
        $this->assertContains(
            $randColors,
            $colors->toArray(),
            "Le code héxadécimal aléatoire est absent de la liste des couleurs"
        );
    }

    public function testImplementArrayAccess()
    {
        $this->assertImplementArrayAccess(
            $this->makeColors($this->flatui),
            'aloha',
            '#1ABC9C'
        );
    }

    public function testImplementCountable()
    {
        $this->assertImplementCountable(
            $this->makeColors($this->flatui),
            count($this->flatui)
        );
    }

    public function testImplementIteratorAggregate()
    {
        $this->assertImplementIteratorAggregate($this->makeColors($this->flatui));
    }

    public function testMagicGet()
    {
        $colors = $this->makeColors($this->flatui);
        $this->assertObjectHasMethods($colors, ['__get']);
        $this->assertEquals($colors->get('aloha'), $colors->aloha);
    }
}
