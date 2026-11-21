<?php
declare(strict_types = 1);

namespace Slothsoft\Schema;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\TestCase;

/**
 * DataDocumentTest
 *
 * @see DataDocument
 */
final class DataDocumentTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(DataDocument::class), "Failed to load class 'Slothsoft\Schema\DataDocument'!");
    }
    
    public function testCreatesMissingDocumentElement(): void {
        $document = new DOMDocument();
        new DataDocument(new DOMXPath($document), ['elements' => [['name' => 'root']]], $document);
        
        $this->assertSame('root', $document->documentElement?->localName);
    }
    
    public function testUsesExistingDocumentElement(): void {
        $document = new DOMDocument();
        $root = $document->createElement('root');
        $document->appendChild($root);
        new DataDocument(new DOMXPath($document), ['elements' => [['name' => 'root']]], $document);
        
        $this->assertSame($root, $document->documentElement);
    }
}
