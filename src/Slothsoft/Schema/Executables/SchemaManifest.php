<?php
namespace Slothsoft\Schema\Executables;

use Slothsoft\Core\IO\Writable\DOMWriterDocumentFromElementTrait;
use Slothsoft\Farah\Module\Executables\ExecutableDOMWriterBase;
use Slothsoft\Schema\Documentation\XSDFile;
use DOMDocument;
use DOMElement;

class SchemaManifest extends ExecutableDOMWriterBase
{
    use DOMWriterDocumentFromElementTrait;
    
    private $versionAssets;
    public function __construct(array $versionAssets) {
        $this->versionAssets = $versionAssets;
    }
    
    public function toElement(DOMDocument $targetDoc) : DOMElement
    {
        $rootNode = $targetDoc->createElement('schema-manifest');
        foreach ($this->versionAssets as $versionAsset) {
            $versionFile = $versionAsset->getRealPath();
            $xsd = new XSDFile($versionFile);
            $rootNode->appendChild($xsd->asManifest($targetDoc));
        }
        return $rootNode;
    }
}

