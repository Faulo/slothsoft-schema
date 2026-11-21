<?php
declare(strict_types = 1);

namespace Slothsoft\Schema\Documentation;

class XSDType extends XSDNode {
    
    /** @var list<XSDAttribute> */
    protected array $childAttributeList = array();
    
    /** @var list<XSDElement> */
    protected array $childElementList = array();
    
    protected function initChildren(): void {
        $this->childAttributeList = array();
        $this->childElementList = array();
        
        /*
         * foreach ($this->refNodeList as $parentNode) {
         * if ($parentNode->hasAttribute('type')) {
         * $type = $parentNode->getAttribute('type');
         * if (preg_match($this->schemaQuery, $type, $match)) {
         * $this->optionList['baseTypeList'][] = $match[1];
         * }
         * }
         * }
         * //
         */
        
        foreach ($this->refNodeList as $typeNode) {
            $baseNodeList = $this->xpath->evaluate('xsd:restriction | xsd:extension | */xsd:restriction | */xsd:extension', $typeNode);
            foreach ($baseNodeList as $baseNode) {
                if ($baseNode->hasAttribute('base')) {
                    $this->_addTypeName($baseNode->getAttribute('base'));
                }
                $this->_addTypeNode($baseNode);
            }
        }
        
        $attributeGroupNodeList = array();
        foreach ($this->refNodeList as $typeNode) {
            $parentNodeList = $this->xpath->evaluate('xsd:attributeGroup', $typeNode);
            foreach ($parentNodeList as $parentNode) {
                if ($parentNode->hasAttribute('ref')) {
                    $ref = $parentNode->getAttribute('ref');
                    $nodeList = $this->xpath->evaluate(sprintf('/xsd:schema/xsd:attributeGroup[@name = "%s"]', $ref));
                    foreach ($nodeList as $node) {
                        $attributeGroupNodeList[] = $node;
                    }
                }
                $attributeGroupNodeList[] = $parentNode;
            }
            $attributeGroupNodeList[] = $typeNode;
        }
        
        foreach ($attributeGroupNodeList as $groupNode) {
            $nodeList = $this->xpath->evaluate('xsd:attribute | */xsd:attribute', $groupNode);
            foreach ($nodeList as $node) {
                $this->childAttributeList[] = $this->ownerFile->createXSDAttribute($node);
            }
        }
        
        foreach ($this->refNodeList as $typeNode) {
            $nodeList = $this->xpath->evaluate('*/xsd:element | */*/xsd:element', $typeNode);
            foreach ($nodeList as $node) {
                $this->childElementList[] = $this->ownerFile->createXSDElement($node);
            }
        }
        
        foreach ($this->refNodeList as $typeNode) {
            $nodeList = $this->xpath->evaluate('*/xsd:any | */*/xsd:any', $typeNode);
            foreach ($nodeList as $node) {
                if ($ns = $node->getAttribute('namespace')) {
                    $this->optionList['childNamespaceList'][$ns] = $ns;
                }
            }
        }
    }
    
    /** @return list<XSDNode> */
    public function getChildList(): array {
        return array_merge($this->childAttributeList, $this->childElementList, parent::getChildList());
    }
}