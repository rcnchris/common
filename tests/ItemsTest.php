<?php

use Rcnchris\Common\Items;
use Tests\Rcnchris\Common\BaseTestCase;

class ItemsTest extends BaseTestCase
{
    /**
     * Tableau de toutes les instances d'items
     *
     * @var array
     */
    private $itemsArray;

    /**
     * Tableau des données brutes
     *
     * @var array
     */
    private $itemsDatas;

    public function setUp()
    {
        $o = new \stdClass();
        $o->name = 'Mathis';
        $o->year = 2007;
        $o->genre = 'male';

        $this->itemsDatas = [
            'stringList' => 'ola,ole,oli',
            'numberList' => '12,13.4,25',
            'item' => [
                'name' => 'Mathis',
                'year' => 2007,
                'genre' => 'male'
            ],
            'items' => [
                [
                    'name' => 'Mathis',
                    'year' => 2007,
                    'genre' => 'male'
                ],
                [
                    'name' => 'Raphaël',
                    'year' => 2007,
                    'genre' => 'male'
                ],
                [
                    'name' => 'Clara',
                    'year' => 2009,
                    'genre' => 'female'
                ]
            ],
            'object' => $o,
            'combine' => [
                'fruits' => ['Avocat', 'Fraise', 'Citron'],
                'couleurs' => ['Vert', 'Rouge', 'Jaune'],
            ],
            'json' => file_get_contents($this->rootPath() . '/composer.json'),
            'synology' => [
                'SYNO.AudioStation.Album' => [
                    'maxVersion' => 4,
                    'minVersion' => 1,
                    'path' => 'AudioStation/query.cgi'
                ]
            ]
        ];
        $items = [];
        foreach ($this->itemsDatas as $name => $content) {
            $items[$name] = $this->makeItems($content);
        }
        $items['self'] = $this->makeItems($items['json']);
        $this->itemsArray = $items;
    }

    /**
     * @param $data
     *
     * @return Items
     */
    public function makeItems($data)
    {
        return new Items($data);
    }

    public function testInstances()
    {
        $this->ekoTitre('Common - Items');
        foreach ($this->itemsArray as $name => $items) {
            $this->assertInstanceOf(Items::class, $items);
        }
    }

    public function testHelp()
    {
        $o = $this->makeItems('ola,ole,oli');
        $this->assertObjectHasAttribute('help', $o);
        $this->assertObjectHasMethods($o, ['help']);
        $this->assertInternalType('string', $o->help());
        $this->assertInternalType('array', $o->help(false));
    }

    public function testToString()
    {
        $this->assertEquals(serialize($this->itemsDatas['item']), (string)$this->itemsArray['item']);
        $this->assertEquals(serialize($this->itemsDatas['items']), (string)$this->itemsArray['items']);
    }

    public function testJoin()
    {
        $this->assertEquals('Avocat, Fraise, Citron', $this->itemsArray['combine']->fruits->join());
        $this->assertEquals('Avocat, Fraise, Citron', $this->itemsArray['combine']->join(null, 'fruits'));
        $this->assertEquals('Avocat;Fraise;Citron', $this->itemsArray['combine']->join(';', 'fruits'));
    }

    public function testGet()
    {
        $this->assertEquals('ola', $this->itemsArray['stringList']->get(0));
        $this->assertEquals(12, $this->itemsArray['numberList']->get(0));
        $this->assertEquals('Mathis', $this->itemsArray['item']->get('name'));
        $this->assertEquals(current($this->itemsDatas['items']), $this->itemsArray['items']->get(0)->toArray());
        $this->assertEquals('rcnchris/common', $this->itemsArray['json']->name);
        $this->assertEquals('rcnchris/common', $this->itemsArray['self']->name);
    }

    public function testGetWithPoint()
    {
        $this->assertEquals('Raphaël', $this->itemsArray['items']->get('1.name'));
    }

