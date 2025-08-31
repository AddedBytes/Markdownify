<?php

namespace Test\Markdownify;

use Generator;
use Markdownify\ConverterExtra;
use PHPUnit\Framework\Attributes\DataProvider;

class ConverterExtraTest extends ConverterTestCase
{


    /* UTILS
     *************************************************************************/
    public function setUp(): void
    {
        $this->converter = new ConverterExtra;
    }


    /* HEADING TEST METHODS
     *************************************************************************/
    #[DataProvider('providerHeadingConversion_withAttribute')]
    public function testHeadingConversion_withAttribute(int $level, string $attributesHTML, string|null $attributesMD = null)
    {
        $innerHTML = 'Heading ' . $level;
        $md = str_pad('', $level, '#') . ' ' . $innerHTML . $attributesMD;
        $html = '<h' . $level . $attributesHTML . '>' . $innerHTML . '</h' . $level . '>';
        $this->assertEquals($md, $this->converter->parseString($html));
    }

    public static function providerHeadingConversion_withAttribute(): Generator
    {
        $attributes = [' id="idAttribute"', ' class=" class1  class2 "'];
        for ($i = 1; $i <= 6; $i++) {
            yield [$i, '', ''];
            yield [$i, $attributes[0], ' {#idAttribute}'];
            yield [$i, $attributes[1], ' {.class1.class2}'];
            yield [$i, $attributes[0] . $attributes[1], ' {#idAttribute.class1.class2}'];
        }
    }


    /* LINK TEST METHODS
     *************************************************************************/
    public static function providerLinkConversion(): Generator
    {
        // Link with href + title + id attributes
        $data['url-title-id']['md'] = 'This is [an example][1]{#myLink} inline link.

 [1]: http://example.com/ "Title"';
        $data['url-title-id']['html'] = '<p>This is <a href="http://example.com/" title="Title" id="myLink">an example</a> inline link.</p>';

        // Link with href + title + class attributes
        $data['url-title-class']['md'] = 'This is [an example][1]{.external} inline link.

 [1]: http://example.com/ "Title"';
        $data['url-title-class']['html'] = '<p>This is <a href="http://example.com/" title="Title" class="external">an example</a> inline link.</p>';

        // Link with href + title + multiple classes attributes
        $data['url-title-multiple-class']['md'] = 'This is [an example][1]{.class1.class2} inline link.

 [1]: http://example.com/ "Title"';
        $data['url-title-multiple-class']['html'] = '<p>This is <a href="http://example.com/" title="Title" class=" class1  class2 ">an example</a> inline link.</p>';

        // Link with href + title + multiple classes attributes
        $data['url-title-multiple-class-id']['md'] = 'This is [an example][1]{#myLink.class1.class2} inline link.

 [1]: http://example.com/ "Title"';
        $data['url-title-multiple-class-id']['html'] = '<p>This is <a href="http://example.com/" title="Title" class=" class1  class2 " id="myLink">an example</a> inline link.</p>';

        foreach ($data as $key => $item) {
            yield $item;
        }
    }


    /* TABLE TEST METHODS
     *************************************************************************/
    public function testTableConversion()
    {
        $html = <<<EOF
<table>
<thead>
<tr>
  <th>First Header</th>
  <th>Second Header</th>
</tr>
</thead>
<tbody>
<tr>
  <td>Content Cell</td>
  <td>Content Cell</td>
</tr>
<tr>
  <td> </td>
  <td>Content Cell</td>
</tr>
</tbody>
</table>
EOF;
        $md = <<<EOF
| First Header | Second Header |
| ------------ | ------------- |
| Content Cell | Content Cell  |
|              | Content Cell  |
EOF;
        $this->assertEquals($md, $this->converter->parseString($html));
    }

    public function testTableConversionWithEmptyCell()
    {
        $html = <<<EOF
<table>
<thead>
<tr>
  <th>First Header</th>
  <th>Second Header</th>
</tr>
</thead>
<tbody>
<tr>
  <td>Content Cell</td>
  <td>Content Cell</td>
</tr>
<tr>
  <td></td>
  <td>Content Cell</td>
</tr>
</tbody>
</table>
EOF;
        $md = <<<EOF
| First Header | Second Header |
| ------------ | ------------- |
| Content Cell | Content Cell  |
|              | Content Cell  |
EOF;
        $this->assertEquals($md, $this->converter->parseString($html));
    }
}
