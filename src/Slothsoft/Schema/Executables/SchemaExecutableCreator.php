<?php
namespace Slothsoft\Schema\Executables;

use Slothsoft\Farah\Module\Executables\ExecutableCreator;
use Slothsoft\Farah\Module\Executables\ExecutableInterface;

class SchemaExecutableCreator extends ExecutableCreator
{
    public function createSchemaInfo(array $versionAssets) : ExecutableInterface {
        $executable = new SchemaInfo($versionAssets);
        $executable->init($this->ownerAsset, $this->args);
        return $executable;
    }
    public function createSchemaManifest(array $versionAssets) : ExecutableInterface {
        $executable = new SchemaManifest($versionAssets);
        $executable->init($this->ownerAsset, $this->args);
        return $executable;
    }
}

