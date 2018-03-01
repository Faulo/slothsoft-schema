<?php
namespace Slothsoft\Schema\Documentation;

class XSDAttribute extends XSDNode {
	protected function initRefNodeList() {
		$this->refNodeList[] = $this->rootNode;
		if ($this->rootNode->hasAttribute('ref')) {
			$name = $this->rootNode->getAttribute('ref');
			$nodeList = $this->xpath->evaluate(sprintf('/xsd:schema/xsd:attribute[@name = "%s"]', $name));
			foreach ($nodeList as $node) {
				$this->refNodeList[] = $node;
			}
		}
	}
	protected function initChildren() {
		$this->optionList['isRequired'] = '0';
		$this->optionList['minOccurs'] = '0';
		$this->optionList['maxOccurs'] = '1';
		
		foreach ($this->refNodeList as $refNode) {
			if ($refNode->hasAttribute('use')) {
				$use = $refNode->getAttribute('use');
				switch ($use) {
					case 'required':
						$this->optionList['isRequired'] = '1';
						$this->optionList['minOccurs'] = '1';
						$this->optionList['maxOccurs'] = '1';
						break;
					case 'optional':
						$this->optionList['isRequired'] = '0';
						$this->optionList['minOccurs'] = '0';
						$this->optionList['maxOccurs'] = '1';
						break;
					case 'prohibited':
						$this->optionList['isRequired'] = '0';
						$this->optionList['minOccurs'] = '0';
						$this->optionList['maxOccurs'] = '0';
						break;
				}
			}
		}
	}
}