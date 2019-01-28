<?php
use Rcnchris\Common\Debug;
use Rcnchris\Common\Items;
use Tests\Rcnchris\Common\BaseTestCase;

class DebugTest extends BaseTestCase
{
    /**
     * @var Debug
     */
    private $debugger;

    /**
     * Crée un debugger dans l'instance
     */
    public function setUp()
    {
        $this->debugger = Debug::getInstance();
    }

    public function testGetInstance()
    {
        $this->ekoTitre('Common - Debug');
        $this->assertInstanceOf(Debug::class, $this->debugger);
    }

    public function testHasHelp()
    {
        $this->assertHasHelp($this->debugger);
    }

    public function testIsType()
    {
        $this->assertTrue(Debug::isType('bool', true));
    }

    public function testIsTypeWithWrongType()
    {
        $this->expectException(\Exception::class);
        $this->assertTrue(Debug::isType('fake', true));
    }

    public function testIsObjectWithoutException()
    {
        $this->assertFalse(Debug::isObject('fake'));
    }

    public function testIsObjectWithException()
    {
        $this->expectException(\Exception::class);
        Debug::isObject('fake', true);
    }

    public function testIsArrayWithArray()
    {
        $var = ['ola', 'ole'];
        $this->assertTrue(Debug::isArray($var));
    }

    public function testIsArrayWithString()
    {
        $var = 'ola, ole';
        $this->assertFalse(Debug::isArray($var));
    }

    public function testIsBoolWithBool()
    {
        $var = true;
        $this->assertTrue(Debug::isBool($var));
    }

    public function testIsBoolWithString()
    {
        $var = 'ola, ole';
        $this->assertFalse(Debug::isBool($var));
    }

    public function testGetType()
    {
        $var1 = 'ola';
        $var2 = ['ola', 'ole'];
        $var3 = new \stdClass();
        $var4 = true;
        $this->assertInstanceOf(
            Items::class,
            Debug::getType($var1, $var2, $var3, $var4)
        );
        $this->assertEquals(
            ['string', 'array', 'object', 'boolean'],
            Debug::getType($var1, $var2, $var3, $var4)->toArray()
        );
    }

    public function testGetTypeWithOneVar()
    {
        $var = 'ola';
        $this->assertEquals('string', Debug::getType($var));
    }

    public function testGetClass()
    {
        $className = get_class($this);
        $this->assertEquals($className, Debug::getClass($this));
        $this->assertEquals($className, $this->debugger->getClass($this));
    }

    public function testGetClassWithoutNamespace()
    {
        $this->assertEquals('DebugTest', Debug::getClass($this, false));
    }

    public function testGetClassShortName()
    {
        $this->assertEquals('DebugTest', Debug::getClassShortName($this));
        $this->assertEquals('DebugTest', $this->debugger->getClassShortName($this));
    }

    public function testGetProperties()
    {
        $this->assertEquals([], Debug::getProperties($this)->toArray());
        $o = new \stdClass();
        $o->name = 'Clara';
        $this->assertEquals(['name' => 'Clara'], Debug::getProperties($o)->toArray());
        $this->assertEquals(['name' => 'Clara'], $this->debugger->getProperties($o)->toArray());
    }

    public function testHasMethods()
    {
        $this->assertTrue(Debug::hasMethod('testHasMethods', $this));
        $this->assertFalse(Debug::hasMethod('testFake', $this));
    }

    public function testGetMethods()
    {
        $this->assertNotEmpty(Debug::getMethods($this));
        $this->assertNotEmpty($this->debugger->getMethods($this));
    }

    public function testGetParentsMethods()
    {
        $this->assertNotEmpty(Debug::getParentsMethods($this));
        $this->assertNotEmpty($this->debugger->getParentsMethods($this));
    }

    public function testGetParents()
    {
        $this->assertNotEmpty(Debug::getParents($this));
        $this->assertCount(4, Debug::getParents($this));
        $this->assertEquals(get_parent_class(get_class($this)), current(Debug::getParents($this)->toArray()));
        $this->assertEquals(get_parent_class(get_class($this)), current($this->debugger->getParents($this)->toArray()));
    }

    public function testGetParentsReverse()
    {
        $this->assertEquals(array_reverse(Debug::getParents($this)->toArray()),
            Debug::getParents($this, true)->toArray());
        $this->assertEquals(array_reverse($this->debugger->getParents($this)->toArray()),
            $this->debugger->getParents($this, true)->toArray());
    }

    public function testGetInterfaces()
    {
        $this->assertNotEmpty(Debug::getInterfaces($this));
        $this->assertNotEmpty($this->debugger->getInterfaces($this));
    }

    public function testGetTraits()
    {
        $this->assertEquals([], Debug::getTraits($this)->toArray());
        $this->assertEquals([], $this->debugger->getTraits($this)->toArray());
    }

    public function testNamespaceSplit()
    {
        $split=$this->debugger->namespaceSplit(Debug::getMethods($this));
        $this->assertInternalType('array', $split);
        $this->assertEquals([
            'Rcnchris\Common',
            'Items'
        ], $split);
    }

    public function testNamespaceSplitWithoutNamespace()
    {
        $split=$this->debugger->namespaceSplit(new \DateTime());
        $this->assertInternalType('array', $split);
        $this->assertEquals([
            '',
            'DateTime'
        ], $split);
    }

    public function testGetSourceFile()
    {
        ob_start();
        $this->assertInternalType('string', $this->debugger->showSource(__FILE__));
        $content = ob_get_clean();
    }
}
