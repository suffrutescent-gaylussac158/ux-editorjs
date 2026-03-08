import { Controller } from '@hotwired/stimulus';
import EditorJS, { type OutputData, type EditorConfig, type ToolConstructable } from '@editorjs/editorjs';

const BUILTIN_TOOLS: Record<string, () => Promise<{ default: ToolConstructable }>> = {
    header: () => import('@editorjs/header'),
    list: () => import('@editorjs/list'),
    paragraph: () => import('@editorjs/paragraph'),
    image: () => import('@editorjs/image'),
    code: () => import('@editorjs/code'),
    delimiter: () => import('@editorjs/delimiter'),
    quote: () => import('@editorjs/quote'),
    warning: () => import('@editorjs/warning'),
    table: () => import('@editorjs/table'),
    embed: () => import('@editorjs/embed'),
    marker: () => import('@editorjs/marker'),
    inlineCode: () => import('@editorjs/inline-code'),
    checklist: () => import('@editorjs/checklist'),
    linkTool: () => import('@editorjs/link'),
    raw: () => import('@editorjs/raw'),
    underline: () => import('@editorjs/underline'),
};

interface ToolEntry {
    config?: Record<string, any>;
    package?: string;
    tunes?: string[];
}

export default class extends Controller {
    declare readonly inputTarget: HTMLInputElement;
    declare readonly editorContainerTarget: HTMLDivElement;
    static targets = ['input', 'editorContainer'];

    declare readonly toolsValue: Record<string, ToolEntry>;
    declare readonly extraOptionsValue: Record<string, any>;
    declare readonly tunesValue: string[];
    static values = {
        tools: {
            type: Object,
            default: {},
        },
        extraOptions: {
            type: Object,
            default: {},
        },
        tunes: {
            type: Array,
            default: [],
        },
    };

    private editorInstance: EditorJS | null = null;

    async connect() {
        if (this.editorInstance) {
            return;
        }

        const tools = await this.resolveTools();
        const config = this.buildConfig(tools);

        this.dispatchEvent('options', config);

        const { maxWidth, minHeight: minHeightOpt, border } = this.extraOptionsValue;
        if (maxWidth || typeof minHeightOpt === 'string' || border) {
            this.applyStyles(maxWidth, typeof minHeightOpt === 'string' ? minHeightOpt : null, border);
        }

        this.editorInstance = new EditorJS({
            ...config,
            onReady: () => {
                this.dispatchEvent('connect', this.editorInstance);
            },
            onChange: async () => {
                await this.syncData();
            },
        });
    }

    disconnect() {
        if (this.editorInstance) {
            this.editorInstance.destroy();
            this.editorInstance = null;
        }
    }

    private async resolveTools(): Promise<Record<string, any>> {
        const toolEntries = Object.entries(this.toolsValue);
        const resolved: Record<string, any> = {};

        await Promise.all(
            toolEntries.map(async ([name, entry]) => {
                const config = entry.config ?? {};
                const packageName = entry.package;

                try {
                    let toolClass: ToolConstructable;

                    if (packageName) {
                        // Custom tool: dynamic import from the specified npm package
                        const module = await import(/* webpackIgnore: true */ packageName);
                        toolClass = module.default ?? module[Object.keys(module)[0]];
                    } else if (BUILTIN_TOOLS[name]) {
                        // Built-in tool
                        const module = await BUILTIN_TOOLS[name]();
                        toolClass = module.default;
                    } else {
                        console.warn(`Editor.js tool "${name}" is not recognized. Register it via the "editorjs:options" event or provide a "package" name.`);
                        return;
                    }

                    resolved[name] = {
                        class: toolClass,
                        ...(Object.keys(config).length > 0 ? { config } : {}),
                        ...(entry.tunes ? { tunes: entry.tunes } : {}),
                    };
                } catch (e) {
                    console.warn(`Editor.js tool "${name}" could not be loaded:`, e);
                }
            })
        );

        // Post-resolution: inject EditorJS library and sibling tools for nested editors (e.g. columns)
        for (const [name, tool] of Object.entries(resolved)) {
            if (tool.config?.requireEditorJS) {
                delete tool.config.requireEditorJS;
                tool.config.EditorJsLibrary = EditorJS;
                // Pass all other resolved tools so nested editors can use them
                const { [name]: _, ...nestedTools } = resolved;
                tool.config.tools = nestedTools;
            }
        }

        return resolved;
    }

    private buildConfig(tools: Record<string, any>): EditorConfig {
        const { placeholder, minHeight, readOnly, autofocus, inlineToolbar } = this.extraOptionsValue;

        let initialData: OutputData | undefined;
        const rawValue = this.inputTarget.value;
        if (rawValue) {
            try {
                initialData = JSON.parse(rawValue);
            } catch {
                // Not valid JSON, ignore
            }
        }

        const config: EditorConfig = {
            holder: this.editorContainerTarget,
            tools,
            data: initialData,
            placeholder: placeholder ?? 'Start writing...',
            minHeight: typeof minHeight === 'number' ? minHeight : 200,
            readOnly: readOnly ?? false,
            autofocus: autofocus ?? false,
            inlineToolbar: inlineToolbar ?? true,
        };

        if (this.tunesValue.length > 0) {
            config.tunes = this.tunesValue;
        }

        return config;
    }

    private toCssValue(value: number | string): string {
        return typeof value === 'number' ? `${value}px` : value;
    }

    private applyStyles(maxWidth?: number | string, minHeight?: string | null, border?: boolean | string): void {
        const scopeId = `editorjs-${Math.random().toString(36).slice(2, 9)}`;
        this.editorContainerTarget.setAttribute('data-editorjs-scope', scopeId);
        const scope = `[data-editorjs-scope="${scopeId}"]`;

        let css = '';
        if (maxWidth) {
            const val = this.toCssValue(maxWidth);
            css += `${scope} .ce-toolbar__content, ${scope} .ce-block__content { max-width: ${val}; }\n`;
        }
        if (minHeight) {
            css += `${scope} .ce-redactor { min-height: ${minHeight}; }\n`;
        }
        if (border) {
            const borderValue = typeof border === 'string' ? border : '1px solid #e0e0e0';
            css += `${scope} { border: ${borderValue}; border-radius: 4px; padding: 8px; }\n`;
        }

        if (css) {
            const style = document.createElement('style');
            style.textContent = css;
            this.editorContainerTarget.appendChild(style);
        }
    }

    private async syncData(): Promise<void> {
        if (!this.editorInstance) {
            return;
        }

        try {
            const outputData = await this.editorInstance.save();
            this.inputTarget.value = JSON.stringify(outputData);

            this.inputTarget.dispatchEvent(new Event('input', { bubbles: true }));
            this.inputTarget.dispatchEvent(new Event('change', { bubbles: true }));

            this.dispatchEvent('change', outputData);
        } catch (e) {
            console.error('Editor.js save failed:', e);
        }
    }

    private dispatchEvent(name: string, payload: any = {}) {
        this.dispatch(name, { detail: payload, prefix: 'editorjs' });
    }
}
