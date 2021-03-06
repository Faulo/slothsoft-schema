<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Documentation;

class XSDType extends XSDNode {

    protected $childAttributeList = array();

    protected $childElementList = array();

    protected function initChildren() {
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
                if ($child = $this->ownerFile->createXSDAttribute($node)) {
                    $this->childAttributeList[] = $child;
                }
            }
        }

        foreach ($this->refNodeList as $typeNode) {
            $nodeList = $this->xpath->evaluate('*/xsd:element | */*/xsd:element', $typeNode);
            foreach ($nodeList as $node) {
                if ($child = $this->ownerFile->createXSDElement($node)) {
                    $this->childElementList[] = $child;
                }
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

    public function getChildList() {
        return array_merge($this->childAttributeList, $this->childElementList, parent::getChildList());
    }
}