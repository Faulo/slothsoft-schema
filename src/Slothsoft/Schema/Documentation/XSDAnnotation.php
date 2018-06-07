<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Documentation;

class XSDAnnotation extends XSDNode
{

    protected function initChildren()
    {
        foreach ($this->refNodeList as $refNode) {
            $nodeList = $this->xpath->evaluate('xsd:documentation | self::xsd:category', $refNode);
            foreach ($nodeList as $node) {
                $this->_addDocumentationNode($node);
            }
        }
    }
}