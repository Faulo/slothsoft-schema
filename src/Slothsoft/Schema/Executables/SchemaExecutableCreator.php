<?php
namespace Slothsoft\Schema\Executables;

use Slothsoft\Farah\Module\Executables\ExecutableCreator;
use Slothsoft\Farah\Module\Executables\ExecutableInterface;

class SchemaExecutableCreator extends ExecutableCreator
{
    public function createSchemaInfo(array $versionAssets) : ExecutableInterface {
        return $this->initExecutable(new SchemaInfo($versionAssets));
    }
    public function createSchemaManifest(array $versionAssets) : ExecutableInterface {
        return $this->initExecutable(new SchemaManifest($versionAssets));
    }
}

