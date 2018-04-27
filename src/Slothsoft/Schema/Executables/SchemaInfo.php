<?php
namespace Slothsoft\Schema\Executables;

use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\Writable\DOMWriterDocumentFromElementTrait;
use Slothsoft\Farah\Module\Executables\ExecutableDOMWriterBase;
use Slothsoft\Schema\Exceptions\SchemaVersioningNotFoundException;
use DOMDocument;
use DOMElement;

class SchemaInfo extends ExecutableDOMWriterBase
{
    use DOMWriterDocumentFromElementTrait;
    
    private $versionAssets;
    public function __construct(array $versionAssets) {
        $this->versionAssets = $versionAssets;
    }
    
    public function toElement(DOMDocument $targetDoc) : DOMElement
    {
        $rootNode = $targetDoc->createElement('schema-info');
        foreach ($this->versionAssets as $id => $versionAsset) {
            $schemaDoc = new \DOMDocument();
            $schemaDoc->load("$id#xml");
            if ($versionNode = $schemaDoc->getElementsByTagNameNS(DOMHelper::NS_SCHEMA_VERSIONING, 'info')->item(0)) {
                $versionNode = $targetDoc->importNode($versionNode, true);
                $versionNode->setAttribute('url', $id);
                $rootNode->appendChild($versionNode);
            } else {
                throw new SchemaVersioningNotFoundException("<ssv:info> not found for schema '$id'");
            }
        }
        return $rootNode;
    }

}

