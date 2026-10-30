<?php
declare(strict_types = 1);

namespace Slothsoft\Schema\Documentation;

use PHPUnit\Framework\TestCase;
use DOMDocument;
use DOMXPath;

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
        
        $this->assertSame(2, $xpath->query('/manifest/element')->length);
        $this->assertSame(1, $xpath->query('/manifest/type[@name="Record"]')->length);
        $this->assertSame(1, $xpath->query('/manifest/element[@name="record"]/typeReference/type[@name="Record"]/attributeReference')->length);
    }
    
    public function testComplexTypeNameIsEmptyForSimpleTypes(): void {
        $file = new XSDFile(dirname(__DIR__, 2) . '/test-files/documentation/references.xsd');
        
        $this->assertSame('Record', $file->getElementByName('record')->getComplexType());
        $this->assertSame('', $file->getElementByName('label')->getComplexType());
    }
}
