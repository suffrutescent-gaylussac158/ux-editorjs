import { Controller } from '@hotwired/stimulus';
interface ToolEntry {
    config?: Record<string, any>;
    package?: string;
}
export default class extends Controller {
    readonly inputTarget: HTMLInputElement;
    readonly editorContainerTarget: HTMLDivElement;
    static targets: string[];
    readonly toolsValue: Record<string, ToolEntry>;
    readonly extraOptionsValue: Record<string, any>;
    readonly tunesValue: string[];
    static values: {
        tools: {
            type: ObjectConstructor;
            default: {};
        };
        extraOptions: {
            type: ObjectConstructor;
            default: {};
        };
        tunes: {
            type: ArrayConstructor;
            default: never[];
        };
    };
    private editorInstance;
    connect(): Promise<void>;
    disconnect(): void;
    private resolveTools;
    private buildConfig;
    private toCssValue;
    private applyStyles;
    private syncData;
    private dispatchEvent;
}
export {};