    public function testGetKeyWithPoint()
    {
        $this->assertInstanceOf(Items::class, $this->itemsArray['synology']->get('SYNO.AudioStation.Album', false));
    }

    public function testMagicGet()
    {
        $this->assertEquals('Mathis', $this->itemsArray['item']->name);
        $this->assertEquals('Mathis', $this->itemsArray['object']->name);
    }

    public function testMagicSet()
    {
        $item = $this->itemsArray['item'];
        $item->name = 'Clara';
        $this->assertEquals('Clara', $item->name);
    }

    public function testToArray()
    {
        $this->assertEquals($this->itemsDatas['item'], $this->itemsArray['item']->toArray());
    }

    public function testToArrayWithFilter()
    {
        $this->assertEquals(
            $this->itemsDatas['items'][0],
            $this->itemsArray['items']->toArray(function ($i) {
                if ($i['name'] === 'Mathis') {
                    return $i;
                }
                return null;
            })[0]
        );
    }

    public function testFilter()
    {
        $this->assertEquals(
            $this->itemsDatas['items'][2],
            $this->itemsArray['items']->filter('name', 'Clara')->toArray()[2]
        );
    }

    public function testMap()
    {
        $this->assertEquals(
            ['MATHIS', 'RAPHAëL', 'CLARA'],
            $this->itemsArray['items']->extract('name')->map('strtoupper')->toArray()
        );
        $this->assertEquals(
            ['AVOCAT', 'FRAISE', 'CITRON'],
            $this->itemsArray['combine']->map('strtoupper', 'fruits')->toArray()
        );
    }

    public function testCombine()
    {
        $this->assertEquals(
            array_combine(
                $this->itemsDatas['combine']['fruits'],
                $this->itemsDatas['combine']['couleurs']
            ),
            $this->itemsArray['combine']->combine('fruits', 'couleurs')->toArray()
        );
    }

    public function testPad()
    {
        $this->assertEquals(
            ['Avocat', 'Fraise', 'Citron', 'new', 'new'],
            $this->itemsArray['combine']->fruits->pad(5, 'new')->toArray()
        );
    }

    public function testMerge()
    {
        $items = new Items($this->itemsArray['combine']['fruits']);
        $merge = $items->merge(['Vert', 'Rouge', 'Jaune']);
        $this->assertEquals(
            ['Avocat', 'Fraise', 'Citron', 'Vert', 'Rouge', 'Jaune'],
            $merge->toArray()
        );
    }

    public function testMergeRecurs()
    {
        $a1 = ['Pomme', 'Fraise', 'Citron'];
        $a2 = ['Kiwi', 'Cerise', 'Fraise'];
        $items = $this->makeItems($a1);
        $this->assertEquals(['Pomme', 'Fraise', 'Citron', 'Kiwi', 'Cerise', 'Fraise'],
            $items->merge($a2, true)->toArray());
    }

    public function testFlip()
    {
        $this->assertEquals(
            [
                'Mathis' => 'name',
                2007 => 'year',
                'male' => 'genre'
            ],
            $this->itemsArray['item']->flip()->toArray()
        );
        $this->assertEquals(
            [
                'Mathis' => 'name',
                2007 => 'year',
                'male' => 'genre'
            ],
            $this->itemsArray['items']->flip(0)->toArray()
        );
    }

    public function testIntersectKeys()
    {
        $a1 = ['blue' => 1, 'red' => 2, 'green' => 3, 'purple' => 4];
        $a2 = ['green' => 5, 'blue' => 6, 'yellow' => 7, 'cyan' => 8];

        $items = $this->makeItems(['a1' => $a1, 'a2' => $a2]);
        $this->assertEquals([
            'blue' => 1,
            'green' => 3
        ], $items->intersectKeys('a1', 'a2')->toArray());
    }

