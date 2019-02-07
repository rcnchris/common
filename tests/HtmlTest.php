<?php
use Rcnchris\Common\Html;
use Rcnchris\Common\Items;
use Rcnchris\Common\Url;
use Tests\Rcnchris\Common\BaseTestCase;

class HtmlTest extends BaseTestCase
{
    /**
     * @var Html
     */
    private $html;

    public function setUp()
    {
        $this->html = Html::getInstance();
    }

    public function testInstance()
    {
        $this->ekoTitre('Common - HTML');
        $this->assertInstanceOf(Html::class, $this->html);
    }

    public function testHelp()
    {
        $this->assertHasHelp($this->html);
    }

    public function testLink()
    {
        $expect = '<a href="http://google.fr">Google</a>';
        $this->assertSimilar($expect, $this->html->link('http://google.fr', 'Google'));
    }

    public function testLinkWithoutLabel()
    {
        $expect = '<a href="http://google.fr">http://google.fr</a>';
        $this->assertSimilar($expect, $this->html->link('http://google.fr'));
    }

    public function testLinkWithAttribute()
    {
        $expect = '<a class="btn btn-primary" href="http://google.fr">Google</a>';
        $this->assertSimilar($expect, $this->html->link('http://google.fr', 'Google', ['class' => 'btn btn-primary']));
    }

    public function testImage()
    {
        $expect = '<img alt="Test unitaire" src="path/to/file.png">';
        $this->assertSimilar($expect, $this->html->img('path/to/file.png', ['alt' => 'Test unitaire']));
    }

    public function testMakeList()
    {
        $expect = '<ul><li>ola</li><li>ole</li></ul>';
        $this->assertSimilar($expect, $this->html->liste(['ola', 'ole']));
    }

    public function testMakeListWithObjectValue()
    {
        $expect = '<ul><li>DateTime</li><li>stdClass</li></ul>';
        $this->assertSimilar($expect, $this->html->liste([new \DateTime(), new stdClass()]));
    }

    public function testMakeListWithObjectValueWithKey()
    {
        $expect = '<ul><li>0 : DateTime</li><li>1 : stdClass</li></ul>';
        $this->assertSimilar($expect, $this->html->liste([new \DateTime(), new stdClass()], ['type' => 'ul'], true));
    }

    public function testMakeListWithArrayValue()
    {
        $expect = '<ul><li>Mathis</li><li>Clara</li></ul>';
        $this->assertSimilar(
            $expect,
            $this->html->liste([
                ['name' => 'Mathis'],
                ['name' => 'Clara'],
            ]));
    }

    public function testMakeListWithArrayValueWithKey()
    {
        $expect = '<ul><li>0 : Mathis</li><li>1 : Clara</li></ul>';
        $this->assertSimilar(
            $expect,
            $this->html->liste(
                [
                    ['name' => 'Mathis'],
                    ['name' => 'Clara']
                ],
                ['type' => 'ul'],
                true
            )
        );
    }

    public function testMakeListWithRessource()
    {
        $expect = '<ul><li>0 : curl</li><li>1 : stream</li></ul>';
        $this->assertSimilar(
            $expect,
            $this->html->liste(
                [
                    curl_init(),
                    fopen(__FILE__, 'r')
                ],
                ['type' => 'ul'],
                true
            )
        );
    }

    public function testMakeListWithKey()
    {
        $expect = '<ol><li>0 : ola</li><li>1 : ole</li></ol>';
        $this->assertSimilar($expect, $this->html->liste(['ola', 'ole'], ['type' => 'ol'], true));
    }

    public function testMakeListOl()
    {
        $expect = '<ol><li>ola</li><li>ole</li></ol>';
        $this->assertSimilar($expect, $this->html->liste(['ola', 'ole'], ['type' => 'ol']));
    }

    public function testMakeListDl()
    {
        $expect = '
            <dl>
                <dt>0</dt>
                <dd>ola</dd>
                <dt>1</dt>
                <dd>ole</dd>
            </dl>
        ';
        $this->assertSimilar($expect, $this->html->liste(['ola', 'ole'], ['type' => 'dl']));
    }

    public function testDetails()
    {
        $expect = '
            <details>
                <summary>ola</summary>
                ole
            </details>
        ';
        $this->assertSimilar($expect, $this->html->details('ola', 'ole'));
    }

