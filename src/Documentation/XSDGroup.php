<?php
declare(strict_types = 1);

namespace Slothsoft\Schema\Documentation;

class XSDGroup extends XSDNode {
    
    /** @var list<XSDElement> */
    protected array $childElementList = array();
    
    protected function initRefNodeList(): void {
        $this->refNodeList[] = $this->rootNode;
        if ($this->rootNode->hasAttribute('ref')) {
            $name = $this->rootNode->getAttribute('ref');
            $nodeList = $this->xpath->evaluate(sprintf('/xsd:schema/xsd:group[@name = "%s"]', $name));
            foreach ($nodeList as $node) {
                $this->refNodeList[] = $node;
            }
        }
    }
    
    protected function initChildren(): void {
        $this->childElementList = array();
        
        foreach ($this->refNodeList as $typeNode) {
            $nodeList = $this->xpath->evaluate('*/xsd:element | */*/xsd:element', $typeNode);
            foreach ($nodeList as $node) {
                $this->childElementList[] = $this->ownerFile->createXSDElement($node);
            }
        }
    }
    
    /** @return list<XSDNode> */
    public function getChildList(): array {
        return array_merge($this->childElementList, parent::getChildList());
    }
}