    public function testIntersectValues()
    {
        $a1 = ["a" => "green", "red", "blue"];
        $a2 = ["b" => "green", "yellow", "red"];

        $items = $this->makeItems(['a1' => $a1, 'a2' => $a2]);
        $this->assertEquals([
            'a' => 'green',
            0 => 'red'
        ], $items->intersectValues('a1', 'a2')->toArray());
    }

    public function testToJson()
    {
        $this->assertEquals(json_encode($this->itemsDatas['items']), $this->itemsArray['items']->toJson());
    }

    public function testSet()
    {
        $this->itemsArray['stringList']->set(null, 'olo');
        $this->assertEquals(['ola', 'ole', 'oli', 'olo'], $this->itemsArray['stringList']->toArray());

        $this->itemsArray['item']->set('new', 'test');
        $this->assertEquals('test', $this->itemsArray['item']->get('new'));

        $this->itemsArray['item']->set('other');
        $this->assertContains('other', $this->itemsArray['item']->keys());
        $this->assertNull($this->itemsArray['item']->get('other'));

        $this->assertFalse($this->itemsArray['item']->set());
    }

    public function testHas()
    {
        $this->assertTrue($this->itemsArray['item']->has('name'));
        $this->assertFalse($this->itemsArray['item']->has('fake'));
    }

    public function testKeys()
    {
        $this->assertEquals(array_keys($this->itemsDatas['item']), $this->itemsArray['item']->keys()->toArray());
        $this->assertNull($this->makeItems([])->keys());
    }

    public function testIsEmpty()
    {
        $this->assertFalse($this->itemsArray['stringList']->isEmpty());
        $this->assertTrue($this->makeItems([])->isEmpty());
    }

    public function testNotEmpty()
    {
        $this->assertTrue($this->itemsArray['stringList']->NotEmpty());
        $this->assertFalse($this->makeItems([])->NotEmpty());
    }

    public function testFirst()
    {
        $this->assertEquals('ola', $this->itemsArray['stringList']->first());
        $this->assertEquals(current($this->itemsDatas['items']), $this->itemsArray['items']->first()->toArray());
    }

    public function testLast()
    {
        $this->assertEquals('oli', $this->itemsArray['stringList']->last());
        $this->assertEquals('Clara', $this->itemsArray['items']->last()->name);
    }

    public function testCount()
    {
        $this->assertEquals(3, $this->itemsArray['stringList']->count());
        $this->assertEquals(3, $this->itemsArray['item']->count());
        $this->assertEquals(3, $this->itemsArray['items']->count());
    }

    public function testExtract()
    {
        $this->assertEquals(['Mathis', 'Raphaël', 'Clara'], $this->itemsArray['items']->extract('name')->toArray());
    }

    public function testReverse()
    {
        $this->assertEquals(['oli', 'ole', 'ola'], $this->itemsArray['stringList']->reverse()->toArray());
    }

    public function testChangeKeysCase()
    {
        $this->assertEquals(['NAME', 'YEAR', 'GENRE'], $this->itemsArray['item']->changeKeyCase()->keys()->toArray());
        $this->assertEquals(
            ['name', 'year', 'genre'],
            $this->itemsArray['item']->changeKeyCase(null, CASE_LOWER)->keys()->toArray()
        );
    }

    public function testChunk()
    {
        $this->assertEquals([
            [
                'name' => 'Mathis',
                'year' => 2007
            ],
            ['genre' => 'male']
        ], $this->itemsArray['item']->chunk(2)->toArray());
    }

    public function testMagicGetMethod()
    {
        $item = $this->itemsArray['item'];
        $this->assertEquals('Mathis', $item->name);
    }

    public function testArrayAccess()
    {
        $item = $this->itemsArray['item'];
        $this->assertEquals('Mathis', $item['name']);
    }

    public function testSum()
    {
        $this->assertEquals(
            array_sum($this->itemsArray['numberList']->toArray()),
            $this->itemsArray['numberList']->sum()
        );
    }

