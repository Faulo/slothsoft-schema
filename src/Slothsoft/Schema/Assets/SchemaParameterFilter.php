<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Assets;

use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;

class SchemaParameterFilter extends AbstractMapParameterFilter {
    protected function loadMap(): array
    {
        return [
            'schema' => '',
            'version' => '',
        ];
    }
}

