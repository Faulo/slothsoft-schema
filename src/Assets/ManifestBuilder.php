<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Assets;

use Slothsoft\Core\IO\Writable\Delegates\DOMWriterFromElementDelegate;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\DOMWriterResultBuilder;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\NullResultBuilder;
use Slothsoft\Schema\Documentation\XSDFile;
use DOMDocument;
use DOMElement;

class ManifestBuilder implements ExecutableBuilderStrategyInterface {

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies {
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
                $rootNode = $targetDoc->createElement('schema-manifest');
                foreach ($schemaVersions as $url) {
                    $versionFile = "$url#xml";
                    $xsd = new XSDFile($versionFile);
                    $rootNode->appendChild($xsd->asManifest($targetDoc));
                }
                return $rootNode;
            };
            $writer = new DOMWriterFromElementDelegate($closure);
            $resultBuilder = new DOMWriterResultBuilder($writer, 'manifest.xml');
        } else {
            $resultBuilder = new NullResultBuilder();
        }
        return new ExecutableStrategies($resultBuilder);
    }
}

