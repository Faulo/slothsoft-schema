<?php
declare(strict_types = 1);

namespace Slothsoft\Schema\Documentation;

use DOMAttr;
use DOMDocument;
use DOMDocumentFragment;
use DOMElement;
use DOMNameSpaceNode;
use DOMNode;
use DOMNodeList;
use DOMText;
use DOMXPath;
use LogicException;
use RuntimeException;

class XSDFile {
    
    protected string $file;
    
    protected DOMDocument $doc;
    
    protected DOMXPath $xpath;
    
    protected string $targetNS;
    
    protected string $targetPrefix;
    
    protected string $targetQuery;
    
    protected string $schemaNS;
    
    protected ?string $schemaPrefix = null;
    
    protected string $schemaQuery;
    
    protected bool $elementsQualified;
    
    protected bool $attributesQualified;
    
    /** @var array<string, XSDNode> */
    protected array $xsdList;
    
    public function __construct(string $file) {
        $this->file = $file;
        $this->doc = new DOMDocument();
        $this->doc->load($this->file);
        $this->xpath = new DOMXPath($this->doc);
        $this->xpath->registerNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');
        
        $targetNS = $this->xpathEvaluate('string(/xsd:schema/@targetNamespace)');
        $this->targetNS = is_string($targetNS) ? $targetNS : '';
        $this->targetPrefix = basename($this->targetNS);
        $this->targetQuery = sprintf('/^%s:(\w+)$/', $this->targetPrefix);
        
        $this->schemaNS = 'http://www.w3.org/2001/XMLSchema';
        $this->schemaPrefix = $this->xpath->document->lookupPrefix($this->schemaNS);
        $this->schemaQuery = sprintf('/^%s:(\w+)$/', $this->schemaPrefix);
        
        $this->elementsQualified = $this->xpathEvaluate('boolean(/xsd:schema/@elementFormDefault = "qualified")') === true;
        $this->attributesQualified = $this->xpathEvaluate('boolean(/xsd:schema/@attributeFormDefault = "qualified")') === true;
        
        $includeList = array();
        $overrideNodeList = array();
        while ($nodeList = $this->xpathElements('/xsd:schema/xsd:include | /xsd:schema/xsd:redefine')) {
            foreach ($nodeList as $oldNode) {
                $newNode = $this->doc->createDocumentFragment();
                if ($include = $oldNode->getAttribute('schemaLocation')) {
                    $include = dirname($this->doc->documentURI ?? $this->file) . DIRECTORY_SEPARATOR . $include;
                    if ($include = realpath($include)) {
                        if (! isset($includeList[$include])) {
                            $includeList[$include] = true;
                        }
                        $includeDoc = new DOMDocument();
                        $includeDoc->load($include);
                        foreach ($includeDoc->documentElement->childNodes ?? [] as $node) {
                            $newNode->appendChild($this->doc->importNode($node, true));
                        }
                    }
                }
                foreach ($this->xpathElements('xsd:complexType', $oldNode) as $heirNode) {
                    $override = $heirNode->cloneNode(true);
                    if ($override instanceof DOMElement) {
                        $overrideNodeList[] = $override;
                    }
                }
                $oldNode->parentNode?->replaceChild($newNode, $oldNode);
            }
        }
        foreach ($overrideNodeList as $heirNode) {
            $name = $heirNode->getAttribute('name');
            $isRestriction = $this->xpathEvaluate('boolean(xsd:complexContent/xsd:restriction)', $heirNode) === true;
            $elementNodeList = $this->xpathElements('xsd:complexContent/*/xsd:all | xsd:complexContent/*/xsd:sequence | xsd:complexContent/*/xsd:attributeGroup', $heirNode);
            $attributeNodeList = $this->xpathElements('xsd:complexContent/*/xsd:attribute', $heirNode);
            
            $ancestorNodeList = $this->xpathElements(sprintf('/xsd:schema/xsd:complexType[@name = "%s"]', $name));
            foreach ($ancestorNodeList as $ancestorNode) {
                $parentNodeList = $this->xpathElements('(. | xsd:complexContent/xsd:restriction | xsd:complexContent/xsd:extension)[last()]', $ancestorNode);
                foreach ($parentNodeList as $parentNode) {
                    if ($isRestriction) {
                        foreach ($this->xpathElements('xsd:all | xsd:sequence', $parentNode) as $node) {
                            $parentNode->removeChild($node);
                        }
                    }
                    foreach ($elementNodeList as $elementNode) {
                        $parentNode->appendChild($elementNode->cloneNode(true));
                    }
                    foreach ($attributeNodeList as $attributeNode) {
                        $attributeName = $attributeNode->getAttribute('name');
                        foreach ($this->xpathElements(sprintf('xsd:attribute[@name = "%s"]', $attributeName), $parentNode) as $node) {
                            $parentNode->removeChild($node);
                        }
                        $parentNode->appendChild($attributeNode->cloneNode(true));
                    }
                }
            }
        }
        $this->xsdList = array();
    }
    
