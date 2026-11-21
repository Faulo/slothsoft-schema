<?php
declare(strict_types = 1);

namespace Slothsoft\Schema;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use LogicException;

class DataElement {
    
    /** @param array{name: string, attributes?: array<string, string>, elements?: array<array<string, mixed>>} $schema */
    public static function loadDocument(DOMDocument $doc, array $schema): ?DataElement {
        $ret = null;
        $xpath = new DOMXPath($doc);
        $nodeList = $xpath->evaluate($schema['name']);
        if ($nodeList instanceof DOMNodeList) {
            foreach ($nodeList as $node) {
                if ($node instanceof DOMElement) {
                    $ret = new DataElement($xpath, $schema, $node);
                }
            }
        }
        return $ret;
    }
    
    /** @var array{name: string, attributes: array<string, string>, elements: array<array<string, mixed>>} */
    protected array $schema = [
        'name' => '',
        'attributes' => [],
        'elements' => []
    ];
    
    protected DOMXPath $dataXPath;
    
    protected DOMElement $dataNode;
    
    /** @var list<DataElement> */
    protected array $elementList = [];
    
    /** @param array<string, mixed> $schema */
    public function __construct(DOMXPath $dataXPath, array $schema, DOMElement $dataNode) {
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
            if ($childNodeList instanceof DOMNodeList) {
                foreach ($childNodeList as $childNode) {
                    if ($childNode instanceof DOMElement) {
                        $this->elementList[] = new DataElement($this->dataXPath, $childSchema, $childNode);
                    }
                }
            }
        }
    }
    
    /** @param array<string, string> $attributeList */
    public function appendAttributeList(array $attributeList): void {
        foreach ($attributeList as $key => $val) {
            $this->dataNode->setAttribute($key, $val);
        }
    }
    
    /** @param array<array<string, string>> $dataList */
    public function appendDataList(string $name, array $dataList): void {
        foreach ($this->schema['elements'] as $childSchema) {
            if ($childSchema['name'] === $name) {
                foreach ($dataList as $data) {
                    $doc = $this->dataNode->ownerDocument;
                    if ($doc === null) {
                        throw new LogicException('Cannot append data to an element without an owner document.');
                    }
                    $childNode = $doc->createElementNS($this->dataNode->namespaceURI, $childSchema['name']);
                    foreach ($data as $key => $val) {
                        $childNode->setAttribute($key, $val);
                    }
                    $this->dataNode->appendChild($childNode);
                    $this->elementList[] = new DataElement($this->dataXPath, $childSchema, $childNode);
                }
            }
        }
    }
    
    public function getDataNode(): DOMElement {
        return $this->dataNode;
    }
    
    /** @return list<DataElement> */
    public function getElementList(): array {
        return $this->elementList;
    }
    
    /** @return array<string, string|float|int|bool|null> */
    public function getAttributes(): array {
        $ret = [];
        foreach ($this->schema['attributes'] as $key => $type) {
            $val = $this->dataNode->hasAttribute($key) ? $this->dataNode->getAttribute($key) : null;
            switch ($type) {
                case 'string':
                    break;
                case 'float':
                    $val = floatval($val);
                    break;
                case 'integer':
                    $val = intval($val);
                    break;
                case 'boolean':
                    $val = $val !== null;
                    break;
            }
            $ret[$key] = $val;
        }
        return $ret;
    }
}