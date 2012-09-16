<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Ben Selby <benmatselby@gmail.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

/**
 * Checkstyle transformation writer test suite
 */
class CheckstyleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that the Checkstyle writer can identify parse_markers in the
     * structure.xml file and then build a checkstyle.xml checkstyle report
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Checkstyle::transform
     *
     * @param string $structure The xml in structure.xml
     * @param string $expected  The expected XML in checkstyle.xml
     *
     * @dataProvider provideDataForCheckStyleTransformation
     *
     * @return void
     */
    public function testTransformCanIdentifyParseMarkersAndCreateCheckstyleReport(
        $structure, $expected
    ) {
        $tr = new \phpDocumentor\Transformer\Transformer();
        $tr->setTarget('/tmp');
        $t = new \phpDocumentor\Transformer\Transformation(
            $tr, '', 'Checkstyle', '', '/checkstyle.xml'
        );

        $expectedDom = new \DOMDocument();
        $expectedDom->loadXML($expected);

        $document = new \DOMDocument();
        $document->loadXML($structure);

        $writer = new Checkstyle();
        $writer->transform($document, $t);

        $this->assertFileExists('/tmp/checkstyle.xml');
        $actual = file_get_contents('/tmp/checkstyle.xml');
        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for
     *     testTransformCanIdentifyParseMarkersAndCreateCheckstyleReport.
     *
     * @return array
     */
    public function provideDataForCheckStyleTransformation()
    {
        return array(
            // Data Set 0
            // Contains one critical error
            array(
                '<?xml version="1.0"?><project version="0.15-DEV" title="">
      <file path="Some/File.php" hash="534515a7fda68473748ffde6ae32f5ad" package="SomePackage">
        <parse_markers>
          <critical line="22">No short description for property $property</critical>
        </parse_markers>
      </file>
      </project>',
                '<?xml version="1.0"?>
<checkstyle version="1.3.0">
  <file name="Some/File.php">
    <error line="22" severity="critical" message="No short description for property $property" source="phpDocumentor.phpDocumentor.phpDocumentor"/>
  </file>
</checkstyle>
'
            ),

            // Data Set 1
            // Contains an error, critical, notice and warning
            array(
                '<?xml version="1.0"?><project version="0.15-DEV" title="">
                  <file path="Some/File.php" hash="534515a7fda68473748ffde6ae32f5ad" package="SomePackage">
                    <parse_markers>
                      <error line="1">Some kind of error</error>
                      <critical line="2">Some kind of critical issue</critical>
                      <notice line="3">Some kind of notice</notice>
                      <warning line="4">Some kind of warning</warning>
                    </parse_markers>
                  </file>
                  </project>',
                '<?xml version="1.0"?>
<checkstyle version="1.3.0">
  <file name="Some/File.php">
    <error line="1" severity="error" message="Some kind of error" source="phpDocumentor.phpDocumentor.phpDocumentor"/>
    <error line="2" severity="critical" message="Some kind of critical issue" source="phpDocumentor.phpDocumentor.phpDocumentor"/>
    <error line="3" severity="notice" message="Some kind of notice" source="phpDocumentor.phpDocumentor.phpDocumentor"/>
    <error line="4" severity="warning" message="Some kind of warning" source="phpDocumentor.phpDocumentor.phpDocumentor"/>
  </file>
</checkstyle>
'
            ),
        );
    }
}