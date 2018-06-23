<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Documentation;

use DOMDocument;
use DOMXPath;
use DOMElement;
use DOMNode;
use DOMNodeList;

class XSDFile
{

    protected $file;

    protected $doc;

    protected $xpath;

    protected $targetNS;

    protected $targetPrefix;

    protected $targetQuery;

    protected $schemaNS;

    protected $schemaPrefix;

    protected $schemaQuery;

    protected $elementsQualified;

    protected $attributesQualified;

    protected $xsdList;

    public function __construct($file)
    {
        $this->file = $file;
        $this->doc = new DOMDocument();
        $this->doc->load($this->file);
        $this->xpath = new DOMXPath($this->doc);
        $this->xpath->registerNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');
        
        $this->targetNS = $this->xpathEvaluate('string(/xsd:schema/@targetNamespace)');
        $this->targetPrefix = basename($this->targetNS);
        $this->targetQuery = sprintf('/^%s:(\w+)$/', $this->targetPrefix);
        
        $this->schemaNS = 'http://www.w3.org/2001/XMLSchema';
        $this->schemaPrefix = $this->xpath->document->lookupPrefix($this->schemaNS);
        $this->schemaQuery = sprintf('/^%s:(\w+)$/', $this->schemaPrefix);
        
        $this->elementsQualified = $this->xpathEvaluate('boolean(/xsd:schema/@elementFormDefault = "qualified")');
        $this->attributesQualified = $this->xpathEvaluate('boolean(/xsd:schema/@attributeFormDefault = "qualified")');
        
        $includeList = array();
        $overrideNodeList = array();
        while ($nodeList = $this->xpathEvaluate('/xsd:schema/xsd:include | /xsd:schema/xsd:redefine')) {
            foreach ($nodeList as $oldNode) {
                $newNode = $this->doc->createDocumentFragment();
                if ($include = $oldNode->getAttribute('schemaLocation')) {
                    $include = dirname($this->doc->documentURI) . DIRECTORY_SEPARATOR . $include;
                    if ($include = realpath($include)) {
                        if (! isset($includeList[$include])) {
                            $includeList[$include] = true;
                        }
                        $includeDoc = new DOMDocument();
                        $includeDoc->load($include);
                        foreach ($includeDoc->documentElement->childNodes as $node) {
                            $newNode->appendChild($this->doc->importNode($node, true));
                        }
                    }
                }
                foreach ($this->xpathEvaluate('xsd:complexType', $oldNode) as $heirNode) {
                    $overrideNodeList[] = $heirNode->cloneNode(true);
                }
                $oldNode->parentNode->replaceChild($newNode, $oldNode);
            }
        }
        foreach ($overrideNodeList as $heirNode) {
            $name = $heirNode->getAttribute('name');
            $isRestriction = $this->xpathEvaluate('boolean(xsd:complexContent/xsd:restriction)', $heirNode);
            $elementNodeList = $this->xpathEvaluate('xsd:complexContent/*/xsd:all | xsd:complexContent/*/xsd:sequence | xsd:complexContent/*/xsd:attributeGroup', $heirNode);
            $attributeNodeList = $this->xpathEvaluate('xsd:complexContent/*/xsd:attribute', $heirNode);
            
            $ancestorNodeList = $this->xpath->evaluate(sprintf('/xsd:schema/xsd:complexType[@name = "%s"]', $name));
            foreach ($ancestorNodeList as $ancestorNode) {
                $parentNodeList = $this->xpathEvaluate('(. | xsd:complexContent/xsd:restriction | xsd:complexContent/xsd:extension)[last()]', $ancestorNode);
                foreach ($parentNodeList as $parentNode) {
                    if ($isRestriction) {
                        foreach ($this->xpathEvaluate('xsd:all | xsd:sequence', $parentNode) as $node) {
                            $parentNode->removeChild($node);
                        }
                    }
                    foreach ($elementNodeList as $elementNode) {
                        $parentNode->appendChild($elementNode->cloneNode(true));
                    }
                    foreach ($attributeNodeList as $attributeNode) {
                        $attributeName = $attributeNode->getAttribute('name');
                        foreach ($this->xpathEvaluate(sprintf('xsd:attribute[@name = "%s"]', $attributeName), $parentNode) as $node) {
                            $parentNode->removeChild($node);
                        }
                        $parentNode->appendChild($attributeNode->cloneNode(true));
                    }
                }
            }
        }
        $this->xsdList = array();
    }

    public function createXSDElement(DOMElement $node)
    {
        $id = $this->getIdByNode($node, 'XSDElement');
        if (! isset($this->xsdList[$id])) {
            $this->xsdList[$id] = new XSDElement($this);
            $this->xsdList[$id]->init($node);
        }
        return $this->xsdList[$id];
    }

    public function createXSDAttribute(DOMElement $node)
    {
        $id = $this->getIdByNode($node, 'XSDAttribute');
        if (! isset($this->xsdList[$id])) {
            $this->xsdList[$id] = new XSDAttribute($this);
            $this->xsdList[$id]->init($node);
        }
        return $this->xsdList[$id];
    }

    public function createXSDType(DOMElement $node)
    {
        $id = $this->getIdByNode($node, 'XSDType');
        if (! isset($this->xsdList[$id])) {
            $this->xsdList[$id] = new XSDType($this);
            $this->xsdList[$id]->init($node);
        }
        return $this->xsdList[$id];
    }

    public function createXSDCategory(DOMElement $node)
    {
        $id = $this->getIdByNode($node, 'XSDCategory');
        if (! isset($this->xsdList[$id])) {
            $this->xsdList[$id] = new XSDCategory($this);
            $this->xsdList[$id]->init($node);
        }
        return $this->xsdList[$id];
    }

