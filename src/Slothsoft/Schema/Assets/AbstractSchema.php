<?php
namespace Slothsoft\Schema\Assets;

use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlResolver;
use Slothsoft\Farah\Module\Node\Asset\AssetImplementation;

class AbstractSchema extends AssetImplementation
{
    protected function getVersionAssets(string $schemaId) : array {
        $ret = [];
        $schemaAsset = FarahUrlResolver::resolveToAsset(FarahUrl::createFromReference($schemaId));
        
        $versionAssets = $schemaAsset->getAssetChildren();
        if (!count($versionAssets)) {
            $versionAssets = [$schemaAsset];
        }
        foreach ($versionAssets as $versionAsset) {
            $url = $versionAsset->createUrl();
            $ret[(string) $url] = $versionAsset;
        }
        
        ksort($ret);
        $ret = array_reverse($ret, true);
        
        return $ret;
    }
}

