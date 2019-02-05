<?php
use Rcnchris\Common\Text;
use Tests\Rcnchris\Common\BaseTestCase;

class TextTest extends BaseTestCase
{
    public function testInstance()
    {
        $this->ekoTitre('Common - Text');
        $this->assertInstanceOf(Text::class, Text::getInstance());
    }

    public function testHasHelp()
    {
        $this->assertHasHelp(Text::getInstance());
    }

    public function testGetBefore()
    {
        $texte = 'ola,oli';
        $this->assertEquals('ola', Text::getBefore(',', $texte));
    }

    public function testGetAfter()
    {
        $texte = 'ola,oli';
        $this->assertEquals('oli', Text::getAfter(',', $texte));
    }

    public function testSerialize()
    {
        $texte = 'ola,oli';
        $this->assertEquals('s:7:"ola,oli";', Text::serialize($texte));
    }

    public function testSerializeJson()
    {
        $texte = 'ola,oli';
        $this->assertEquals('"ola,oli"', Text::serialize($texte, 'json'));
    }

    public function testBitsToHumanSize()
    {
        $this->assertEquals('1024 B', Text::bitsToHumanSize(1024));
        $this->assertEquals('1 KB', Text::bitsToHumanSize(1025));
    }

    public function testHexaToRgb()
    {
        $this->assertEquals([
            'r' => 0,
            'g' => 0,
            'b' => 0
        ], Text::hexaToRgb('#000000'));
    }

    public function testHexaToRgbToString()
    {
        $this->assertSimilar('rgb(0, 0, 0)', Text::hexaToRgb('#000000', true));
    }

    public function testHexaToRgbWithWrongHexa()
    {
        $this->expectException(\Exception::class);
        Text::hexaToRgb('000000');
    }

    public function testShowPhpSource()
    {
        $source = 'phpinfo();';
        $result = '<code><span style="color: #000000"><span style="color: #0000BB">&lt;?php&nbsp;phpinfo</span><span style="color: #007700">();&nbsp;</span><span style="color: #0000BB">?&gt;</span></span></code>';
        $this->assertSimilar($result, Text::showPhpSource($source));
    }

    public function testMarkdown()
    {
        $result = Text::markdown('# Ola les gens');
        $this->assertSimilar('<h1>Ola les gens</h1>', $result);
    }
}