    public function testTableWithSimpleList()
    {
        $list = ['ola', 'ole', 'oli'];
        $expect = '
        <table>
            <tbody>
                <tr><th>0</th><td>ola</td></tr>
                <tr><th>1</th><td>ole</td></tr>
                <tr><th>2</th><td>oli</td></tr>
            </tbody>
        </table>
        ';
        $this->assertSimilar($expect, $this->html->table($list));
    }

    public function testTableWithSimpleListWithoutHeader()
    {
        $list = ['ola', 'ole', 'oli'];
        $expect = '
        <table>
            <tbody>
                <tr><td>ola</td></tr>
                <tr><td>ole</td></tr>
                <tr><td>oli</td></tr>
            </tbody>
        </table>
        ';
        $this->assertSimilar($expect, $this->html->table($list, [], false));
    }

    public function testTableRecursive()
    {
        $list = [
            'list1' => ['ola', 'ole', 'oli'],
            'list2' => ['olo', 'olu', 'oly'],
        ];
        $expect = '
        <table>
            <tbody>
                <tr>
                    <th>list1</th>
                    <td>
                        <table>
                            <tbody>
                                <tr><th>0</th><td>ola</td></tr>
                                <tr><th>1</th><td>ole</td></tr>
                                <tr><th>2</th><td>oli</td></tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>list2</th>
                    <td>
                        <table>
                            <tbody>
                                <tr><th>0</th><td>olo</td></tr>
                                <tr><th>1</th><td>olu</td></tr>
                                <tr><th>2</th><td>oly</td></tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        ';
        $this->assertSimilar($expect, $this->html->table($list));
    }

    public function testTableWithSimpleListWithCaption()
    {
        $list = ['ola', 'ole', 'oli'];
        $expect = '
        <table>
            <caption>Avec un titre</caption>
            <tbody>
                <tr><th>0</th><td>ola</td></tr>
                <tr><th>1</th><td>ole</td></tr>
                <tr><th>2</th><td>oli</td></tr>
            </tbody>
        </table>
        ';
        $this->assertSimilar($expect, $this->html->table($list, ['caption' => 'Avec un titre']));
    }

    public function testTableWithSimpleListColMode()
    {
        $list = [
            'ola',
            'ole',
            'oli',
            (new \DateTime())->createFromFormat('d-m-Y H:i:s', '15-10-1975 05:15:05'),
            (new Url('http://php.net/manual/fr/'))
        ];
        $expect = '
        <table>
            <thead>
                <tr>
                    <th>0</th>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>ola</td>
                    <td>ole</td>
                    <td>oli</td>
                    <td>15-10-1975 05:15:05</td>
                    <td>http://php.net/manual/fr/</td>
                </tr>
            </tbody>
        </table>
        ';
        $this->assertSimilar($expect, $this->html->table($list, [], true, true));
    }

    public function testTableWithSimpleListColModeRecursive()
    {
        $list = [
            'list1' => ['ola', 'ole', 'oli'],
            'list2' => ['olo', 'olu', 'oly'],
        ];
        $expect = '
        <table>
            <thead>
                <tr>
                    <th>list1</th>
                    <th>list2</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <table>
                            <thead>
                                <tr>
                                    <th>0</th>
                                    <th>1</th>
                                    <th>2</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ola</td>
                                    <td>ole</td>
                                    <td>oli</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td>
                        <table>
                            <thead>
                                <tr>
                                    <th>0</th>
                                    <th>1</th>
                                    <th>2</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>olo</td>
                                    <td>olu</td>
                                    <td>oly</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        ';
        $this->assertSimilar($expect, $this->html->table($list, [], true, true));
    }

    public function testTableWithAttributes()
    {
        $list = ['ola', 'ole', 'oli'];
        $expect = '
        <table class="table table-sm">
            <tbody>
                <tr><th>0</th><td>ola</td></tr>
                <tr><th>1</th><td>ole</td></tr>
                <tr><th>2</th><td>oli</td></tr>
            </tbody>
        </table>
        ';
        $this->assertSimilar($expect, $this->html->table($list, ['class' => 'table table-sm']));
    }

