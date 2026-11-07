<?php
declare(strict_types = 1);

namespace Slothsoft\Schema\XSL;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\TestCase;
use XSLTProcessor;

final class SchemaTitleTest extends TestCase {
    
    /**
     * @runInSeparateProcess
     */
    public function testDocumentTitleKeepsTranslationWithoutHeadingLink(): void {
        $source = new DOMDocument();
        $source->loadXML('<data><item name="schema-info"><schema xmlns:ssv="http://schema.slothsoft.net/schema/versioning"><ssv:info><ssv:name>Editor &amp; tools</ssv:name><ssv:version>0.4</ssv:version><ssv:revision>R20261107</ssv:revision></ssv:info></schema></item></data>');
        $template = new DOMDocument();
        $template->load('farah://slothsoft@schema/xsl/schema.global');
        $processor = new XSLTProcessor();
        $processor->importStylesheet($template);
        $result = $processor->transformToDoc($source);
        $this->assertInstanceOf(DOMDocument::class, $result);
        $xpath = new DOMXPath($result);
        $xpath->registerNamespace('html', 'http://www.w3.org/1999/xhtml');
        $xpath->registerNamespace('sfd', 'http://schema.slothsoft.net/farah/dictionary');
        $this->assertSame(0.0, $xpath->evaluate('count(//html:title//html:a)'));
        $this->assertSame('title', $xpath->evaluate('string(//html:title/html:span[@sfd:dict="."])'));
        $this->assertSame('titleEditor & tools v0.4 R20261107', $xpath->evaluate('string(//html:title)'));
        $this->assertSame('/', $xpath->evaluate('string(//html:h1/html:a/@href)'));
        $this->assertSame($xpath->evaluate('string(//html:title)'), $xpath->evaluate('string(//html:h1)'));
    }
}
