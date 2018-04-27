<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Assets;

use Slothsoft\Farah\Module\Executables\ExecutableInterface;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlArguments;
use Slothsoft\Schema\Executables\SchemaExecutableCreator;

class SchemaInfo extends AbstractSchema
{
    protected function loadExecutable(FarahUrlArguments $args) : ExecutableInterface {
        $creator = new SchemaExecutableCreator($this, $args);
        
        if ($args->has('schema')) {
            $schemaId = $args->get('schema');
            return $creator->createSchemaInfo($this->getVersionAssets($schemaId));
        } else {
            return $creator->createNullExecutable();
        }
    }
}

