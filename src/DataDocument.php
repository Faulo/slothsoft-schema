<?php
declare(strict_types = 1);

namespace Slothsoft\Schema;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;

class DataDocument {
    
    /** @var array{name: string, attributes: array<string, string>, elements: array<array<string, mixed>>} */
    protected array $schema = [
        'name' => '',
        'attributes' => [],
        'elements' => []
    ];
    
    protected DOMXPath $dataXPath;
    
    protected DOMDocument $dataNode;
    
    /** @var list<DataElement> */
    protected array $elementList = [];
    
    /** @param array<string, mixed> $schema */
    public function __construct(DOMXPath $dataXPath, array $schema, DOMDocument $dataNode) {
        foreach ($this->schema as $key => &$val) {
            if (isset($schema[$key])) {
                $val = $schema[$key];
            }
        }
        unset($val);
        
        $this->dataXPath = $dataXPath;
        $this->dataNode = $dataNode;
        
        foreach ($this->schema['elements'] as $childSchema) {
            $childNodeList = $this->dataXPath->evaluate($childSchema['name'], $this->dataNode);
            $hasChildElement = false;
            if ($childNodeList instanceof DOMNodeList) {
                foreach ($childNodeList as $childNode) {
                    if ($childNode instanceof DOMElement) {
                        $this->elementList[] = new DataElement($this->dataXPath, $childSchema, $childNode);
                        $hasChildElement = true;
                    }
                }
            }
            if (! $hasChildElement) {
                $childNode = $this->dataNode->createElement($childSchema['name']);
                $this->dataNode->appendChild($childNode);
                $this->elementList[] = new DataElement($this->dataXPath, $childSchema, $childNode);
            }
        }
    }
}