    public function testTableWithAssociativeArray()
    {
        $list = [
            'Mathis' => 12,
            'Raphaël' => 14,
            'Clara' => 16,
            'Date' => (new \DateTime())->createFromFormat('d-m-Y H:i:s', '15-10-1975 05:15:05'),
            'Diff' => (new \DateTime())->createFromFormat('d-m-Y H:i:s', '15-10-1975 05:15:05')->diff(new \DateTime()),
            'Items' => new Items('ola,ole'),
            'Url' => new Url('http://php.net/manual/fr/')
        ];
        $expect = '
        <table>
            <tbody>
                <tr><th>Mathis</th><td>12</td></tr>
                <tr><th>Raphaël</th><td>14</td></tr>
                <tr><th>Clara</th><td>16</td></tr>
                <tr><th>Date</th><td>15-10-1975 05:15:05</td></tr>
                <tr><th>Diff</th><td>DateInterval</td></tr>
                <tr><th>Items</th><td><table><tbody><tr><th>0</th><td>ola</td></tr><tr><th>1</th><td>ole</td></tr></tbody></table></td></tr>
                <tr><th>Url</th><td>http://php.net/manual/fr/</td></tr>
            </tbody>
        </table>
        ';
        $this->assertSimilar($expect, $this->html->table($list));
    }

    public function testTableWithAssociativeArrayColMode()
    {
        $list = [
            'Mathis' => 12,
            'Raphaël' => 14,
            'Clara' => 16,
            'Date' => (new \DateTime())->createFromFormat('d-m-Y H:i:s', '15-10-1975 05:15:05'),
            'Diff' => (new \DateTime())->createFromFormat('d-m-Y H:i:s', '15-10-1975 05:15:05')->diff(new \DateTime()),
            'Items' => new Items('ola,ole'),
        ];
        $expect = '
        <table>
            <thead><tr><th>Mathis</th><th>Raphaël</th><th>Clara</th><th>Date</th><th>Diff</th><th>Items</th></tr></thead>
            <tbody>
                <tr>
                    <td>12</td>
                    <td>14</td>
                    <td>16</td>
                    <td>15-10-1975 05:15:05</td>
                    <td>DateInterval</td>
                    <td>
                        <table>
                            <thead><tr><th>0</th><th>1</th></tr></thead>
                            <tbody><tr><td>ola</td><td>ole</td></tr></tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        ';
        $this->assertSimilar($expect, $this->html->table($list, [], true, true));
    }

    public function testGetFieldInputText()
    {
        $expect = '<input id="year" name="year" type="text" value="2018">';
        $this->assertSimilar($expect, $this->html->field('year', 2018));
    }

    public function testGetFieldInputTextDisabled()
    {
        $expect = '<input disabled id="year" name="year" type="text" value="2018">';
        $this->assertSimilar($expect, $this->html->field('year', 2018, ['disabled' => true]));
    }

    public function testGetFieldInputTextRequired()
    {
        $expect = '<input id="year" name="year" required type="text" value="2018">';
        $this->assertSimilar($expect, $this->html->field('year', 2018, ['required' => true]));
    }

    public function testGetFieldInputTextWithPlaceHolder()
    {
        $expect = '<input class="form-control" id="year" name="year" placeholder="Année" type="text" value="2018">';
        $this->assertSimilar($expect,
            $this->html->field('year', 2018, ['class' => 'form-control', 'placeholder' => 'Année']));
    }

    public function testGetFieldInputTextWithClass()
    {
        $expect = '<input class="form-control" id="year" name="year" type="text" value="2018">';
        $this->assertSimilar($expect, $this->html->field('year', 2018, ['class' => 'form-control']));
    }

    public function testGetFieldInputTextWithLabel()
    {
        $expect = '<label for="year">Année</label><input class="form-control" id="year" name="year" type="text" value="2018">';
        $this->assertSimilar($expect,
            $this->html->field('year', 2018, ['class' => 'form-control', 'label' => 'Année']));
    }

    public function testGetFieldInputDate()
    {
        $birthday = (new \DateTime())->createFromFormat('d-m-Y H:i:s', '15-10-1975 05:15:05');
        $expect = '<input id="birthday" name="birthday" type="text" value="1975-10-15 05:15:05">';
        $this->assertSimilar($expect, $this->html->field('birthday', $birthday));
    }

