<?php
declare(strict_types = 1);
namespace Slothsoft\Schema\Exceptions;

use PHPUnit\Framework\TestCase;

/**
 * SchemaVersioningNotFoundExceptionTest
 *
 * @see SchemaVersioningNotFoundException
 *
 * @todo auto-generated
 */
class SchemaVersioningNotFoundExceptionTest extends TestCase {
    
    public function testClassExists(): void {
        $this->assertTrue(class_exists(SchemaVersioningNotFoundException::class), "Failed to load class 'Slothsoft\Schema\Exceptions\SchemaVersioningNotFoundException'!");
    }
}