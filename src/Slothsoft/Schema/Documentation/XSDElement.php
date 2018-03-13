<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Documentation;

class XSDElement extends XSDNode {
	protected function initRefNodeList() {
		$this->refNodeList[] = $this->rootNode;
		if ($this->rootNode->hasAttribute('ref')) {
			$name = $this->rootNode->getAttribute('ref');
			$nodeList = $this->xpath->evaluate(sprintf('/xsd:schema/xsd:element[@name = "%s"]', $name));
			foreach ($nodeList as $node) {
				$this->refNodeList[] = $node;
			}
		}
	}
	protected function initChildren() {
		foreach (array_reverse($this->refNodeList) as $refNode) {
			if ($refNode->hasAttribute('minOccurs')) {
				$this->optionList['minOccurs'] = $refNode->getAttribute('minOccurs');
			}
			if ($refNode->hasAttribute('maxOccurs')) {
				$this->optionList['maxOccurs'] = $refNode->getAttribute('maxOccurs');
			}			
		}
	}
}