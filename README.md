# Editor.js Bundle for Symfony using Symfony UX

Symfony UX Bundle implementing [Editor.js](https://editorjs.io/) — a block-style editor that outputs clean JSON data.

Also working out of the box with EasyAdmin.

If you need an easy-to-use block editor (with no complex configuration) in a Symfony project, this is what you need.

* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Available Tools](#available-tools)
* [Community Tools (built-in DTOs)](#community-tools-built-in-dtos)
* [Block Tunes](#block-tunes)
* [Advanced Tool Configuration](#advanced-tool-configuration)
* [Editor Options](#editor-options)
* [EasyAdmin Integration](#easyadmin-integration)
* [Image Upload](#image-upload)
* [Data Format](#data-format)
* [Extending the Editor](#extending-the-editor)
* [JavaScript Events](#javascript-events)

## Installation

### Step 1: Require the bundle

```sh
composer require makraz/ux-editorjs
```

If you are using the **AssetMapper** component, you're done!

### Step 2: JavaScript dependencies (Webpack Encore only)

If you are using **Webpack Encore** (skip this step if using AssetMapper):

```sh
yarn install --force && yarn watch
```

Or with npm:

```sh
npm install --force && npm run watch
```

That's it. You can now use `EditorjsType` in your Symfony forms.

## Basic Usage

In a form, use `EditorjsType`. It works like a classic form type with additional options:

```php
use Makraz\EditorjsBundle\Form\EditorjsType;
use Makraz\EditorjsBundle\DTO\Enums\EditorjsTool;

public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('content', EditorjsType::class, [
            'editorjs_tools' => [
                EditorjsTool::HEADER,
                EditorjsTool::LIST,
                EditorjsTool::PARAGRAPH,
            ],
        ])
    ;
}
```

By default, the editor comes with `Header`, `List`, and `Paragraph` tools enabled.

You can add as many Editor.js fields on a single page as you need, just like any normal form field.

## Available Tools

### Built-in tools (no extra package required)

These tools are bundled with `@editorjs/*` packages and can be enabled via the `EditorjsTool` enum or their DTO class:

| Enum | DTO Class | Description |
|------|-----------|-------------|
| `EditorjsTool::HEADER` | `HeaderTool` | Heading blocks (H1–H6) |
| `EditorjsTool::LIST` | `ListTool` | Ordered and unordered lists |
| `EditorjsTool::PARAGRAPH` | `ParagraphTool` | Paragraph blocks |
| `EditorjsTool::IMAGE` | `ImageTool` | Image upload and embed |
| `EditorjsTool::CODE` | `CodeTool` | Code blocks |
| `EditorjsTool::QUOTE` | `QuoteTool` | Blockquotes |
| `EditorjsTool::WARNING` | `WarningTool` | Warning/alert blocks |
| `EditorjsTool::TABLE` | `TableTool` | Tables with optional headings |
| `EditorjsTool::DELIMITER` | `DelimiterTool` | Horizontal delimiter |
| `EditorjsTool::EMBED` | `EmbedTool` | Embeds (YouTube, Vimeo, CodePen, GitHub) |
| `EditorjsTool::MARKER` | `MarkerTool` | Text highlighting (inline) |
| `EditorjsTool::INLINE_CODE` | `InlineCodeTool` | Inline code (inline) |
| `EditorjsTool::CHECKLIST` | `ChecklistTool` | Checklists |
| `EditorjsTool::LINK` | `LinkTool` | Link previews |
| `EditorjsTool::RAW` | `RawTool` | Raw HTML blocks |
| `EditorjsTool::UNDERLINE` | `UnderlineTool` | Underline text (inline) |

Quick usage — pass enum values directly for default configuration:

```php
'editorjs_tools' => [
    EditorjsTool::HEADER,
    EditorjsTool::LIST,
    EditorjsTool::CODE,
    EditorjsTool::QUOTE,
    EditorjsTool::DELIMITER,
    EditorjsTool::MARKER,
    EditorjsTool::INLINE_CODE,
],
```

## Community Tools (built-in DTOs)

The bundle ships with ready-to-use DTOs for popular community tools. These require adding the corresponding npm package to your project (see [Adding Community Tools](#adding-community-tools)), but no JavaScript code is needed — the bundle handles the dynamic import.

| DTO Class | Name | Package | Description |
|-----------|------|---------|-------------|
| `AlignmentParagraphTool` | `paragraph` | `editorjs-paragraph-with-alignment` | Paragraph with text alignment |
| `AlignmentHeaderTool` | `header` | `editorjs-header-with-alignment` | Header with text alignment |
| `NestedListTool` | `list` | `@editorjs/nested-list` | Lists with nesting support |
| `AlertTool` | `alert` | `editorjs-alert` | Alert/notification blocks |
| `AttachesTool` | `attaches` | `@editorjs/attaches` | File attachment uploads |
| `SimpleImageTool` | `simpleImage` | `@editorjs/simple-image` | Simple image (paste URL, no upload) |
| `ToggleBlockTool` | `toggle` | `editorjs-toggle-block` | Collapsible toggle blocks |
| `TextColorTool` | `textColor` | `editorjs-text-color-plugin` | Text color / background marker |
| `HyperlinkTool` | `hyperlink` | `editorjs-hyperlink` | Advanced hyperlink with target/rel |
| `StrikethroughTool` | `strikethrough` | `@sotaproject/strikethrough` | Strikethrough text (inline) |
| `ColumnsTool` | `columns` | `@calumk/editorjs-columns` | Multi-column layouts with nested editors |

### Usage examples

```php
use Makraz\EditorjsBundle\DTO\Tools\AlignmentParagraphTool;
use Makraz\EditorjsBundle\DTO\Tools\AlignmentHeaderTool;
use Makraz\EditorjsBundle\DTO\Tools\AlertTool;
use Makraz\EditorjsBundle\DTO\Tools\AttachesTool;
use Makraz\EditorjsBundle\DTO\Tools\ToggleBlockTool;
use Makraz\EditorjsBundle\DTO\Tools\TextColorTool;
use Makraz\EditorjsBundle\DTO\Tools\NestedListTool;
use Makraz\EditorjsBundle\DTO\Tools\HyperlinkTool;
use Makraz\EditorjsBundle\DTO\Tools\StrikethroughTool;
use Makraz\EditorjsBundle\DTO\Tools\SimpleImageTool;
use Makraz\EditorjsBundle\DTO\Tools\ColumnsTool;

$builder->add('content', EditorjsType::class, [
    'editorjs_tools' => [
        // Aligned paragraph (replaces built-in paragraph)
        new AlignmentParagraphTool(defaultAlignment: 'left'),

        // Aligned header (replaces built-in header)
        new AlignmentHeaderTool(levels: [1, 2, 3], defaultLevel: 2, defaultAlignment: 'left'),

        // Nested list (replaces built-in list)
        new NestedListTool(defaultStyle: 'unordered'),

        // Alert block
        new AlertTool(defaultType: 'info', defaultAlign: 'left'),

        // File attachments
        new AttachesTool(endpoint: '/api/upload/file'),

        // Toggle block
        new ToggleBlockTool(placeholder: 'Toggle title'),

        // Text color
        new TextColorTool(defaultColor: '#FF1300', type: 'text'),

        // Hyperlink with target/rel
        new HyperlinkTool(shortcut: 'CMD+K', target: '_blank', rel: 'nofollow'),

        // Multi-column layout
        new ColumnsTool(),

        // Other tools
        new StrikethroughTool(),
        new SimpleImageTool(),
        EditorjsTool::CODE,
        EditorjsTool::QUOTE,
        EditorjsTool::DELIMITER,
    ],
]);
```

### Community Tool Configuration Reference

#### AlignmentParagraphTool

```php
new AlignmentParagraphTool(
    placeholder: '',              // Placeholder text
    defaultAlignment: 'left',     // 'left', 'center', or 'right'
    preserveBlank: false,         // Preserve empty paragraphs
)
```

#### AlignmentHeaderTool

```php
new AlignmentHeaderTool(
    placeholder: 'Enter a header',
    levels: [1, 2, 3, 4, 5, 6],
    defaultLevel: 2,
    defaultAlignment: 'left',     // 'left', 'center', or 'right'
)
```

#### AlertTool

```php
new AlertTool(
    defaultType: 'info',                    // 'primary', 'secondary', 'info', 'success', 'warning', 'danger'
    defaultAlign: 'left',                   // 'left', 'center', 'right'
    messagePlaceholder: 'Enter alert message',
)
```

#### AttachesTool

```php
new AttachesTool(
    endpoint: '/api/upload/file',   // Upload endpoint (required for file uploads)
    field: 'file',                  // Form field name
    buttonText: 'Select file',     // Upload button text
    types: 'application/pdf',      // Allowed MIME types (comma-separated string)
    errorMessage: 'Upload failed', // Custom error message
)
```

#### NestedListTool

```php
new NestedListTool(
    defaultStyle: 'unordered',  // 'ordered' or 'unordered'
)
```

#### ToggleBlockTool

```php
new ToggleBlockTool(
    placeholder: 'Toggle title',  // Placeholder text for the toggle
)
```

#### TextColorTool

```php
new TextColorTool(
    defaultColor: '#FF1300',  // Default color
    type: 'text',             // 'text' for text color, 'marker' for background highlight
)
```

> **Note**: Use `type: 'text'` to register as `textColor`, or `type: 'marker'` to register as `colorMarker`. You can use both in the same form.

#### HyperlinkTool

```php
new HyperlinkTool(
    shortcut: 'CMD+K',                                  // Keyboard shortcut
    target: '_blank',                                    // Default target
    rel: 'nofollow',                                     // Default rel attribute
    availableTargets: ['_blank', '_self'],                // Dropdown options for target
    availableRels: ['nofollow', 'noreferrer', 'ugc'],    // Dropdown options for rel
)
```

#### ColumnsTool

```php
// Default: all sibling tools are automatically available inside columns
new ColumnsTool()
```

The columns tool automatically receives the EditorJS library and all other resolved tools, so nested editors inside columns can use the same tools as the parent editor. No extra configuration is needed.

#### SimpleImageTool / StrikethroughTool

No configuration options — just instantiate:

```php
new SimpleImageTool()
new StrikethroughTool()
```

## Block Tunes

[Block Tunes](https://editorjs.io/block-tunes) are special tools that apply globally to all blocks (e.g. text alignment, indentation). The bundle provides dedicated DTOs for common tunes and a `TuneInterface` marker.

Tunes are passed in the same `editorjs_tools` array — the bundle automatically registers them as both tools and global tunes in the EditorJS config.

```php
use Makraz\EditorjsBundle\Form\EditorjsType;
use Makraz\EditorjsBundle\DTO\Enums\EditorjsTool;
use Makraz\EditorjsBundle\DTO\Tools\AlignmentBlockTune;
use Makraz\EditorjsBundle\DTO\Tools\TextVariantTune;
use Makraz\EditorjsBundle\DTO\Tools\IndentTune;

$builder->add('content', EditorjsType::class, [
    'editorjs_tools' => [
        EditorjsTool::HEADER,
        EditorjsTool::PARAGRAPH,
        EditorjsTool::LIST,

        // Block Tunes — applied globally to all blocks
        new AlignmentBlockTune(default: 'left'),
        new TextVariantTune(),
        new IndentTune(maxIndent: 5, indentSize: 24, direction: 'ltr'),
    ],
]);
```

### Built-in Tunes

| DTO Class | Name | Package | Options |
|-----------|------|---------|---------|
| `AlignmentBlockTune` | `textAlignment` | `editorjs-alignment-blocktune` | `default`: `'left'`, `'center'`, `'right'` |
| `TextVariantTune` | `textVariant` | `@editorjs/text-variant-tune` | — |
| `IndentTune` | `indentTune` | `editorjs-indent-tune` | `maxIndent`, `indentSize`, `direction` |

### Creating a Custom Tune

Implement `TuneInterface` (which extends `ToolInterface`):

```php
use Makraz\EditorjsBundle\DTO\Tools\TuneInterface;

final class MyCustomTune implements TuneInterface
{
    public function getName(): string
    {
        return 'myTune';
    }

    public function getPackage(): ?string
    {
        return 'my-custom-tune-package';
    }

    public function getConfig(): array
    {
        return [];
    }
}
```

## Advanced Tool Configuration

### Built-in Tool Configuration Reference

For finer control over built-in tools, use the DTO classes instead of the enum. You can mix both approaches:

```php
use Makraz\EditorjsBundle\Form\EditorjsType;
use Makraz\EditorjsBundle\DTO\Tools\HeaderTool;
use Makraz\EditorjsBundle\DTO\Tools\ListTool;
use Makraz\EditorjsBundle\DTO\Tools\ImageTool;
use Makraz\EditorjsBundle\DTO\Tools\TableTool;
use Makraz\EditorjsBundle\DTO\Enums\EditorjsTool;

$builder->add('content', EditorjsType::class, [
    'editorjs_tools' => [
        new HeaderTool(levels: [1, 2, 3], defaultLevel: 2),
        new ListTool(defaultStyle: 'ordered', maxLevel: 3),
        new ImageTool(uploadEndpoint: '/editorjs/upload/file'),
        new TableTool(rows: 3, cols: 4, withHeadings: true),
        EditorjsTool::CODE,
        EditorjsTool::QUOTE,
        EditorjsTool::DELIMITER,
    ],
]);
```

#### HeaderTool

```php
new HeaderTool(
    placeholder: 'Enter a header',
    levels: [1, 2, 3, 4, 5, 6],
    defaultLevel: 2,
)
```

#### ListTool

```php
new ListTool(
    defaultStyle: 'unordered',  // 'ordered' or 'unordered'
    maxLevel: 3,
)
```

#### ImageTool

```php
new ImageTool(
    uploadEndpoint: '/editorjs/upload/file',
    uploadByUrlEndpoint: '/editorjs/upload/url',
    captionPlaceholder: true,
    withBorder: false,
    stretched: false,
    withBackground: false,
)
```

> **Note**: See [Image Upload](#image-upload) for the built-in upload controller.

#### TableTool

```php
new TableTool(
    rows: 2,
    cols: 3,
    withHeadings: true,
)
```

#### QuoteTool

```php
new QuoteTool(
    quotePlaceholder: 'Enter a quote',
    captionPlaceholder: 'Quote\'s author',
)
```

#### WarningTool

```php
new WarningTool(
    titlePlaceholder: 'Title',
    messagePlaceholder: 'Message',
)
```

#### EmbedTool

```php
new EmbedTool(
    services: ['youtube', 'vimeo', 'codepen', 'github'],
)
```

#### LinkTool

```php
new LinkTool(
    fetchEndpoint: '/api/link-metadata',
)
```

#### CodeTool

```php
new CodeTool(
    placeholder: 'Enter code',
)
```

#### ParagraphTool

```php
new ParagraphTool(
    placeholder: '',
    preserveBlank: false,
)
```

#### RawTool

```php
new RawTool(
    placeholder: 'Enter raw HTML',
)
```

#### Tools with no configuration

The following built-in tools have no additional configuration options:

- `EditorjsTool::DELIMITER` — Horizontal delimiter
- `EditorjsTool::MARKER` — Text highlighting
- `EditorjsTool::INLINE_CODE` — Inline code
- `EditorjsTool::CHECKLIST` — Checklists
- `EditorjsTool::UNDERLINE` — Underline text

## Editor Options

Use the `editorjs_options` parameter to configure global editor behavior:

```php
$builder->add('content', EditorjsType::class, [
    'editorjs_tools' => [
        EditorjsTool::HEADER,
        EditorjsTool::PARAGRAPH,
    ],
    'editorjs_options' => [
        'placeholder' => 'Start writing your article...',
        'minHeight' => 300,        // pixels (int) or CSS value (string, e.g. '50%')
        'maxWidth' => 900,         // pixels (int) or CSS value (string, e.g. '80%')
        'border' => true,          // true for default border, or a CSS border string
        'autofocus' => true,
        'readOnly' => false,
        'inlineToolbar' => true,
    ],
]);
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `placeholder` | `string` | `'Start writing...'` | Placeholder text shown in an empty editor |
| `minHeight` | `int\|string` | `200` | Minimum height of the editor — integer for pixels, string for CSS values (e.g. `'50%'`, `'20rem'`) |
| `maxWidth` | `int\|string` | `650` | Maximum width of the editor content area — integer for pixels, string for CSS values (e.g. `'80%'`, `'40rem'`) |
| `border` | `bool\|string` | `false` | Show a border around the editor. `true` for a default border (`1px solid #e0e0e0`), or a CSS border string (e.g. `'2px dashed #ccc'`) |
| `readOnly` | `bool` | `false` | Set the editor to read-only mode |
| `autofocus` | `bool` | `false` | Automatically focus the editor on page load |
| `inlineToolbar` | `bool\|array` | `true` | Enable or configure the inline toolbar |

## EasyAdmin Integration

The bundle provides a dedicated `EditorjsAdminField` for seamless EasyAdmin integration:

```php
use Makraz\EditorjsBundle\Form\EditorjsAdminField;
use Makraz\EditorjsBundle\DTO\Enums\EditorjsTool;
use Makraz\EditorjsBundle\DTO\Tools\HeaderTool;

public function configureFields(string $pageName): iterable
{
    yield EditorjsAdminField::new('content');
}
```

To customize the tools, use `setFormTypeOptions`:

```php
yield EditorjsAdminField::new('content')
    ->setFormTypeOptions([
        'editorjs_tools' => [
            new HeaderTool(levels: [1, 2, 3], defaultLevel: 2),
            EditorjsTool::LIST,
            EditorjsTool::PARAGRAPH,
            EditorjsTool::CODE,
            EditorjsTool::QUOTE,
            EditorjsTool::IMAGE,
        ],
        'editorjs_options' => [
            'placeholder' => 'Write your content here...',
            'minHeight' => 400,
        ],
    ])
;
```

The field automatically registers the Twig form theme and works with both AssetMapper and Webpack Encore.

## Image Upload

The bundle provides a built-in upload controller for the Editor.js Image Tool. Three storage options are available: **local filesystem**, **Flysystem**, or **your own custom handler**.

### Option 1: Local Filesystem (default)

Store uploads in your Symfony `public/` directory:

```yaml
# config/packages/editorjs.yaml
editorjs:
    upload:
        enabled: true
        handler: local
        local_dir: '%kernel.project_dir%/public/uploads/editorjs'
        local_public_path: '/uploads/editorjs'
        max_file_size: 5242880  # 5 MB
        allowed_mime_types:
            - image/jpeg
            - image/png
            - image/gif
            - image/webp
            - image/svg+xml
```

Then import the bundle routes:

```yaml
# config/routes/editorjs.yaml
editorjs:
    resource: '@EditorjsBundle/config/routes.php'
```

And use the ImageTool with the built-in endpoints:

```php
use Makraz\EditorjsBundle\DTO\Tools\ImageTool;

$builder->add('content', EditorjsType::class, [
    'editorjs_tools' => [
        new ImageTool(
            uploadEndpoint: '/editorjs/upload/file',
            uploadByUrlEndpoint: '/editorjs/upload/url',
        ),
        // ... other tools
    ],
]);
```

### Option 2: Flysystem

Store uploads via [League Flysystem](https://flysystem.thephpleague.com/) (S3, GCS, Azure, SFTP, etc.):

```sh
composer require league/flysystem-bundle
```

```yaml
# config/packages/editorjs.yaml
editorjs:
    upload:
        enabled: true
        handler: flysystem
        flysystem_storage: 'default.storage'  # Your Flysystem storage service ID
        flysystem_path: 'uploads/editorjs'
        flysystem_public_url: 'https://cdn.example.com'
        max_file_size: 10485760  # 10 MB
```

### Option 3: Custom Handler

Implement your own upload logic by creating a service that implements `UploadHandlerInterface`:

```php
use Makraz\EditorjsBundle\Upload\UploadHandlerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MyUploadHandler implements UploadHandlerInterface
{
    public function upload(UploadedFile $file): string
    {
        // Your upload logic here
        // Return the public URL of the uploaded file
        return 'https://example.com/path/to/file.jpg';
    }

    public function uploadByUrl(string $url): string
    {
        // Download from URL and store
        // Return the public URL
        return 'https://example.com/path/to/file.jpg';
    }
}
```

```yaml
# config/packages/editorjs.yaml
editorjs:
    upload:
        enabled: true
        handler: custom
        custom_handler: App\Upload\MyUploadHandler
```

### Upload Configuration Reference

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enabled` | `bool` | `false` | Enable the built-in upload controller |
| `handler` | `string` | `'local'` | `'local'`, `'flysystem'`, or `'custom'` |
| `local_dir` | `string` | `'%kernel.project_dir%/public/uploads/editorjs'` | Local upload directory |
| `local_public_path` | `string` | `'/uploads/editorjs'` | Public URL path prefix |
| `flysystem_storage` | `string` | `null` | Flysystem storage service ID |
| `flysystem_path` | `string` | `'uploads/editorjs'` | Path within the Flysystem filesystem |
| `flysystem_public_url` | `string` | `''` | Public URL prefix for Flysystem files |
| `custom_handler` | `string` | `null` | Service ID of your `UploadHandlerInterface` |
| `max_file_size` | `int` | `5242880` | Maximum file size in bytes (5 MB) |
| `allowed_mime_types` | `array` | `['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']` | Allowed MIME types |

### Upload Response Format

The built-in controller returns the format expected by the Editor.js Image Tool:

```json
{
    "success": 1,
    "file": {
        "url": "/uploads/editorjs/my-image-a1b2c3d4e5f6g7h8.jpg"
    }
}
```

On error:

```json
{
    "success": 0,
    "message": "File type \"text/plain\" is not allowed."
}
```

### Without the Built-in Controller

If you prefer to handle uploads entirely yourself, don't enable the upload config. Create your own controller and pass its URL to the `ImageTool`:

```php
new ImageTool(uploadEndpoint: '/api/my-custom-upload')
```

Your endpoint must return the JSON format shown above.

## Data Format

Editor.js outputs structured JSON data. The value stored in your entity will be a JSON string:

```json
{
  "time": 1234567890,
  "blocks": [
    {
      "type": "header",
      "data": {
        "text": "Hello World",
        "level": 2
      }
    },
    {
      "type": "paragraph",
      "data": {
        "text": "This is a paragraph with <b>bold</b> and <i>italic</i> text."
      }
    },
    {
      "type": "list",
      "data": {
        "style": "unordered",
        "items": ["Item 1", "Item 2", "Item 3"]
      }
    }
  ],
  "version": "2.30.0"
}
```

### Rendering in Twig

To display Editor.js content in your templates, you will need to parse the JSON and render each block. A simple approach:

```twig
{% set content = myEntity.content|json_decode %}
{% if content.blocks is defined %}
    {% for block in content.blocks %}
        {% if block.type == 'header' %}
            <h{{ block.data.level }}>{{ block.data.text|raw }}</h{{ block.data.level }}>
        {% elseif block.type == 'paragraph' %}
            <p>{{ block.data.text|raw }}</p>
        {% elseif block.type == 'list' %}
            {% if block.data.style == 'ordered' %}
                <ol>{% for item in block.data.items %}<li>{{ item|raw }}</li>{% endfor %}</ol>
            {% else %}
                <ul>{% for item in block.data.items %}<li>{{ item|raw }}</li>{% endfor %}</ul>
            {% endif %}
        {% elseif block.type == 'code' %}
            <pre><code>{{ block.data.code }}</code></pre>
        {% elseif block.type == 'quote' %}
            <blockquote>{{ block.data.text|raw }}<cite>{{ block.data.caption|raw }}</cite></blockquote>
        {% elseif block.type == 'delimiter' %}
            <hr/>
        {% endif %}
    {% endfor %}
{% endif %}
```

## Extending the Editor

### Adding Community Tools

You can use any tool from the [Editor.js ecosystem](https://github.com/editor-js/awesome-editorjs) using either a [built-in DTO](#community-tools-built-in-dtos) or the generic `CustomTool` DTO. No JavaScript code required — the bundle dynamically imports the npm package for you.

**Step 1**: Add the npm package to your project.

For **AssetMapper**, add it to `importmap.php`:
```php
return [
    'editorjs-paragraph-with-alignment' => ['version' => '3.0.0'],
];
```

For **Webpack Encore**, install via npm/yarn:
```sh
npm install editorjs-paragraph-with-alignment
```

**Step 2**: Use a built-in DTO or the generic `CustomTool`:

```php
use Makraz\EditorjsBundle\DTO\Tools\CustomTool;

$builder->add('content', EditorjsType::class, [
    'editorjs_tools' => [
        // Generic CustomTool for any community tool
        new CustomTool(
            name: 'paragraph',
            package: 'editorjs-paragraph-with-alignment',
            config: ['defaultAlignment' => 'left'],
        ),
        EditorjsTool::LIST,
        EditorjsTool::CODE,
    ],
]);
```

### Creating Your Own Tool DTO

For tools you use frequently, create a dedicated DTO by extending `AbstractTool`:

```php
use Makraz\EditorjsBundle\DTO\Tools\AbstractTool;

final class MyCustomBlockTool extends AbstractTool
{
    public function __construct(
        private readonly string $someOption = 'default',
    ) {
    }

    public function getName(): string
    {
        return 'myBlock';
    }

    public function getPackage(): ?string
    {
        return 'editorjs-my-block';
    }

    public function getConfig(): array
    {
        return [
            'someOption' => $this->someOption,
        ];
    }
}
```

Then use it like any built-in tool:

```php
$builder->add('content', EditorjsType::class, [
    'editorjs_tools' => [
        new MyCustomBlockTool(someOption: 'value'),
        EditorjsTool::HEADER,
        EditorjsTool::LIST,
    ],
]);
```

### Advanced: JavaScript Event

For full control, you can still register tools manually via the `editorjs:options` event:

```javascript
document.addEventListener('editorjs:options', (event) => {
    const config = event.detail;

    config.tools.myCustomTool = {
        class: MyCustomToolClass,
        config: { /* ... */ },
    };
});
```

## JavaScript Events

The Stimulus controller dispatches events you can listen to for custom behavior:

```javascript
// Fired before the editor initializes — modify config here
document.addEventListener('editorjs:options', (event) => {
    const config = event.detail;
    console.log('Editor config:', config);
});

// Fired when the editor is ready
document.addEventListener('editorjs:connect', (event) => {
    const editorInstance = event.detail;
    console.log('Editor.js is ready!', editorInstance);
});

// Fired on every content change
document.addEventListener('editorjs:change', (event) => {
    const outputData = event.detail;
    console.log('Content changed:', outputData);
});
```

| Event | Detail | Description |
|-------|--------|-------------|
| `editorjs:options` | `EditorConfig` | Dispatched before initialization. Modify the config object to add tools or change settings. |
| `editorjs:connect` | `EditorJS` | Dispatched when the editor is fully initialized and ready. |
| `editorjs:change` | `OutputData` | Dispatched whenever the editor content changes. |

## Symfony Live Component Compatibility

The editor is wrapped in a `data-live-ignore` container, so it works correctly with Symfony Live Components without being destroyed on re-render.

## Requirements

- PHP >= 8.1
- Symfony 6.4, 7.x, or 8.x
- `symfony/stimulus-bundle` >= 2.9.1

## License

MIT