    public function createXSDGroup(DOMElement $node)
    {
        $id = $this->getIdByNode($node, 'XSDGroup');
        if (! isset($this->xsdList[$id])) {
            $this->xsdList[$id] = new XSDGroup($this);
            $this->xsdList[$id]->init($node);
        }
        return $this->xsdList[$id];
    }

    public function createXSDAnnotation(DOMElement $node)
    {
        $id = $this->getIdByNode($node, 'XSDAnnotation');
        if (! isset($this->xsdList[$id])) {
            $this->xsdList[$id] = new XSDAnnotation($this);
            $this->xsdList[$id]->init($node);
        }
        return $this->xsdList[$id];
    }

    public function getIdByNode(DOMElement $node, $className)
    {
        return sprintf('%s(%s)', $className, $node->getNodePath());
    }

    public function getElementByName($name)
    {
        $nodeList = $this->xpathEvaluate(sprintf('/xsd:schema/xsd:element[@name = "%s"]', $name));
        foreach ($nodeList as $node) {
            return $this->createXSDElement($node);
        }
    }

    public function getXPath()
    {
        return $this->xpath;
    }

    public function getTargetNS()
    {
        return $this->targetNS;
    }

    public function getSchemaQuery()
    {
        return $this->schemaQuery;
    }

    public function getSchemaNS()
    {
        return $this->schemaNS;
    }

    public function asNode(DOMDocument $doc)
    {
        $retNode = $doc->createDocumentFragment();
        $nodeList = $this->xpathEvaluate('/xsd:schema/xsd:element[@name]');
        foreach ($nodeList as $node) {
            $retNode->appendChild($doc->createElementNS($this->targetNS, $node->getAttribute('name')));
        }
        return $retNode->hasChildNodes() ? $retNode : $doc->createTextNode('');
    }

    public function asManifest(DOMDocument $targetDoc = null)
    {
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
        
        $storage = array();
        
        $nodeList = $this->xpathEvaluate('/xsd:schema/xsd:annotation');
        foreach ($nodeList as $node) {
            $xsd = $this->createXSDAnnotation($node);
            $manifestNode->appendChild($xsd->getManifestNode($retDoc, $storage));
        }
        
        $nodeList = $this->xpathEvaluate('/xsd:schema/xsd:element[@name]');
        foreach ($nodeList as $node) {
            if ($xsd = $this->createXSDElement($node)) {
                // echo $xsd->getId() . PHP_EOL;flush();
                $xsd->getManifestNode($retDoc, $storage);
            }
        }
        $nodeList = $this->xpathEvaluate('/xsd:schema/xsd:complexType[@name]');
        foreach ($nodeList as $node) {
            if ($xsd = $this->createXSDType($node)) {
                // echo $xsd->getId() . PHP_EOL;flush();
                $xsd->getManifestNode($retDoc, $storage);
            }
        }
        $nodeList = $this->xpathEvaluate('/xsd:schema/xsd:simpleType[@name]');
        foreach ($nodeList as $node) {
            if ($xsd = $this->createXSDType($node)) {
                // echo $xsd->getId() . PHP_EOL;flush();
                $xsd->getManifestNode($retDoc, $storage);
            }
        }
        
        // my_dump($storage);
        foreach ($storage as $node) {
            $manifestNode->appendChild($node);
        }
        $xpath = new DOMXPath($retDoc);
        do {
            $nodeList = array();
            $tmpList = $xpath->evaluate('.//typeReference[not(type) and not(@id = ancestor::*/@id)]', $manifestNode);
            foreach ($tmpList as $node) {
                $nodeList[] = $node;
            }
            foreach ($nodeList as $node) {
                $id = $node->getAttribute('id');
                if (isset($storage[$id])) {
                    $node->appendChild($storage[$id]->cloneNode(true));
                } else {
                    $node->parentNode->removeChild($node);
                }
            }
        } while (count($nodeList));
        
        return $retNode;
    }

    public function asExcel()
    {
        $retDoc = new DOMDocument();
        $retDoc->appendChild($retDoc->createElement('excel'));
        
        $nodeList = $this->xpathEvaluate('/xsd:schema/xsd:element[@name]');
        foreach ($nodeList as $node) {
            if ($element = $this->getElementByName($node->getAttribute('name'))) {
                $retDoc->documentElement->appendChild($element->getExcelNode($retDoc));
            }
        }
        return $retDoc;
    }

    public function getExampleDocument(XSDElement $xsd)
    {
        $retDoc = new DOMDocument();
        // PHP Bug: muss Namensraum-Prefix explizit und im documentElement vergeben, sonst verschwindet er
        if ($this->attributesQualified) {
            $tempNode = $retDoc->createElementNS($this->targetNS, $this->targetPrefix . ':' . $this->targetPrefix);
            $retDoc->appendChild($tempNode);
            $retDoc->createAttributeNS($this->targetNS, $this->targetPrefix . ':' . $this->targetPrefix);
            $exampleNode = $xsd->getExampleNode($retDoc);
            $tempNode->appendChild($exampleNode);
            $retDoc->replaceChild($exampleNode, $tempNode);
        } else {
            $retDoc->appendChild($xsd->getExampleNode($retDoc));
        }
        $retDoc->formatOutput = true;
        return $retDoc;
    }

    public function createTargetElement(DOMDocument $doc, $name)
    {
        return $this->elementsQualified ? $doc->createElementNS($this->targetNS, $name) : $doc->createElement($name);
    }

    public function createTargetAttribute(DOMDocument $doc, $name, $value = null)
    {
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

    public function xpathEvaluate($query, DOMNode $contextNode = null)
    {
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