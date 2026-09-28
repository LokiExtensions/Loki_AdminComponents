<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Integration;

use PHPUnit\Framework\TestCase;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsEnabled;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegistered;
use Yireo\IntegrationTestHelper\Test\Integration\Traits\AssertModuleIsRegisteredForReal;

class ModuleTest extends TestCase
{
    use AssertModuleIsRegistered;
    use AssertModuleIsRegisteredForReal;
    use AssertModuleIsEnabled;

    public function testIfModuleIsEnabled()
    {
        $requiredModules = [
            'Loki_Base',
            'Loki_Components',
            'Loki_AdminComponents',
        ];
        foreach ($requiredModules as $moduleName) {
            $this->assertModuleIsRegistered($moduleName);
            $this->assertModuleIsEnabled($moduleName);
        }
    }
}
