<?php

namespace Makraz\EditorjsBundle\Form;

use Makraz\EditorjsBundle\DTO\Enums\EditorjsTool;
use Makraz\EditorjsBundle\DTO\Tools\PerToolTuneInterface;
use Makraz\EditorjsBundle\DTO\Tools\ToolInterface;
use Makraz\EditorjsBundle\DTO\Tools\TuneInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditorjsType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $tools = $this->resolveTools($options['editorjs_tools']);
        $tunes = $this->resolveTuneNames($options['editorjs_tools']);

        $extraOptions = $options['editorjs_options'];

        // Symfony 8 compatibility: callable defaults are no longer auto-resolved
        if (\is_callable($extraOptions)) {
            $extraResolver = new OptionsResolver();
            self::configureExtraOptions($extraResolver);
            $extraOptions($extraResolver);
            $extraOptions = $extraResolver->resolve([]);
        }

        $view->vars['attr']['data-tools'] = json_encode($tools);
        $view->vars['attr']['data-extra-options'] = json_encode($extraOptions);
        $view->vars['attr']['data-tunes'] = json_encode($tunes);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'error_bubbling' => true,
            'editorjs_tools' => [
                EditorjsTool::HEADER,
                EditorjsTool::LIST,
                EditorjsTool::PARAGRAPH,
            ],
            'editorjs_options' => static function (OptionsResolver $extraResolver) {
                self::configureExtraOptions($extraResolver);
            },
        ]);

        $resolver->setAllowedTypes('editorjs_tools', 'array');
        $resolver->setAllowedTypes('editorjs_options', ['array', 'callable']);
    }

    private static function configureExtraOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('placeholder', 'Start writing...')
            ->setAllowedTypes('placeholder', 'string')
        ;
        $resolver
            ->setDefault('minHeight', 200)
            ->setAllowedTypes('minHeight', ['int', 'string'])
        ;
        $resolver
            ->setDefault('readOnly', false)
            ->setAllowedTypes('readOnly', 'bool')
        ;
        $resolver
            ->setDefault('autofocus', false)
            ->setAllowedTypes('autofocus', 'bool')
        ;
        $resolver
            ->setDefault('inlineToolbar', true)
            ->setAllowedTypes('inlineToolbar', ['bool', 'array'])
        ;
        $resolver
            ->setDefault('maxWidth', 650)
            ->setAllowedTypes('maxWidth', ['int', 'string'])
        ;
        $resolver
            ->setDefault('border', false)
            ->setAllowedTypes('border', ['bool', 'string'])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'editorjs';
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }

    /**
     * @param array<EditorjsTool|ToolInterface|string> $tools
     *
     * @return list<string>
     */
    private function resolveTuneNames(array $tools): array
    {
        $tunes = [];

        foreach ($tools as $tool) {
            if ($tool instanceof TuneInterface && !$tool instanceof PerToolTuneInterface) {
                $tunes[] = $tool->getName();
            }
        }

        return $tunes;
    }

    /**
     * @param array<EditorjsTool|ToolInterface|string> $tools
     *
     * @return array<string, array<string, mixed>>
     */
    private function resolveTools(array $tools): array
    {
        $resolved = [];
        $perToolTunes = [];

        // First pass: resolve all tools and collect per-tool tunes
        foreach ($tools as $tool) {
            if ($tool instanceof ToolInterface) {
                $entry = [];

                $config = $tool->getConfig();
                if ([] !== $config) {
                    $entry['config'] = $config;
                }

                $package = $tool->getPackage();
                if (null !== $package) {
                    $entry['package'] = $package;
                }

                $resolved[$tool->getName()] = $entry;

                if ($tool instanceof PerToolTuneInterface) {
                    $perToolTunes[] = $tool;
                }
            } elseif ($tool instanceof EditorjsTool) {
                $resolved[$tool->value] = [];
            } elseif (\is_string($tool)) {
                $resolved[$tool] = [];
            }
        }

        // Second pass: apply per-tool tunes to their target tools
        foreach ($perToolTunes as $tune) {
            foreach ($tune->getApplicableTools() as $toolName) {
                if (isset($resolved[$toolName])) {
                    $resolved[$toolName]['tunes'] ??= [];
                    $resolved[$toolName]['tunes'][] = $tune->getName();
                }
            }
        }

        return $resolved;
    }
}