    /**
     * @template T of XSDNode
     * @param class-string<T> $class
     * @return T
     */
    private function getOrCreateNode(DOMElement $node, string $className, string $class): XSDNode {
        $id = $this->getIdByNode($node, $className);
        $xsd = $this->xsdList[$id] ?? null;
        if ($xsd === null) {
            $xsd = new $class($this);
            $this->xsdList[$id] = $xsd;
            $xsd->init($node);
        }
        if (! ($xsd instanceof $class)) {
            throw new LogicException("The cached schema node '$id' is not a $class.");
        }
        return $xsd;
    }
    
    public function createXSDElement(DOMElement $node): XSDElement {
        return $this->getOrCreateNode($node, 'XSDElement', XSDElement::class);
    }
    
    public function createXSDAttribute(DOMElement $node): XSDAttribute {
        return $this->getOrCreateNode($node, 'XSDAttribute', XSDAttribute::class);
    }
    
    public function createXSDType(DOMElement $node): XSDType {
        return $this->getOrCreateNode($node, 'XSDType', XSDType::class);
    }
    
    public function createXSDCategory(DOMElement $node): XSDCategory {
        return $this->getOrCreateNode($node, 'XSDCategory', XSDCategory::class);
    }
    
    public function createXSDGroup(DOMElement $node): XSDGroup {
        return $this->getOrCreateNode($node, 'XSDGroup', XSDGroup::class);
    }
    
    public function createXSDAnnotation(DOMElement $node): XSDAnnotation {
        return $this->getOrCreateNode($node, 'XSDAnnotation', XSDAnnotation::class);
    }
    
    public function getIdByNode(DOMElement $node, string $className): string {
        return sprintf('%s(%s)', $className, $node->getNodePath());
    }
    
    public function getElementByName(string $name): ?XSDElement {
        $nodeList = $this->xpathElements(sprintf('/xsd:schema/xsd:element[@name = "%s"]', $name));
        foreach ($nodeList as $node) {
            return $this->createXSDElement($node);
        }
        return null;
    }
    
    public function getXPath(): DOMXPath {
        return $this->xpath;
    }
    
    public function getTargetNS(): string {
        return $this->targetNS;
    }
    
    public function getSchemaQuery(): string {
        return $this->schemaQuery;
    }
    
    public function getSchemaNS(): string {
        return $this->schemaNS;
    }
    
    public function asNode(DOMDocument $doc): DOMDocumentFragment|DOMText {
        $retNode = $doc->createDocumentFragment();
        $nodeList = $this->xpathElements('/xsd:schema/xsd:element[@name]');
        foreach ($nodeList as $node) {
            $retNode->appendChild($doc->createElementNS($this->targetNS, $node->getAttribute('name')));
        }
        return $retNode->hasChildNodes() ? $retNode : $doc->createTextNode('');
    }
    
