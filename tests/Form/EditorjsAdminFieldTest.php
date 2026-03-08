<?php

namespace Makraz\EditorjsBundle\Tests\Form;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use Makraz\EditorjsBundle\Form\EditorjsAdminField;
use Makraz\EditorjsBundle\Form\EditorjsType;
use PHPUnit\Framework\TestCase;

class EditorjsAdminFieldTest extends TestCase
{
    public function testImplementsFieldInterface(): void
    {
        $field = EditorjsAdminField::new('content');
        $this->assertInstanceOf(FieldInterface::class, $field);
    }

    public function testFieldProperty(): void
    {
        $field = EditorjsAdminField::new('body');
        $dto = $field->getAsDto();

        $this->assertSame('body', $dto->getProperty());
    }

    public function testFieldLabel(): void
    {
        $field = EditorjsAdminField::new('content', 'Article Body');
        $dto = $field->getAsDto();

        $this->assertSame('Article Body', $dto->getLabel());
    }

    public function testFieldLabelNull(): void
    {
        $field = EditorjsAdminField::new('content');
        $dto = $field->getAsDto();

        $this->assertNull($dto->getLabel());
    }

    public function testFieldFormType(): void
    {
        $field = EditorjsAdminField::new('content');
        $dto = $field->getAsDto();

        $this->assertSame(EditorjsType::class, $dto->getFormType());
    }

    public function testFieldLabelFalse(): void
    {
        $field = EditorjsAdminField::new('content', false);
        $dto = $field->getAsDto();

        $this->assertFalse($dto->getLabel());
    }
}
