<?php

namespace Makraz\EditorjsBundle\DependencyInjection;

use League\Flysystem\FilesystemOperator;
use Makraz\EditorjsBundle\Controller\EditorjsUploadController;
use Makraz\EditorjsBundle\Upload\FlysystemUploadHandler;
use Makraz\EditorjsBundle\Upload\LocalUploadHandler;
use Makraz\EditorjsBundle\Upload\UploadHandlerInterface;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class EditorjsExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (\is_array($bundles) && isset($bundles['TwigBundle'])) {
            $container->prependExtensionConfig('twig', ['form_themes' => ['@Editorjs/form.html.twig']]);
        }

        if ($this->isAssetMapperAvailable($container)) {
            $container->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        __DIR__.'/../../assets/dist' => '@makraz/ux-editorjs',
                    ],
                ],
            ]);
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->configureUpload($config['upload'], $container);
    }

    private function configureUpload(array $uploadConfig, ContainerBuilder $container): void
    {
        if (!$uploadConfig['enabled']) {
            return;
        }

        // Register the upload handler
        match ($uploadConfig['handler']) {
            'local' => $this->registerLocalHandler($uploadConfig, $container),
            'flysystem' => $this->registerFlysystemHandler($uploadConfig, $container),
            'custom' => $this->registerCustomHandler($uploadConfig, $container),
        };

        // Register the upload controller
        $controllerDef = new Definition(EditorjsUploadController::class);
        $controllerDef->setArgument('$uploadHandler', new Reference(UploadHandlerInterface::class));
        $controllerDef->setArgument('$maxFileSize', $uploadConfig['max_file_size']);
        $controllerDef->setArgument('$allowedMimeTypes', $uploadConfig['allowed_mime_types']);
        $controllerDef->addTag('controller.service_arguments');
        $controllerDef->setPublic(true);

        $container->setDefinition(EditorjsUploadController::class, $controllerDef);
    }

    private function registerLocalHandler(array $config, ContainerBuilder $container): void
    {
        $def = new Definition(LocalUploadHandler::class);
        $def->setArgument('$uploadDir', $config['local_dir']);
        $def->setArgument('$publicPath', $config['local_public_path']);
        $def->setArgument('$slugger', new Reference('slugger'));

        $container->setDefinition(UploadHandlerInterface::class, $def);
    }

    private function registerFlysystemHandler(array $config, ContainerBuilder $container): void
    {
        if (!interface_exists(FilesystemOperator::class)) {
            throw new \LogicException('Flysystem upload handler requires "league/flysystem-bundle". Run: composer require league/flysystem-bundle'); // @codeCoverageIgnore
        }

        if (null === $config['flysystem_storage']) {
            throw new \LogicException('You must configure "editorjs.upload.flysystem_storage" when using the Flysystem handler.');
        }

        $def = new Definition(FlysystemUploadHandler::class);
        $def->setArgument('$filesystem', new Reference($config['flysystem_storage']));
        $def->setArgument('$uploadPath', $config['flysystem_path']);
        $def->setArgument('$publicUrlPrefix', $config['flysystem_public_url']);
        $def->setArgument('$slugger', new Reference('slugger'));

        $container->setDefinition(UploadHandlerInterface::class, $def);
    }

    private function registerCustomHandler(array $config, ContainerBuilder $container): void
    {
        if (null === $config['custom_handler']) {
            throw new \LogicException('You must configure "editorjs.upload.custom_handler" when using the custom handler.');
        }

        $container->setAlias(UploadHandlerInterface::class, $config['custom_handler']);
    }

    private function isAssetMapperAvailable(ContainerBuilder $container): bool
    {
        if (!interface_exists(AssetMapperInterface::class)) {
            return false; // @codeCoverageIgnore
        }

        $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');
        if (!\is_array($bundlesMetadata)) {
            return false;
        }

        if (!isset($bundlesMetadata['FrameworkBundle'])) {
            return false;
        }

        return is_file($bundlesMetadata['FrameworkBundle']['path'].'/Resources/config/asset_mapper.php');
    }
}
