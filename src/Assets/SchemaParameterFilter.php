<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Assets;

use Slothsoft\Core\IO\Sanitizer\StringSanitizer;
use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;

class SchemaParameterFilter extends AbstractMapParameterFilter
{

    protected function createValueSanitizers(): array
    {
        return [
            'schema' => new StringSanitizer(),
            'version' => new StringSanitizer(),
        ];
    }

}
