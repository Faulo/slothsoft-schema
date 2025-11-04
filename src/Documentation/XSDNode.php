<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Documentation;

use DOMDocument;
use DOMElement;
use Exception;
use Slothsoft\Core\DOMHelper;

abstract class XSDNode {
    
    protected $ownerFile;
    
    protected $xpath;
    
    protected $rootNode;
    
    protected $idNode;
    
    protected $refNodeList;
    
    protected $documentationNodeList;
    
    protected $className;
    
    protected $name;
    
    protected $isDefaultName;
    
    protected $id;
    
    protected $optionList;
    
    protected $childTypeList;
    
    protected $childCategoryList;
    
    protected $childAnnotationList;
    
    protected $exampleNode;
    
    protected $exampleStatus;
    
    protected $manifestNode;
    
    protected $manifestStatus;
    
    const STATUS_EMPTY = 1;
    
    const STATUS_PROCESSING = 2;
    
    const STATUS_SUCCESS = 3;
    
    const STATUS_ERROR = 4;
    
    public function __construct(XSDFile $file) {
        $this->ownerFile = $file;
        $this->xpath = $this->ownerFile->getXPath();
    }
    
    public function init(DOMElement $node) {
        $this->rootNode = $node;
        $this->exampleStatus = self::STATUS_EMPTY;
        $this->manifestStatus = self::STATUS_EMPTY;
        
        $this->refNodeList = array();
        $this->documentationNodeList = array();
        
        $this->childTypeList = array();
        $this->childCategoryList = array();
        $this->childAnnotationList = array();
        
        $this->optionList = array();
        $this->optionList['minOccurs'] = '1';
        $this->optionList['maxOccurs'] = '1';
        $this->optionList['isRequired'] = null;
        $this->optionList['foreignTypeList'] = array();
        $this->optionList['childNamespaceList'] = array();
        $this->optionList['tokenList'] = array();
        $this->optionList['patternList'] = array();
        $this->optionList['cardinality'] = null;
        
        $this->className = basename(str_replace('\\', DIRECTORY_SEPARATOR, get_class($this)));
        $this->name = 'unknown' . $this->className;
        $this->isDefaultName = true;
        $this->setIdNode($this->rootNode);
        
        $this->initRefNodeList();
        
        foreach ($this->refNodeList as $refNode) {
            if ($refNode->hasAttribute('ref')) {
                $ref = $refNode->getAttribute('ref');
                $foreignType = explode(':', $ref, 2);
                if (count($foreignType) === 2) {
                    $this->isDefaultName = false;
                    $this->name = $ref;
                    $this->_addForeignType($foreignType[0], $foreignType[1]);
                }
            }
        }
        
        foreach ($this->refNodeList as $refNode) {
            if ($refNode->hasAttribute('name')) {
                $this->isDefaultName = false;
                $this->name = $refNode->getAttribute('name');
                $this->setIdNode($refNode);
            }
        }
        
        foreach ($this->refNodeList as $refNode) {
            $this->_addTypeParentNode($refNode);
            $this->_addCategoryParentNode($refNode);
            $this->_addGroupParentNode($refNode);
            $this->_addAnnotationParentNode($refNode);
            $this->_addTokenParentNode($refNode);
        }
        
        $this->initChildren();
        
        $this->optionList['cardinality'] = $this->_getCardinality();
    }
    
    protected function initRefNodeList() {
        $this->refNodeList[] = $this->rootNode;
    }
    
    protected function initChildren() {}
    
