<?php
declare(strict_types = 1);

namespace Slothsoft\Schema\Assets;

use Slothsoft\Core\IO\Sanitizer\StringSanitizer;
use Slothsoft\Farah\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\Module\Asset\ParameterFilterStrategy\AbstractMapParameterFilter;

class SchemaParameterFilter extends AbstractMapParameterFilter {
    
    /** @return array<array-key, FarahUrl> */
    public static function createSchemaVersions(string $schemaId, string $version): array {
        if ($version) {
            $schemaId .= "/$version";
            $schemaUrl = FarahUrl::createFromReference($schemaId);
            $schemaVersions = [
                $version => $schemaUrl
            ];
        } else {
            $schemaUrl = FarahUrl::createFromReference($schemaId);
            $schemaVersions = [];
            foreach (Module::resolveToAsset($schemaUrl)->getAssetChildren() as $asset) {
                $version = $asset->getManifestElement()->getAttribute('name');
                $schemaVersions[$version] = $asset->createUrl();
            }
            uksort($schemaVersions, static fn(int|string $left, int|string $right): int => version_compare((string) $left, (string) $right));
            $schemaVersions = array_reverse($schemaVersions);
        }
        return $schemaVersions;
    }
    
    protected function createValueSanitizers(): array {
        return [
            'schema' => new StringSanitizer(),
            'version' => new StringSanitizer()
        ];
    }
}

