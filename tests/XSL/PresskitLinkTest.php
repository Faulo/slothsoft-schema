<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\XSL;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMElement;
use XSLTProcessor;

class PresskitLinkTest extends TestCase {

    private const XSLT_WRAPPER = <<<EOT
    <?xml version="1.0" encoding="UTF-8"?>
    <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:ssp="http://schema.slothsoft.net/schema/presskit">
    
    	<xsl:import href="farah://slothsoft@schema/xsl/presskit.functions" />
    
    	<xsl:template match="/link">
    		<xsl:call-template name="ssp:link" />
    	</xsl:template>
    
    	<xsl:template match="/link-and-name">
    		<xsl:call-template name="ssp:link">
    			<xsl:with-param name="link" select="link" />
    			<xsl:with-param name="name" select="name" />
    			<xsl:with-param name="rel" select="rel" />
    		</xsl:call-template>
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
            'with just link' => [
                '<link>http://somewhere</link>',
                '<a href="http://somewhere" xmlns="http://www.w3.org/1999/xhtml">http://somewhere</a>'
            ],
            'with link and name' => [
                '<link-and-name><link>http://somewhere</link><name>fancy name</name></link-and-name>',
                '<a href="http://somewhere" xmlns="http://www.w3.org/1999/xhtml">fancy name</a>'
            ],
            'with link and empty name' => [
                '<link-and-name><link>http://somewhere</link><name></name></link-and-name>',
                '<a href="http://somewhere" xmlns="http://www.w3.org/1999/xhtml">http://somewhere</a>'
            ],
            'with empty link and name' => [
                '<link-and-name><link></link><name>Bob</name></link-and-name>',
                '<span xmlns="http://www.w3.org/1999/xhtml">Bob</span>'
            ],
            'with empty' => [
                '<link-and-name><link></link><name></name></link-and-name>',
                '<code xmlns="http://www.w3.org/1999/xhtml">ssp:link requires a "link" or a "name".</code>'
            ],
            'with rel=me' => [
                '<link-and-name><link>//somewhere</link><rel>me</rel></link-and-name>',
                '<a href="//somewhere" rel="me" xmlns="http://www.w3.org/1999/xhtml">//somewhere</a>'
            ],
            'with rel=external' => [
                '<link-and-name><link>//somewhere</link><rel>me external</rel></link-and-name>',
                '<a href="//somewhere" rel="me external" target="_blank" xmlns="http://www.w3.org/1999/xhtml">//somewhere</a>'
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
            $this->assertSame($attr->value, $actual->getAttribute($attr->name), "Expected attribute '$attr->name' with value '$attr->value'.");
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
