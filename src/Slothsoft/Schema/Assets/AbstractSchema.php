<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Assets;

use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlResolver;
use Slothsoft\Farah\Module\Node\Asset\AssetBase;

class AbstractSchema extends AssetBase
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