    /** @return ($targetDoc is null ? DOMDocument : DOMElement) */
    public function asManifest(?DOMDocument $targetDoc = null): DOMDocument|DOMElement {
        if ($targetDoc === null) {
            $retDoc = new DOMDocument();
            $manifestNode = $retDoc->createElement('manifest');
            $retDoc->appendChild($manifestNode);
            $retNode = $retDoc;
        } else {
            $retDoc = $targetDoc;
            $manifestNode = $retDoc->createElement('manifest');
            $retNode = $manifestNode;
        }
        
        $storage = [];
        
        $nodeList = $this->xpathElements('/xsd:schema/xsd:annotation');
        foreach ($nodeList as $node) {
            $annotation = $this->createXSDAnnotation($node)->getManifestNode($retDoc, $storage);
            if ($annotation !== null) {
                $manifestNode->appendChild($annotation);
            }
        }
        
        $nodeList = $this->xpathElements('/xsd:schema/xsd:element[@name]');
        foreach ($nodeList as $node) {
            $this->createXSDElement($node)->getManifestNode($retDoc, $storage);
        }
        $nodeList = $this->xpathElements('/xsd:schema/xsd:complexType[@name]');
        foreach ($nodeList as $node) {
            $this->createXSDType($node)->getManifestNode($retDoc, $storage);
        }
        $nodeList = $this->xpathElements('/xsd:schema/xsd:simpleType[@name]');
        foreach ($nodeList as $node) {
            $this->createXSDType($node)->getManifestNode($retDoc, $storage);
        }
        
        foreach ($storage as $node) {
            if ($node !== null) {
                $manifestNode->appendChild($node);
            }
        }
        $xpath = new DOMXPath($retDoc);
        do {
            $nodeList = array();
            $references = $xpath->query('.//typeReference[not(type) and not(@id = ancestor::*/@id)]', $manifestNode);
            if ($references !== false) {
                foreach ($references as $node) {
                    if ($node instanceof DOMElement) {
                        $nodeList[] = $node;
                    }
                }
            }
            foreach ($nodeList as $node) {
                $id = $node->getAttribute('id');
                if (isset($storage[$id])) {
                    $node->appendChild($storage[$id]->cloneNode(true));
                } else {
                    $node->parentNode?->removeChild($node);
                }
            }
        } while (count($nodeList));
        
        return $retNode;
    }
    
    public function asExcel(): DOMDocument {
        $retDoc = new DOMDocument();
        $rootNode = $retDoc->createElement('excel');
        $retDoc->appendChild($rootNode);
        
        $nodeList = $this->xpathElements('/xsd:schema/xsd:element[@name]');
        foreach ($nodeList as $node) {
            if ($element = $this->getElementByName($node->getAttribute('name'))) {
                $rootNode->appendChild($element->getExcelNode($retDoc));
            }
        }
        return $retDoc;
    }
    
    public function getExampleDocument(XSDElement $xsd): DOMDocument {
        $retDoc = new DOMDocument();
        // PHP Bug: muss Namensraum-Prefix explizit und im documentElement vergeben, sonst verschwindet er
        if ($this->attributesQualified) {
            $tempNode = $retDoc->createElementNS($this->targetNS, $this->targetPrefix . ':' . $this->targetPrefix);
            $retDoc->appendChild($tempNode);
            $retDoc->createAttributeNS($this->targetNS, $this->targetPrefix . ':' . $this->targetPrefix);
        }
        $exampleNode = $xsd->getExampleNode($retDoc);
        if ($exampleNode === null) {
            throw new RuntimeException('Unable to create an example for ' . $xsd->getName());
        }
        if (isset($tempNode)) {
            $tempNode->appendChild($exampleNode);
            $retDoc->replaceChild($exampleNode, $tempNode);
        } else {
            $retDoc->appendChild($exampleNode);
        }
        $retDoc->formatOutput = true;
        return $retDoc;
    }
    
    public function createTargetElement(DOMDocument $doc, string $name): DOMElement {
        return $this->elementsQualified ? $doc->createElementNS($this->targetNS, $name) : $doc->createElement($name);
    }
    
    public function createTargetAttribute(DOMDocument $doc, string $name, ?string $value = null): DOMAttr {
        if ($this->attributesQualified) {
            $retNode = $doc->createAttributeNS($this->targetNS, $name);
        } else {
            $retNode = $doc->createAttribute($name);
        }
        if ($value !== null) {
            $retNode->appendChild($doc->createTextNode($value));
        }
        return $retNode;
    }
    
    /** @return list<DOMElement> */
    private function xpathElements(string $query, ?DOMNode $contextNode = null): array {
        $result = $this->xpathEvaluate($query, $contextNode);
        if (! is_array($result)) {
            return [];
        }
        return array_values(array_filter($result, static fn(DOMNode|DOMNameSpaceNode $node): bool => $node instanceof DOMElement));
    }
    
    /** @return list<DOMNode|DOMNameSpaceNode>|bool|float|string */
    public function xpathEvaluate(string $query, ?DOMNode $contextNode = null): array|bool|float|string {
        $result = $this->xpath->evaluate($query, $contextNode);
        if ($result instanceof DOMNodeList) {
            $ret = array();
            foreach ($result as $node) {
                $ret[] = $node;
            }
        } else {
            $ret = $result;
        }
        return $ret;
    }
}
