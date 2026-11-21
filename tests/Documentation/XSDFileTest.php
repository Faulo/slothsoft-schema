<?php
declare(strict_types = 1);

namespace Slothsoft\Schema\Documentation;

use DOMDocument;
use DOMNodeList;
use DOMXPath;
use PHPUnit\Framework\TestCase;

/**
 * XSDFileTest
 *
 * @see XSDFile
 *
 */
final class XSDFileTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(XSDFile::class), "Failed to load class 'Slothsoft\Schema\Documentation\XSDFile'!");
    }
    
    public function testManifestResolvesNamedTypeReferences(): void {
        $file = new XSDFile(dirname(__DIR__, 2) . '/test-files/documentation/references.xsd');
        $manifest = $file->asManifest();
        $this->assertInstanceOf(DOMDocument::class, $manifest);
        $xpath = new DOMXPath($manifest);
        
        $nodes = $xpath->query('/manifest/element');
        $this->assertInstanceOf(DOMNodeList::class, $nodes);
        $this->assertSame(2, $nodes->length);
        $nodes = $xpath->query('/manifest/type[@name="Record"]');
        $this->assertInstanceOf(DOMNodeList::class, $nodes);
        $this->assertSame(1, $nodes->length);
        $nodes = $xpath->query('/manifest/element[@name="record"]/typeReference/type[@name="Record"]/attributeReference');
        $this->assertInstanceOf(DOMNodeList::class, $nodes);
        $this->assertSame(1, $nodes->length);
    }
    
    public function testComplexTypeNameIsEmptyForSimpleTypes(): void {
        $file = new XSDFile(dirname(__DIR__, 2) . '/test-files/documentation/references.xsd');
        
        $element = $file->getElementByName('record');
        $this->assertInstanceOf(XSDElement::class, $element);
        $this->assertSame('Record', $element->getComplexType());
        $element = $file->getElementByName('label');
        $this->assertInstanceOf(XSDElement::class, $element);
        $this->assertSame('', $element->getComplexType());
    }
    
    public function testExcelOutputAllowsMissingExampleContent(): void {
        $file = new XSDFile(dirname(__DIR__, 2) . '/test-files/documentation/references.xsd');
        $xpath = new DOMXPath($file->asExcel());
        
        $this->assertSame(1.0, $xpath->evaluate('count(/excel/XSDElement[@name="record"][@baseType=""][@example=""])'));
        $this->assertSame('string', $xpath->evaluate('string(/excel/XSDElement[@name="label"]/@example)'));
    }
}
