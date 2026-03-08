<?php

namespace Makraz\EditorjsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('editorjs');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('upload')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultFalse()
                            ->info('Enable the built-in upload controller.')
                        ->end()
                        ->enumNode('handler')
                            ->values(['local', 'flysystem', 'custom'])
                            ->defaultValue('local')
                            ->info('Upload handler: "local" for filesystem, "flysystem" for League Flysystem, "custom" for your own service.')
                        ->end()
                        ->scalarNode('local_dir')
                            ->defaultValue('%kernel.project_dir%/public/uploads/editorjs')
                            ->info('Directory for local uploads.')
                        ->end()
                        ->scalarNode('local_public_path')
                            ->defaultValue('/uploads/editorjs')
                            ->info('Public URL path prefix for local uploads.')
                        ->end()
                        ->scalarNode('flysystem_storage')
                            ->defaultNull()
                            ->info('Flysystem storage service ID (e.g. "default.storage").')
                        ->end()
                        ->scalarNode('flysystem_path')
                            ->defaultValue('uploads/editorjs')
                            ->info('Path prefix within the Flysystem filesystem.')
                        ->end()
                        ->scalarNode('flysystem_public_url')
                            ->defaultValue('')
                            ->info('Public URL prefix for Flysystem files (e.g. "https://cdn.example.com").')
                        ->end()
                        ->scalarNode('custom_handler')
                            ->defaultNull()
                            ->info('Service ID of your custom UploadHandlerInterface implementation.')
                        ->end()
                        ->integerNode('max_file_size')
                            ->defaultValue(5 * 1024 * 1024)
                            ->info('Maximum upload file size in bytes (default: 5 MB).')
                        ->end()
                        ->arrayNode('allowed_mime_types')
                            ->scalarPrototype()->end()
                            ->defaultValue(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                            ->info('Allowed MIME types for uploads.')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
