<?php

namespace Makraz\EditorjsBundle\Tests\DependencyInjection;

use Makraz\EditorjsBundle\Controller\EditorjsUploadController;
use Makraz\EditorjsBundle\DependencyInjection\EditorjsExtension;
use Makraz\EditorjsBundle\Upload\FlysystemUploadHandler;
use Makraz\EditorjsBundle\Upload\LocalUploadHandler;
use Makraz\EditorjsBundle\Upload\UploadHandlerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EditorjsExtensionTest extends TestCase
{
    public function testLoadWithUploadDisabled(): void
    {
        $container = $this->createContainer();
        $extension = new EditorjsExtension();

        $extension->load([['upload' => ['enabled' => false]]], $container);

        $this->assertFalse($container->hasDefinition(UploadHandlerInterface::class));
        $this->assertFalse($container->hasDefinition(EditorjsUploadController::class));
    }

    public function testLoadWithLocalHandler(): void
    {
        $container = $this->createContainer();
        $extension = new EditorjsExtension();

        $extension->load([['upload' => [
            'enabled' => true,
            'handler' => 'local',
            'local_dir' => '/tmp/uploads',
            'local_public_path' => '/uploads',
        ]]], $container);

        $this->assertTrue($container->hasDefinition(UploadHandlerInterface::class));
        $this->assertTrue($container->hasDefinition(EditorjsUploadController::class));

        $handlerDef = $container->getDefinition(UploadHandlerInterface::class);
        $this->assertSame(LocalUploadHandler::class, $handlerDef->getClass());
    }

    public function testLoadWithCustomHandler(): void
    {
        $container = $this->createContainer();
        $extension = new EditorjsExtension();

        $extension->load([['upload' => [
            'enabled' => true,
            'handler' => 'custom',
            'custom_handler' => 'app.my_handler',
        ]]], $container);

        $this->assertTrue($container->hasAlias(UploadHandlerInterface::class));
    }

    public function testCustomHandlerWithoutServiceIdThrows(): void
    {
        $container = $this->createContainer();
        $extension = new EditorjsExtension();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('custom_handler');

        $extension->load([['upload' => [
            'enabled' => true,
            'handler' => 'custom',
        ]]], $container);
    }

    public function testFlysystemHandlerWithoutStorageThrows(): void
    {
        $container = $this->createContainer();
        $extension = new EditorjsExtension();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('flysystem_storage');

        $extension->load([['upload' => [
            'enabled' => true,
            'handler' => 'flysystem',
        ]]], $container);
    }

    public function testFlysystemHandlerRegistersCorrectly(): void
    {
        $container = $this->createContainer();
        $extension = new EditorjsExtension();

        $extension->load([['upload' => [
            'enabled' => true,
            'handler' => 'flysystem',
            'flysystem_storage' => 'default.storage',
            'flysystem_path' => 'media/editorjs',
            'flysystem_public_url' => 'https://cdn.example.com',
        ]]], $container);

        $this->assertTrue($container->hasDefinition(UploadHandlerInterface::class));
        $handlerDef = $container->getDefinition(UploadHandlerInterface::class);

        $this->assertSame(FlysystemUploadHandler::class, $handlerDef->getClass());
        $this->assertSame('media/editorjs', $handlerDef->getArgument('$uploadPath'));
        $this->assertSame('https://cdn.example.com', $handlerDef->getArgument('$publicUrlPrefix'));
    }

    public function testUploadControllerReceivesMaxFileSizeAndMimeTypes(): void
    {
        $container = $this->createContainer();
        $extension = new EditorjsExtension();

        $extension->load([['upload' => [
            'enabled' => true,
            'handler' => 'local',
            'max_file_size' => 10_000_000,
            'allowed_mime_types' => ['image/png'],
        ]]], $container);

        $controllerDef = $container->getDefinition(EditorjsUploadController::class);
        $this->assertSame(10_000_000, $controllerDef->getArgument('$maxFileSize'));
        $this->assertSame(['image/png'], $controllerDef->getArgument('$allowedMimeTypes'));
    }

    public function testPrependAddsTwigFormTheme(): void
    {
        $container = $this->createContainer();
        $container->setParameter('kernel.bundles', ['TwigBundle' => 'Symfony\Bundle\TwigBundle\TwigBundle']);

        $extension = new EditorjsExtension();
        $extension->prepend($container);

        $twigConfig = $container->getExtensionConfig('twig');
        $this->assertNotEmpty($twigConfig);

        $formThemes = $twigConfig[0]['form_themes'] ?? [];
        $this->assertContains('@Editorjs/form.html.twig', $formThemes);
    }

    public function testPrependSkipsTwigWhenNotInstalled(): void
    {
        $container = $this->createContainer();
        $container->setParameter('kernel.bundles', []);

        $extension = new EditorjsExtension();
        $extension->prepend($container);

        $twigConfig = $container->getExtensionConfig('twig');
        $this->assertEmpty($twigConfig);
    }

    public function testControllerIsPublic(): void
    {
        $container = $this->createContainer();
        $extension = new EditorjsExtension();

        $extension->load([['upload' => [
            'enabled' => true,
            'handler' => 'local',
        ]]], $container);

        $controllerDef = $container->getDefinition(EditorjsUploadController::class);
        $this->assertTrue($controllerDef->isPublic());
    }

    public function testControllerHasServiceArgumentsTag(): void
    {
        $container = $this->createContainer();
        $extension = new EditorjsExtension();

        $extension->load([['upload' => [
            'enabled' => true,
            'handler' => 'local',
        ]]], $container);

        $controllerDef = $container->getDefinition(EditorjsUploadController::class);
        $this->assertTrue($controllerDef->hasTag('controller.service_arguments'));
    }

    public function testPrependAddsAssetMapperPathWhenAvailable(): void
    {
        $frameworkBundlePath = (new \ReflectionClass(\Symfony\Bundle\FrameworkBundle\FrameworkBundle::class))->getFileName();
        $frameworkBundleDir = \dirname($frameworkBundlePath);

        $container = $this->createContainer();
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.bundles_metadata', [
            'FrameworkBundle' => ['path' => $frameworkBundleDir],
        ]);

        $extension = new EditorjsExtension();
        $extension->prepend($container);

        $frameworkConfig = $container->getExtensionConfig('framework');
        $this->assertNotEmpty($frameworkConfig);

        $paths = $frameworkConfig[0]['asset_mapper']['paths'] ?? [];
        $this->assertNotEmpty($paths);
        $this->assertContains('@makraz/ux-editorjs', $paths);
    }

    public function testPrependSkipsAssetMapperWhenFrameworkBundleMissing(): void
    {
        $container = $this->createContainer();
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.bundles_metadata', []);

        $extension = new EditorjsExtension();
        $extension->prepend($container);

        $frameworkConfig = $container->getExtensionConfig('framework');
        $this->assertEmpty($frameworkConfig);
    }

    public function testPrependSkipsAssetMapperWhenBundlesMetadataNotArray(): void
    {
        $container = $this->createContainer();
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.bundles_metadata', 'invalid');

        $extension = new EditorjsExtension();
        $extension->prepend($container);

        $frameworkConfig = $container->getExtensionConfig('framework');
        $this->assertEmpty($frameworkConfig);
    }

    public function testDefaultConfigRegistersFormTypeService(): void
    {
        $container = $this->createContainer();
        $extension = new EditorjsExtension();

        $extension->load([[]], $container);

        $this->assertTrue($container->hasDefinition('form.ux_editorjs'));
    }

    public function testLocalHandlerArguments(): void
    {
        $container = $this->createContainer();
        $extension = new EditorjsExtension();

        $extension->load([['upload' => [
            'enabled' => true,
            'handler' => 'local',
            'local_dir' => '/var/www/uploads',
            'local_public_path' => '/media/uploads',
        ]]], $container);

        $handlerDef = $container->getDefinition(UploadHandlerInterface::class);
        $this->assertSame('/var/www/uploads', $handlerDef->getArgument('$uploadDir'));
        $this->assertSame('/media/uploads', $handlerDef->getArgument('$publicPath'));
    }

    private function createContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.project_dir', sys_get_temp_dir());
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.bundles_metadata', []);

        return $container;
    }
}
