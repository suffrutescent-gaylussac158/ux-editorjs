<?php

namespace Makraz\EditorjsBundle\Form;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

class EditorjsAdminField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->addFormTheme('@Editorjs/form.html.twig', '@EasyAdmin/crud/form_theme.html.twig')
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType(EditorjsType::class)
        ;
    }
}
