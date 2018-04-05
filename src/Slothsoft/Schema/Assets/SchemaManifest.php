<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Assets;

use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Results\ResultCatalog;
use Slothsoft\Farah\Module\Results\ResultInterface;
use Slothsoft\Schema\Documentation\XSDFile;

class SchemaManifest extends AbstractSchema
{
    protected function loadResult(FarahUrl $url) : ResultInterface {
        $args = $url->getArguments();
        if (!$args->has('schema')) {
            return ResultCatalog::createNullResult($url);
        }
        $versionAssets = $this->getVersionAssets($args->get('schema'));
        
        $dataDoc = new \DOMDocument();
        $rootNode = $dataDoc->createElement('schema-manifest');
        foreach ($versionAssets as $versionAsset) {
            $versionFile = $versionAsset->getRealPath();
            $xsd = new XSDFile($versionFile);
            $rootNode->appendChild($xsd->asManifest($dataDoc));
        }
        $dataDoc->appendChild($rootNode);
        
        return ResultCatalog::createDOMDocumentResult($url, $dataDoc);
    }
}