    public function testGetFieldInputArray()
    {
        $items = ['ola', 'ole', 'oli'];
        $expect = '<input id="items" name="items" type="text" value="a:3:{i:0;s:3:"ola";i:1;s:3:"ole";i:2;s:3:"oli";}">';
        $this->assertSimilar($expect, $this->html->field('items', $items));
    }

    public function testGetFieldInputCheckbox()
    {
        $expect = '<input name="vrai" type="hidden" value="0"><input id="vrai" name="vrai" type="checkbox" value="0">';
        $this->assertSimilar($expect, $this->html->field('vrai', 0, ['type' => 'checkbox']));

        $expect = '<input name="vrai" type="hidden" value="0"><input checked id="vrai" name="vrai" type="checkbox">';
        $this->assertSimilar($expect, $this->html->field('vrai', 1, ['type' => 'checkbox']));
    }

    public function testGetFieldInputRadio()
    {
        $expect = '<input name="vrai" type="hidden" value="0"><input id="vrai" name="vrai" type="radio" value="0">';
        $this->assertSimilar($expect, $this->html->field('vrai', 0, ['type' => 'radio']));

        $expect = '<input name="vrai" type="hidden" value="0"><input checked id="vrai" name="vrai" type="radio">';
        $this->assertSimilar($expect, $this->html->field('vrai', 1, ['type' => 'radio']));
    }

    public function testGetFieldInputFile()
    {
        $expect = '<input id="vrai" name="vrai" type="file" value="">';
        $this->assertSimilar($expect, $this->html->field('vrai', null, ['type' => 'file']));
    }

    public function testGetFieldTextarea()
    {
        $expect = '<textarea cols="25" id="year" name="year" rows="5">2018</textarea>';
        $this->assertSimilar($expect,
            $this->html->field('year', 2018, ['type' => 'textarea', 'rows' => 5, 'cols' => 25]));
    }

    public function testGetFieldSelect()
    {
        $items = ['ola', 'ole', 'oli'];
        $expect = '
            <select id="selUnchecked" name="selUnchecked">
                <option value="0">ola</option>
                <option value="1">ole</option>
                <option value="2">oli</option>
            </select>';
        $this->assertSimilar($expect, $this->html->field('selUnchecked', null, ['items' => $items]));
    }

    public function testGetFieldSelectSelected()
    {
        $items = ['ola', 'ole', 'oli'];
        $expect = '
            <select id="selChecked" name="selChecked">
                <option value="0">ola</option>
                <option selected value="1">ole</option>
                <option value="2">oli</option>
            </select>';
        $this->assertSimilar($expect, $this->html->field('selChecked', 1, ['items' => $items]));
    }

    public function testGetFieldSelectMultiple()
    {
        $items = ['ola', 'ole', 'oli'];
        $expect = '
            <select id="selChecked" multiple name="selChecked">
                <option value="0">ola</option>
                <option value="1">ole</option>
                <option value="2">oli</option>
            </select>';
        $this->assertSimilar($expect, $this->html->field('selChecked', null, ['items' => $items, 'multiple' => true]));
    }

    public function testGetFieldSelectWithEmpty()
    {
        $items = ['ola', 'ole', 'oli'];
        $expect = '
            <select id="selEmpty" name="selEmpty">
                <option value=""></option>
                <option value="0">ola</option>
                <option selected value="1">ole</option>
                <option value="2">oli</option>
            </select>';
        $this->assertSimilar($expect, $this->html->field('selEmpty', 1, ['items' => $items, 'empty' => true]));
    }

    public function testButton()
    {
        $expect = '<button type="submit">Feu</button>';
        $this->assertSimilar($expect, $this->html->button('Feu'));
    }

    public function testIcon()
    {
        $this->assertEquals('<i class="fa fa-home"></i>', $this->html->icon('home'));
        $this->assertEquals('<span class="icon-home"></span>', $this->html->icon('icon-home', 'other'));
    }

    public function testSurround()
    {
        $content = 'ola les gens';
        $tag = 'em';
        $attributes = ['class' => 'bg-warning'];
        $expected = '<em class="bg-warning">ola les gens</em>';
        $this->assertSimilar($expected, Html::surround($content, $tag, $attributes));
    }

    public function testSurroundWithWrongTag()
    {
        $content = 'ola les gens';
        $tag = 1452;
        $this->assertNull(Html::surround($content, $tag));

        $tag = ['em'];
        $this->assertNull(Html::surround($content, $tag));
    }
}