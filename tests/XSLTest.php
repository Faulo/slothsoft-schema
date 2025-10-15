<?php
declare(strict_types = 1);
namespace Slothsoft\Schema;

use PHPUnit\Framework\TestCase;
use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\DOMTests\DOMNodeEqualTo;
use DOMDocument;

class XSLTest extends TestCase {
    
    public function exampleProvider(): array {
        return [
            'farah://slothsoft@schema/xsl/historical-games-night.1.0-to-2.0' => [
                'farah://slothsoft@schema/xsl/historical-games-night.1.0-to-2.0',
                'test-files/historical-games-night/index-1.0.xml',
                'farah://slothsoft@schema/examples/historical-games-night/index'
            ]
        ];
    }
    
    /**
     *
     * @dataProvider exampleProvider
     */
    public function test_xslTemplate(string $templateFile, string $inputFile, string $expectedFile): void {
        $dom = new DOMHelper();
        $actualDocument = $dom->transformToDocument($inputFile, $templateFile);
        
        $expectedDocument = new DOMDocument();
        $expectedDocument->load($expectedFile, LIBXML_NOBLANKS);
        
        $this->assertThat($actualDocument, new DOMNodeEqualTo($expectedDocument));
    }
}