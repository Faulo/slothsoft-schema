<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Assets;

use Slothsoft\Core\DOMHelper;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\DOMWriter\ElementClosureDOMWriter;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\DOMWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\NullResultBuilder;
use Slothsoft\Schema\Exceptions\SchemaVersioningNotFoundException;
use DOMDocument;
use DOMElement;

class InfoBuilder implements ExecutableBuilderStrategyInterface
{

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies
    {
        if ($schemaId = $args->get('schema')) {
            if ($version = $args->get('version')) {
                $schemaId .= "/$version";
                $schemaUrl = FarahUrl::createFromReference($schemaId);
                $schemaVersions = [
                    $schemaUrl
                ];
            } else {
                $schemaUrl = FarahUrl::createFromReference($schemaId);
                $schemaVersions = [];
                foreach (Module::resolveToAsset($schemaUrl)->getAssetChildren() as $asset) {
                    $schemaVersions[] = $asset->createUrl();
                }
            }
            $closure = function (DOMDocument $targetDoc) use ($schemaVersions): DOMElement {
                $rootNode = $targetDoc->createElement('schema-info');
                foreach ($schemaVersions as $url) {
                    $schemaDoc = new \DOMDocument();
                    $schemaDoc->load("$url#xml");
                    if ($versionNode = $schemaDoc->getElementsByTagNameNS(DOMHelper::NS_SCHEMA_VERSIONING, 'info')->item(0)) {
                        $versionNode = $targetDoc->importNode($versionNode, true);
                        $versionNode->setAttribute('url', (string) $url);
                        $rootNode->appendChild($versionNode);
                    } else {
                        throw new SchemaVersioningNotFoundException("<ssv:info> not found for schema '$id'");
                    }
                }
                return $rootNode;
            };
            $writer = new ElementClosureDOMWriter($closure);
            $resultBuilder = new DOMWriterResultBuilder($writer);
        } else {
            $resultBuilder = new NullResultBuilder();
        }
        return new ExecutableStrategies($resultBuilder);
    }
}

