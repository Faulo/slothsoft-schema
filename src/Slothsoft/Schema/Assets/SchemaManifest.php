<?php
namespace Slothsoft\Schema\Assets;

use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlResolver;
use Slothsoft\Farah\Module\Node\Asset\AssetImplementation;
use Slothsoft\Farah\Module\Results\ResultCatalog;
use Slothsoft\Farah\Module\Results\ResultInterface;
use Slothsoft\Schema\Documentation\XSDFile;

class SchemaManifest extends AssetImplementation
{
    protected function loadResult(FarahUrl $url) : ResultInterface {
        $args = $url->getArguments();
        $schemaId = $args->get('schema');
        
        $schemaAsset = FarahUrlResolver::resolveToAsset(FarahUrl::createFromReference($schemaId));
        
        $dataDoc = new \DOMDocument();
        $rootNode = $dataDoc->createElement('schema-manifest');
        $versionAssets = $schemaAsset->getAssetChildren();
        if (!count($versionAssets)) {
            $versionAssets = [$schemaAsset];
        }
        foreach ($schemaAsset->getAssetChildren() as $versionAsset) {
            $versionFile = $versionAsset->getRealPath();
            $xsd = new XSDFile($versionFile);
            $rootNode->appendChild($xsd->asManifest($dataDoc));
        }
        $dataDoc->appendChild($rootNode);
        
        return ResultCatalog::createDOMDocumentResult($url, $dataDoc);
    }
}

