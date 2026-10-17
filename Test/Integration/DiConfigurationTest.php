<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Integration;

use Loki\AdminComponents\Form\Fieldset\FieldsetsResolver;
use Loki\AdminComponents\Form\Fieldset\FieldsetsResolver\ProviderFieldsetsResolver;
use Loki\AdminComponents\Ui\Button\ButtonsResolver;
use Loki\AdminComponents\Ui\Button\ButtonsResolver\BlockButtonsResolver;
use Loki\AdminComponents\Ui\Button\ButtonsResolver\DefaultFormButtonsResolver;
use Loki\AdminComponents\Ui\Button\ButtonsResolver\DefaultGridButtonsResolver;
use Loki\AdminComponents\Ui\Button\ButtonsResolver\ProviderButtonsResolver;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

#[AppArea('adminhtml')]
class DiConfigurationTest extends TestCase
{
    public function testButtonsResolversAreConfiguredInOrder(): void
    {
        $buttonsResolver = ObjectManager::getInstance()->create(ButtonsResolver::class);
        $resolvers = $this->getPrivateProperty($buttonsResolver, 'buttonsResolvers');

        $this->assertSame(['provider', 'block', 'default_grid', 'default_form'], array_keys($resolvers));
        $this->assertInstanceOf(ProviderButtonsResolver::class, $resolvers['provider']);
        $this->assertInstanceOf(BlockButtonsResolver::class, $resolvers['block']);
        $this->assertInstanceOf(DefaultGridButtonsResolver::class, $resolvers['default_grid']);
        $this->assertInstanceOf(DefaultFormButtonsResolver::class, $resolvers['default_form']);
    }

    public function testFieldsetsResolversDoNotResolveProviderFieldsetsTwice(): void
    {
        $fieldsetsResolver = ObjectManager::getInstance()->create(FieldsetsResolver::class);
        $resolvers = $this->getPrivateProperty($fieldsetsResolver, 'fieldsetsResolvers');

        $this->assertSame(['provider'], array_keys($resolvers));
        $this->assertInstanceOf(ProviderFieldsetsResolver::class, $resolvers['provider']);

        $resolverClasses = array_map(fn (object $resolver) => get_class($resolver), $resolvers);
        $this->assertSame(array_unique($resolverClasses), $resolverClasses);
    }

    private function getPrivateProperty(object $object, string $propertyName): array
    {
        $property = new ReflectionProperty($object, $propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}
