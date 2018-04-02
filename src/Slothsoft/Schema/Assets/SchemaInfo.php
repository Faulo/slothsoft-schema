<?php
namespace Slothsoft\Schema\Assets;

use Slothsoft\Core\DOMHelper;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlResolver;
use Slothsoft\Farah\Module\Node\Asset\AssetImplementation;
use Slothsoft\Farah\Module\Results\ResultCatalog;
use Slothsoft\Farah\Module\Results\ResultInterface;

class SchemaInfo extends AssetImplementation
{
    protected function loadResult(FarahUrl $url) : ResultInterface {
        $args = $url->getArguments();
        $schemaId = $args->get('schema');
        
        $schemaAsset = FarahUrlResolver::resolveToAsset(FarahUrl::createFromReference($schemaId));
        
        $versionAssets = $schemaAsset->getAssetChildren();
        if (!count($versionAssets)) {
            $versionAssets = [$schemaAsset];
        }
        $versionNodes = [];
        foreach ($versionAssets as $versionAsset) {
            $url = $versionAsset->createUrl();
            $schemaDoc = $versionAsset->createResult()->toDocument();
            if ($versionNode = $schemaDoc->getElementsByTagNameNS(DOMHelper::NS_SCHEMA_VERSIONING, 'manifest')->item(0)) {
                $versionNodes[(string) $url] = $versionNode;
            }
        }
        
        ksort($versionNodes);
        $versionNodes = array_reverse($versionNodes, true);
        
        $dataDoc = new \DOMDocument();
        $rootNode = $dataDoc->createElement('schema-info');
        foreach ($versionNodes as $id => $versionNode) {
            $versionNode = $dataDoc->importNode($versionNode, true);
            $versionNode->setAttribute('xml:id', $id);
            $rootNode->appendChild($versionNode);
        }
        $dataDoc->appendChild($rootNode);
        
        return ResultCatalog::createDOMDocumentResult($url, $dataDoc);
    }
}

