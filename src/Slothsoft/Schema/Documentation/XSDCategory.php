<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Documentation;

class XSDCategory extends XSDNode
{

    protected function initRefNodeList()
    {
        $this->refNodeList[] = $this->rootNode;
        if ($this->rootNode->hasAttribute('ref')) {
            $name = $this->rootNode->getAttribute('ref');
            $nodeList = $this->xpath->evaluate(sprintf('/xsd:schema/xsd:annotation/xsd:appinfo/xsd:category[@name = "%s"]', $name));
            foreach ($nodeList as $node) {
                $this->refNodeList[] = $node;
            }
        }
    }

    protected function initChildren()
    {
        foreach ($this->refNodeList as $refNode) {
            $this->_addAnnotationNode($refNode);
        }
    }
}