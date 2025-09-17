<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\XSL;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMElement;
use XSLTProcessor;

class PresskitDateTest extends TestCase {
    
    private const XSLT_WRAPPER = <<<EOT
    <?xml version="1.0" encoding="UTF-8"?>
    <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:ssp="http://schema.slothsoft.net/schema/presskit">
    
        <xsl:import href="farah://slothsoft@schema/xsl/presskit.functions"/>
    
        <xsl:template match="/*">
            <xsl:call-template name="ssp:date"/>
        </xsl:template>
    
    </xsl:stylesheet>
    EOT;
    
    /**
     *
     * @dataProvider provideDateTransformations
     */
    public function testDateTransformation(string $inputXml, string $expectedOutput): void {
        $this->assertMatchesXML($expectedOutput, $this->transform($inputXml));
    }
    
    public static function provideDateTransformations(): array {
        return [
            'with datetime and text' => [
                '<date datetime="2025-07-03">July 2025</date>',
                '<time datetime="2025-07-03" xmlns="http://www.w3.org/1999/xhtml">July 2025</time>'
            ],
            'with datetime only' => [
                '<date datetime="2013-12-25 11:12+0200" />',
                '<time datetime="2013-12-25 11:12+0200" xmlns="http://www.w3.org/1999/xhtml">25.12.2013</time>'
            ],
            'with value only' => [
                '<date>03.07.2025</date>',
                '<time xmlns="http://www.w3.org/1999/xhtml">03.07.2025</time>'
            ],
            'with empty' => [
                '<date></date>',
                '<code xmlns="http://www.w3.org/1999/xhtml">ssp:date requires a "datetime" attribute or text content.</code>'
            ]
        ];
    }
    
    private function assertMatchesXML(string $expectedXml, DOMElement $actual) {
        $expectedDocument = new DOMDocument();
        $expectedDocument->loadXML($expectedXml);
        $expected = $expectedDocument->documentElement;
        
        $this->assertSame($expected->localName, $actual->localName);
        $this->assertSame($expected->namespaceURI, $actual->namespaceURI);
        $this->assertSame($expected->textContent, $actual->textContent);
        foreach ($expected->attributes as $attr) {
            $this->assertSame($attr->value, $actual->getAttribute($attr->name));
        }
    }
    
    private function transform(string $xmlString): DOMElement {
        $data = new DOMDocument();
        $data->loadXML($xmlString);
        
        $template = new DOMDocument();
        $template->loadXML(self::XSLT_WRAPPER);
        
        $xslt = new XSLTProcessor();
        $xslt->registerPHPFunctions();
        $xslt->importStylesheet($template);
        
        return $xslt->transformToDoc($data)->documentElement;
    }
}