    public function getChildList() {
        return array_merge($this->childTypeList, $this->childCategoryList, $this->childAnnotationList);
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function hasName() {
        return ! $this->isDefaultName;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setIdNode(DOMElement $node) {
        $this->idNode = $node;
        $this->id = $this->ownerFile->getIdByNode($this->idNode, $this->getClassName());
    }
    
    public function getClassName() {
        return $this->className;
    }
    
    public function getHtmlId() {
        return sprintf('%s-%s', preg_replace('/[^\w\-\.]+/', '', $this->name), md5($this->id));
    }
    
    public function getSortIndex() {
        $sortBase = preg_replace('/\d+/', '', $this->id);
        $match = null;
        $sortIndex = preg_match('/(\d+)/', $this->id, $match) ? (int) $match[1] : 0;
        return sprintf('%s-%05d', $sortBase, $sortIndex);
    }
    
    public function isRoot() {
        return $this->rootNode->parentNode === $this->xpath->document->documentElement;
    }
    
    public function getBaseType() {
        foreach ($this->optionList['foreignTypeList'] as $schemaType) {
            return $schemaType['name'];
        }
        foreach ($this->childTypeList as $child) {
            $schemaType = $child->getBaseType();
            if ($schemaType !== null) {
                return $schemaType;
            }
        }
        return null;
    }
    
    public function getSchemaTypeList() {
        $ret = array();
        foreach ($this->optionList['foreignTypeList'] as $type) {
            if ($type['namespace'] === $this->ownerFile->getSchemaNS()) {
                $ret[] = $type['name'];
            }
        }
        return $ret;
    }
    
    public function getMin() {
        return $this->optionList['minOccurs'];
    }
    
    public function getMax() {
        return $this->optionList['maxOccurs'];
    }
    
    public function getComplexType() {
        $ret = null;
        foreach ($this->childTypeList as $childType) {
            if ($childType->hasName()) {
                $ret = $childType->getName();
                break;
            }
        }
        return $ret;
    }
    
    public function getCardinality() {
        return $this->optionList['cardinality'];
    }
    
    public function getExcelNode(DOMDocument $doc, array $xsdStack = array()) {
        $xsdStack[] = $this;
        
        $retNode = $doc->createElement($this->getClassName());
        
        if ($this->hasName()) {
            $retNode->setAttribute('name', $this->getName());
        }
        
        $retNode->setAttribute('id', $this->id);
        // $retNode->setAttribute('isRoot', (int) $this->isRoot());
        
        $retNode->setAttribute('min', $this->getMin());
        $retNode->setAttribute('max', $this->getMax());
        $retNode->setAttribute('type', $this->getComplexType());
        $retNode->setAttribute('baseType', $this->getBaseType());
        $retNode->setAttribute('cardinality', $this->_getCardinalityPath($xsdStack));
        $retNode->setAttribute('path', $this->_getElementPath($xsdStack));
        
        $retNode->setAttribute('example', $this->getExampleContent());
        
        foreach ($this->getChildList() as $child) {
            $retNode->appendChild($child->getExcelNode($doc, $xsdStack));
        }
        
        return $retNode;
    }
    
    public function getManifestNode(DOMDocument $doc, array &$storage) {
        switch ($this->manifestStatus) {
            case self::STATUS_EMPTY:
                $this->manifestStatus = self::STATUS_PROCESSING;
                $storage[$this->id] = null;
                
                try {
                    $retNode = null;
                    if ($this->optionList['maxOccurs'] === '0') {
                        throw new Exception('Skipping manifest for prohibited node');
                    }
                    $childList = $this->getChildList();
                    switch ($this->getClassName()) {
                        case 'XSDElement':
                            $retNode = $doc->createElement('element');
                            break;
                        case 'XSDAttribute':
                            $retNode = $doc->createElement('attribute');
                            break;
                        case 'XSDType':
                            $retNode = $doc->createElement('type');
                            break;
                        case 'XSDCategory':
                            $retNode = $doc->createElement('category');
                            break;
                        case 'XSDGroup':
                            $retNode = $doc->createElement('group');
                            break;
                        case 'XSDAnnotation':
                            $retNode = $doc->createElement('annotation');
                            break;
                    }
                    
                    if ($retNode) {
                        // $this->manifestNode = $retNode;
                        
                        if ($this->hasName()) {
                            $retNode->setAttribute('name', $this->getName());
                        }
                        
                        $retNode->setAttribute('id', $this->getId());
                        $retNode->setAttribute('href', $this->getHtmlId());
                        $retNode->setAttribute('sort', $this->getSortIndex());
                        if ($this->isRoot()) {
                            $retNode->setAttribute('isRoot', '');
                        }
                        if ($this->optionList['isRequired'] !== null) {
                            $retNode->setAttribute('isRequired', $this->optionList['isRequired']);
                        }
                        
                        try {
                            $content = $this->getExampleContent();
                        } catch (\Exception $e) {
                            echo "Unknown content type!" . PHP_EOL . $e . PHP_EOL;
                            $content = null;
                        }
                        if ($content !== null) {
                            $retNode->setAttribute('example', $content);
                        }
                        
                        foreach ($childList as $child) {
                            $childNode = null;
                            switch ($child->getClassName()) {
                                case 'XSDElement':
                                    // echo $child->getName() . PHP_EOL;
                                    $childNode = $doc->createElement('elementReference');
                                    $childNode->setAttribute('min', $child->getMin());
                                    $childNode->setAttribute('max', $child->getMax());
                                    $childNode->setAttribute('cardinality', $child->getCardinality());
                                    break;
                                case 'XSDAttribute':
                                    $childNode = $doc->createElement('attributeReference');
                                    break;
                                case 'XSDType':
                                    $childNode = $doc->createElement('typeReference');
                                    break;
                                case 'XSDCategory':
                                    $childNode = $doc->createElement('categoryReference');
                                    break;
                                case 'XSDGroup':
                                    $childNode = $doc->createElement('groupReference');
                                    break;
                                case 'XSDAnnotation':
                                    $childNode = $doc->createElement('annotationReference');
                                    break;
                            }
                            if ($childNode) {
                                $childNode->setAttribute('id', $child->getId());
                                $retNode->appendChild($childNode);
                            }
                            $child->getManifestNode($doc, $storage);
                        }
                        foreach ($this->optionList['foreignTypeList'] as $type) {
                            $node = $doc->createElement('foreignType');
                            foreach ($type as $key => $val) {
                                $node->setAttribute($key, $val);
                            }
                            $retNode->appendChild($node);
                        }
                        foreach ($this->optionList['childNamespaceList'] as $name) {
                            $node = $doc->createElement('childNamespace');
                            $node->setAttribute('name', $name);
                            $retNode->appendChild($node);
                        }
                        foreach ($this->optionList['tokenList'] as $name) {
                            $node = $doc->createElement('token');
                            $node->setAttribute('name', $name);
                            $retNode->appendChild($node);
                        }
                        foreach ($this->optionList['patternList'] as $name) {
                            $node = $doc->createElement('pattern');
                            $node->setAttribute('name', $name);
                            $retNode->appendChild($node);
                        }
                        if ($this->documentationNodeList) {
                            foreach ($this->documentationNodeList as $annotationNode) {
                                $node = $doc->createElement('documentation');
                                $node->appendChild($doc->importNode($annotationNode, true));
                                $retNode->appendChild($node);
                            }
                        }
                    }
                } catch (Exception $e) {
                    $retNode = null;
                }
                if ($retNode) {
                    $storage[$this->id] = $retNode;
                    $this->manifestNode = $retNode;
                    $this->manifestStatus = self::STATUS_SUCCESS;
                } else {
                    unset($storage[$this->id]);
                    $this->manifestNode = $retNode;
                    $this->manifestStatus = self::STATUS_ERROR;
                }
                break;
            case self::STATUS_PROCESSING:
                break;
            case self::STATUS_SUCCESS:
                // $this->manifestNode = $this->manifestNode->cloneNode(true);
                break;
            case self::STATUS_ERROR:
                break;
        }
        
        return $this->manifestNode;
    }
    
    public function getExampleNode(DOMDocument $doc) {
        // echo 'start: '.$this->name . PHP_EOL;flush();
        switch ($this->exampleStatus) {
            case self::STATUS_EMPTY:
                $this->exampleStatus = self::STATUS_PROCESSING;
                $invalidCreation = false;
                try {
                    $retNode = null;
                    if ($this->optionList['maxOccurs'] === '0') {
                        throw new Exception('Skipping manifest for prohibited node');
                    }
                    $childList = $this->getChildList();
                    switch ($this->getClassName()) {
                        case 'XSDElement':
                            $retNode = $this->ownerFile->createTargetElement($doc, $this->name);
                            break;
                        case 'XSDAttribute':
                            $retNode = $this->ownerFile->createTargetAttribute($doc, $this->name);
                            break;
                        case 'XSDType':
                            if ($childList) {
                                $retNode = $doc->createElement('XSDType');
                            }
                            break;
                        case 'XSDCategory':
                        case 'XSDGroup':
                        case 'XSDAnnotation':
                            break;
                    }
                    if ($retNode) {
                        $this->exampleNode = $retNode;
                        foreach ($childList as $child) {
                            if ($child->getExampleStatus() === self::STATUS_PROCESSING and $child->getMin() !== '0') {
                                $invalidCreation = true;
                                continue;
                            }
                            $childNode = $child->getExampleNode($doc);
                            if ($childNode) {
                                switch ($child->getClassName()) {
                                    case 'XSDElement':
                                    case 'XSDAttribute':
                                        $retNode->appendChild($childNode);
                                        break;
                                    case 'XSDType':
                                        foreach ($childNode->attributes as $node) {
                                            // $retNode->appendChild($node->cloneNode(true));
                                            $retNode->appendChild($this->ownerFile->createTargetAttribute($doc, $node->name, $node->value));
                                        }
                                        foreach ($childNode->childNodes as $node) {
                                            switch ($node->nodeType) {
                                                case XML_ELEMENT_NODE:
                                                case XML_COMMENT_NODE:
                                                    $retNode->appendChild($node->cloneNode(true));
                                                    break;
                                            }
                                        }
                                        break;
                                    case 'XSDCategory':
                                    case 'XSDGroup':
                                    case 'XSDAnnotation':
                                        break;
                                }
                            }
                        }
                        $content = $this->getExampleContent();
                        if ($content !== null) {
                            $retNode->appendChild($doc->createTextNode($content));
                        }
                    }
                } catch (Exception $e) {
                    $retNode = null;
                }
                if ($retNode) {
                    // only process first choice
                    foreach ($this->xpath->evaluate('parent::xsd:choice/xsd:element[1]', $this->idNode) as $node) {
                        if ($node !== $this->idNode) {
                            $retNode = $doc->createComment(preg_replace('/\<!--.+?--\>/', '', $doc->saveXML($retNode)));
                        }
                    }
                    $this->exampleNode = $retNode;
                    $this->exampleStatus = self::STATUS_SUCCESS;
                } else {
                    $this->exampleStatus = self::STATUS_ERROR;
                }
                if ($invalidCreation and $this->getMin() === '0') {
                    return null;
                }
                break;
            case self::STATUS_PROCESSING:
                break;
            case self::STATUS_SUCCESS:
                $this->exampleNode = $this->exampleNode->cloneNode(true);
                break;
            case self::STATUS_ERROR:
                break;
        }
        
        return $this->exampleNode;
    }
    
    public function getExampleStatus() {
        return $this->exampleStatus;
    }
    
    public function getExampleContent() {
        foreach ($this->optionList['tokenList'] as $token) {
            return $token;
        }
        foreach ($this->optionList['patternList'] as $pattern) {
            switch ($pattern) {
                case '[0-9]*':
                case '[0-9]+':
                case '([0-9])*':
                case '([0-9])+':
                    return '0';
                case '[A-Z]+[0-9]*':
                    return 'A0';
                case '[A-Z][A-Z0-9]*(-[0-9]+)+':
                    return 'A-0';
            }
        }
        foreach ($this->childTypeList as $child) {
            $content = $child->getExampleContent();
            if ($content !== null) {
                return $content;
            }
        }
        foreach ($this->getSchemaTypeList() as $schemaType) {
            switch ($schemaType) {
                case 'token':
                
                case 'ENTITIES':
                case 'ENTITY':
                case 'ID':
                case 'IDREF':
                case 'IDREFS':
                case 'language':
                case 'Name':
                case 'NCName':
                case 'NMTOKEN':
                case 'NMTOKENS':
                case 'normalizedString':
                case 'QName':
                case 'string':
                    return $schemaType;
                case 'boolean':
                    return 'true';
                case 'decimal':
                case 'float':
                case 'double':
                    return '0.0';
                case 'byte':
                case 'int':
                case 'integer':
                case 'long':
                case 'nonNegativeInteger':
                case 'nonPositiveInteger':
                case 'short':
                case 'unsignedLong':
                case 'unsignedInt':
                case 'unsignedShort':
                case 'unsignedByte':
                    return '0';
                case 'positiveInteger':
                    return '1';
                case 'negativeInteger':
                    return '-1';
                case 'time':
                    return date('h:i:s');
                case 'date':
                    return date('Y-m-d');
                case 'dateTime':
                    return date('c');
                case 'duration':
                    return 'P1Y';
                case 'gDay':
                    return date('d');
                case 'gMonth':
                    return date('m');
                case 'gMonthDay':
                    return date('m-d');
                case 'gYear':
                    return date('Y');
                case 'gYearMonth':
                    return date('Y-m');
                case 'base64Binary':
                    return base64_encode($schemaType);
                case 'anyURI':
                    return $this->ownerFile->getTargetNS();
                case 'anyType':
                    return 'anyType';
                default:
                    throw new Exception(json_encode($schemaType));
            }
        }
        return null;
    }
    
    protected function _getElementPath(array $xsdList) {
        $ret = array();
        foreach ($xsdList as $xsd) {
            if ($xsd instanceof XSDElement or $xsd instanceof XSDAttribute) {
                $ret[] = '/' . $xsd->getName();
            }
        }
        return implode('', $ret);
    }
    
    protected function _getCardinality() {
        $ret = null;
        switch ($this->getClassName()) {
            case 'XSDElement':
            case 'XSDAttribute':
                $ret = $this->getMin() . '-' . $this->getMax();
                switch ($ret) {
                    case '1-unbounded':
                        $ret = '+';
                        break;
                    case '0-unbounded':
                        $ret = '*';
                        break;
                    case '0-1':
                        $ret = '?';
                        break;
                    case '1-1':
                        $ret = '1';
                        break;
                }
                break;
        }
        return $ret;
    }
    
    protected function _getCardinalityPath(array $xsdList) {
        $ret = array();
        foreach ($xsdList as $xsd) {
            if ($val = $xsd->getCardinality()) {
                $ret[] = '/' . $val;
            }
        }
        return implode('', $ret);
    }
    
    protected function _addTypeParentNode(DOMElement $parentNode) {
        $nodeList = $this->xpath->evaluate('xsd:complexType | xsd:simpleType | xsd:union/xsd:complexType | xsd:union/xsd:simpleType', $parentNode);
        foreach ($nodeList as $node) {
            $this->_addTypeNode($node);
        }
        if ($memberTypes = $this->xpath->evaluate('normalize-space(xsd:union/@memberTypes)', $parentNode)) {
            foreach (explode(' ', $memberTypes) as $type) {
                $this->_addTypeName($type);
            }
        }
        if ($parentNode->hasAttribute('type')) {
            $this->_addTypeName($parentNode->getAttribute('type'));
        }
    }
    
    protected function _addTypeNode(DOMElement $node) {
        if (in_array($node, $this->refNodeList, true)) {
            // override!
        } else {
            if ($child = $this->ownerFile->createXSDType($node)) {
                $this->childTypeList[] = $child;
            }
        }
    }
    
    protected function _addTypeName($type) {
        // $this->ownerFile->getSchemaQuery()
        $match = null;
        if (preg_match('/([^:]+):([^:]+)/', $type, $match)) {
            $this->_addForeignType($match[1], $match[2]);
        } else {
            $nodeList = $this->xpath->evaluate(sprintf('/xsd:schema/xsd:complexType[@name = "%s"] | /xsd:schema/xsd:simpleType[@name = "%s"]', $type, $type));
            foreach ($nodeList as $node) {
                $this->_addTypeNode($node);
            }
        }
    }
    
    protected function _addForeignType($prefix, $localName) {
        $this->optionList['foreignTypeList'][] = array(
            'prefix' => $prefix,
            'name' => $localName,
            'namespace' => $this->xpath->document->lookupNamespaceURI($prefix)
        );
    }
    
    protected function _addCategoryParentNode(DOMElement $parentNode) {
        $nodeList = $this->xpath->evaluate('xsd:annotation/xsd:appinfo/xsd:category', $parentNode);
        foreach ($nodeList as $node) {
            if ($node->hasAttribute('ref')) {
                $name = $node->getAttribute('ref');
                $categoryNodeList = $this->xpath->evaluate(sprintf('/xsd:schema/xsd:annotation/xsd:appinfo/xsd:category[@name = "%s"]', $name));
                foreach ($categoryNodeList as $categoryNode) {
                    $this->_addCategoryNode($categoryNode);
                }
            } else {
                $this->_addCategoryNode($node);
            }
        }
    }
    
    protected function _addCategoryNode(DOMElement $node) {
        if ($child = $this->ownerFile->createXSDCategory($node)) {
            $this->childCategoryList[] = $child;
        }
    }
    
    protected function _addGroupParentNode(DOMElement $parentNode) {
        $nodeList = $this->xpath->evaluate('xsd:group[@ref] | */xsd:group[@ref] | */*/xsd:group[@ref]', $parentNode);
        foreach ($nodeList as $node) {
            $this->_addGroupName($node->getAttribute('ref'));
        }
    }
    
    protected function _addGroupNode(DOMElement $node) {
        if ($child = $this->ownerFile->createXSDGroup($node)) {
            $this->childCategoryList[] = $child;
        }
    }
    
    protected function _addGroupName($group) {
        $nodeList = $this->xpath->evaluate(sprintf('/xsd:schema/xsd:group[@name = "%s"]', $group));
        foreach ($nodeList as $node) {
            $this->_addGroupNode($node);
        }
    }
    
    protected function _addAnnotationParentNode(DOMElement $parentNode) {
        $nodeList = $this->xpath->evaluate('xsd:annotation | xsd:complexContent/xsd:annotation | xsd:simpleContent/xsd:annotation', $parentNode);
        foreach ($nodeList as $node) {
            $this->_addAnnotationNode($node);
        }
    }
    
    protected function _addAnnotationNode(DOMElement $parentNode) {
        if ($child = $this->ownerFile->createXSDAnnotation($parentNode)) {
            $this->childAnnotationList[] = $child;
        }
        /*
         * if ($parentNode->hasChildNodes()) {
         * $retNode = $parentNode->ownerDocument->createDocumentFragment();
         * $nodeList = array();
         * foreach ($parentNode->childNodes as $node) {
         * $nodeList[] = $node;
         * }
         * foreach ($nodeList as $node) {
         * $retNode->appendChild($node);
         * }
         * $this->annotationNodeList[] = $retNode;
         * }
         * //
         */
    }
    
    protected function _addDocumentationNode(DOMElement $parentNode) {
        if ($parentNode->hasChildNodes()) {
            $retNode = $parentNode->ownerDocument->createDocumentFragment();
            $nodeList = [];
            foreach ($parentNode->childNodes as $node) {
                switch ($node->nodeType) {
                    case XML_TEXT_NODE:
                        if (trim($node->textContent) !== '') {
                            $nodeList[] = $node;
                        }
                        break;
                    case XML_COMMENT_NODE:
                        $tmpNode = $node->ownerDocument->createElementNS(DOMHelper::NS_HTML, 'pre');
                        $tmpNode->appendChild($node);
                        $nodeList[] = $tmpNode;
                        break;
                    case XML_CDATA_SECTION_NODE:
                    default:
                        $nodeList[] = $node;
                        break;
                }
            }
            foreach ($nodeList as $node) {
                $retNode->appendChild($node);
            }
            $this->documentationNodeList[] = $retNode;
        }
    }
    
    protected function _addTokenParentNode(DOMElement $parentNode) {
        if ($parentNode->hasAttribute('fixed')) {
            array_unshift($this->optionList['tokenList'], $parentNode->getAttribute('fixed'));
        }
        $nodeList = $this->xpath->evaluate('*/xsd:enumeration', $parentNode);
        foreach ($nodeList as $node) {
            $this->optionList['tokenList'][] = $node->getAttribute('value');
        }
        $nodeList = $this->xpath->evaluate('xsd:pattern', $parentNode);
        foreach ($nodeList as $node) {
            $this->optionList['patternList'][] = $node->getAttribute('value');
        }
    }
}