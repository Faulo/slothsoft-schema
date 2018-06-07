<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Documentation;

class XSDGroup extends XSDNode
{
    protected $childElementList = array();

    protected function initRefNodeList()
    {
        $this->refNodeList[] = $this->rootNode;
        if ($this->rootNode->hasAttribute('ref')) {
            $name = $this->rootNode->getAttribute('ref');
            $nodeList = $this->xpath->evaluate(sprintf('/xsd:schema/xsd:group[@name = "%s"]', $name));
            foreach ($nodeList as $node) {
                $this->refNodeList[] = $node;
            }
        }
    }

    protected function initChildren()
    {
        $this->childElementList = array();
        
        foreach ($this->refNodeList as $typeNode) {
            $nodeList = $this->xpath->evaluate('*/xsd:element | */*/xsd:element', $typeNode);
            foreach ($nodeList as $node) {
                if ($child = $this->ownerFile->createXSDElement($node)) {
                    $this->childElementList[] = $child;
                }
            }
        }
    }
    
    public function getChildList()
    {
        return array_merge($this->childElementList, parent::getChildList());
    }
}