    public function testSumWithKey()
    {
        $notes = [
            'Mathis' => [12, 8, 16],
            'Raphaël' => [4, 18, 13],
            'Clara' => [12, 20, 16],
        ];
        $this->assertEquals(array_sum($notes['Clara']), $this->makeItems($notes)->sum('Clara'));
    }

    public function testProduct()
    {
        $this->assertEquals(
            384,
            $this->makeItems([2, 4, 6, 8])->product()
        );
    }

    public function testProductWithKey()
    {
        $this->assertEquals(
            384,
            $this->makeItems([
                'Clara' => [2, 4, 6, 8],
                'Mathis' => [1, 2, 4, 8],
            ])->product('Clara')
        );
    }

    public function testCountValues()
    {
        $values = [12, 45, 84, 12, 65, 3, 45];
        $this->assertEquals(array_count_values($values), $this->makeItems($values)->countValues()->toArray());
    }

    public function testCountValuesWithKeys()
    {
        $values = [
            'Clara' => [12, 45, 84, 12, 65, 3, 45],
            'Mathis' => [10, 75, 478, 1, 85, 73, 945],
        ];
        $this->assertEquals(
            array_count_values($values['Clara']),
            $this->makeItems($values)->countValues('Clara')->toArray()
        );
    }

    public function testRand()
    {
        $values = [12, 45, 78];
        $this->assertTrue(
            in_array(
                $this->makeItems($values)->rand(),
                $values
            )
        );
    }

    public function testRandWithKeys()
    {
        $values = [
            'Clara' => [12, 45, 78],
            'Mathis' => [4, 96, 74],
        ];
        $this->assertTrue(
            in_array(
                $this->makeItems($values)->rand(1, 'Clara'),
                $values['Clara']
            )
        );
    }

    public function testFindKey()
    {
        $this->assertEquals(1, $this->itemsArray['stringList']->findKey('ole'));
        $this->assertEquals('year', $this->itemsArray['item']->findKey(2007));
    }

    public function testFindKeyWithKey()
    {
        $config = [
            'datasources' => [
                'default' => [
                    'server' => 'localhost',
                    'sgbd' => 'mysql'
                ]
            ]
        ];
        $items = $this->makeItems($config);
        $this->assertEquals('sgbd', $items->get('datasources.default')->findKey('mysql'));
    }

    public function testDiffValues()
    {
        $a1 = ["a" => "green", "red", "blue", "red"];
        $a2 = ["b" => "green", "yellow", "red"];
        $items = $this->makeItems(['a1' => $a1, 'a2' => $a2]);
        $this->assertEquals([
            '1' => 'blue',
        ], $items->diffValues('a1', 'a2')->toArray());
    }

    public function testDiffAssoc()
    {
        $a1 = ['a' => 'vert', 'b' => 'marron', 'c' => 'bleu', 'rouge'];
        $a2 = ['a' => 'vert', 'jaune', 'rouge'];
        $items = $this->makeItems(['a1' => $a1, 'a2' => $a2]);
        $this->assertEquals([
            'b' => 'marron',
            'c' => 'bleu',
            0 => 'rouge'
        ], $items->diffAssoc('a1', 'a2')->toArray());
    }

    public function testDiffKeys()
    {
        $a1 = ['blue' => 1, 'red' => 2, 'green' => 3, 'purple' => 4];
        $a2 = ['green' => 5, 'yellow' => 7, 'cyan' => 8];
        $items = $this->makeItems(['a1' => $a1, 'a2' => $a2]);
        $this->assertEquals([
            'blue' => 1,
            'red' => 2,
            'purple' => 4
        ], $items->diffKeys('a1', 'a2')->toArray());
    }

    public function testSlice()
    {
        $this->assertEquals(
            [
                1 => 'ole',
                2 => 'oli'
            ],
            $this->itemsArray['stringList']->slice(1, 2)->toArray()
        );
    }

