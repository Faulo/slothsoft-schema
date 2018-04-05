<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Assets;

use Slothsoft\Core\DOMHelper;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Results\ResultCatalog;
use Slothsoft\Farah\Module\Results\ResultInterface;
use Slothsoft\Schema\Exceptions\SchemaVersioningNotFoundException;

class SchemaInfo extends AbstractSchema
{
    protected function loadResult(FarahUrl $url) : ResultInterface {
        $args = $url->getArguments();
        if (!$args->has('schema')) {
            return ResultCatalog::createNullResult($url);
        }
        $versionAssets = $this->getVersionAssets($args->get('schema'));
        
        $dataDoc = new \DOMDocument();
        $rootNode = $dataDoc->createElement('schema-info');
        foreach ($versionAssets as $id => $versionAsset) {
            $schemaDoc = $versionAsset->createResult()->toDocument();
            if ($versionNode = $schemaDoc->getElementsByTagNameNS(DOMHelper::NS_SCHEMA_VERSIONING, 'info')->item(0)) {
                $versionNode = $dataDoc->importNode($versionNode, true);
                $versionNode->setAttribute('url', $id);
                $rootNode->appendChild($versionNode);
            } else {
                throw new SchemaVersioningNotFoundException("<ssv:info> not found for schema '$schemaDoc->documentURI'");
            }
        }
        $dataDoc->appendChild($rootNode);
        
        return ResultCatalog::createDOMDocumentResult($url, $dataDoc);
    }
}