    public function testSliceWithKey()
    {
        $this->assertEquals(
            [
                1 => 'Fraise',
                2 => 'Citron'
            ],
            $this->itemsArray['combine']->slice(1, 2, 'fruits')->toArray()
        );
    }

    public function testDistinct()
    {
        $this->assertEquals(
            [12, 14, 16],
            $this->makeItems([12, 14, 16, 14, 12])->distinct()->toArray()
        );
    }

    public function testDistinctWithKey()
    {
        $values = [
            'Clara' => [12, 14, 16, 14, 12],
            'Mathis' => [7, 14, 7, 14, 12],
        ];
        $this->assertEquals(
            [12, 14, 16],
            $this->makeItems($values)->distinct('Clara')->toArray()
        );
    }

    public function testSort()
    {
        $values = [12, 16, -3, 14, 4];
        $this->assertEquals([-3, 4, 12, 14, 16], $this->makeItems($values)->sort()->toArray());
    }

    public function testSortWithKey()
    {
        $values = [
            'Clara' => [12, 16, -3, 14, 4],
            'Mathis' => [7, 14, 77, 18, 12],
        ];
        $this->assertEquals([-3, 4, 12, 14, 16], $this->makeItems($values)->sort('Clara')->toArray());
        $this->assertEquals([16, 14, 12, 4, -3], $this->makeItems($values)->sort('Clara', true)->toArray());
    }

    public function testMin()
    {
        $values = [12, 16, -3, 14, 4];
        $this->assertEquals(-3, $this->makeItems($values)->min());

        $this->assertEquals([
            'name' => 'Clara',
            'year' => 2009,
            'genre' => 'female'
        ], $this->itemsArray['items']->min()->toArray());
    }

    public function testMinWithKeys()
    {
        $values = [
            'Clara' => [12, 16, -3, 14, 4],
            'Mathis' => [7, 14, 77, 18, 12],
        ];
        $this->assertEquals(-3, $this->makeItems($values)->min('Clara'));
    }

    public function testMax()
    {
        $values = [12, 16, -3, 14, 4];
        $this->assertEquals(16, $this->makeItems($values)->max());

        $this->assertEquals([
            'name' => 'Raphaël',
            'year' => 2007,
            'genre' => 'male'
        ], $this->itemsArray['items']->max()->toArray());
    }

    public function testMaxWithKeys()
    {
        $values = [
            'Clara' => [12, 16, -3, 14, 4],
            'Mathis' => [7, 14, 77, 18, 12],
        ];
        $this->assertEquals(16, $this->makeItems($values)->max('Clara'));
    }

    public function testTypesMap()
    {
        $this->assertEquals([
            'name' => 'string',
            'year' => 'integer',
            'genre' => 'string'
        ], $this->itemsArray['item']->typesMap()->toArray());
    }

    public function testTypesMapWithKey()
    {
        $this->assertEquals([
            'name' => 'string',
            'year' => 'integer',
            'genre' => 'string'
        ], $this->itemsArray['items']->typesMap(0)->toArray());
    }

    public function testHasValue()
    {
        $values = [
            'Clara' => [12, 16, -3, 14, 4],
            'Mathis' => [7, 14, 77, 18, 12],
        ];
        $this->assertTrue($this->makeItems($values['Clara'])->hasValue(14));
        $this->assertFalse($this->makeItems($values['Clara'])->hasValue(77));
    }

    public function testHasValueWithKey()
    {
        $values = [
            'Clara' => [12, 16, -3, 14, 4],
            'Mathis' => [7, 14, 77, 18, 12],
        ];
        $this->assertTrue($this->makeItems($values)->hasValue(14, 'Clara'));
        $this->assertFalse($this->makeItems($values)->hasValue(77, 'Clara'));
    }

    public function testArrayAccessInterface()
    {
        $a = [
            'name' => 'Mathis',
            'year' => 2007
        ];
        $items = $this->makeItems($a);
        $this->assertImplementArrayAccess($items, 'name', 'Mathis');
    }
}
