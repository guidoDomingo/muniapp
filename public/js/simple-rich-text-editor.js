/**
 * Simple Rich Text Editor para MuniApp
 * Alternativa ligera a TinyMCE sin dependencias externas
 */

class SimpleRichTextEditor {
    constructor(elementId, options = {}) {
        this.element = document.getElementById(elementId);
        this.options = {
            height: options.height || '300px',
            readonly: options.readonly || false,
            placeholder: options.placeholder || 'Escriba aquí...',
            toolbar: options.toolbar !== false,
            ...options
        };
        
        if (!this.element) {
            console.error(`Element with ID ${elementId} not found`);
            return;
        }
        
        this.init();
    }
    
    init() {
        // Crear contenedor principal
        this.container = document.createElement('div');
        this.container.className = 'simple-editor-container';
        
        // Crear toolbar si está habilitado
        if (this.options.toolbar && !this.options.readonly) {
            this.createToolbar();
        }
        
        // Crear área de edición
        this.createEditor();
        
        // Reemplazar el elemento original
        this.element.style.display = 'none';
        this.element.parentNode.insertBefore(this.container, this.element.nextSibling);
        
        // Sincronizar contenido inicial
        this.updateOriginalElement();
    }
    
    createToolbar() {
        this.toolbar = document.createElement('div');
        this.toolbar.className = 'simple-editor-toolbar';
        
        const buttons = [
            { command: 'bold', icon: 'B', title: 'Negrita' },
            { command: 'italic', icon: 'I', title: 'Cursiva' },
            { command: 'underline', icon: 'U', title: 'Subrayado' },
            { type: 'separator' },
            { command: 'insertUnorderedList', icon: '•', title: 'Lista con viñetas' },
            { command: 'insertOrderedList', icon: '1.', title: 'Lista numerada' },
            { type: 'separator' },
            { command: 'justifyLeft', icon: '⇤', title: 'Alinear izquierda' },
            { command: 'justifyCenter', icon: '⇔', title: 'Centrar' },
            { command: 'justifyRight', icon: '⇥', title: 'Alinear derecha' },
            { type: 'separator' },
            { command: 'removeFormat', icon: '✕', title: 'Quitar formato' }
        ];
        
        buttons.forEach(button => {
            if (button.type === 'separator') {
                const separator = document.createElement('span');
                separator.className = 'toolbar-separator';
                separator.textContent = '|';
                this.toolbar.appendChild(separator);
            } else {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'toolbar-button';
                btn.innerHTML = button.icon;
                btn.title = button.title;
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.execCommand(button.command);
                });
                this.toolbar.appendChild(btn);
            }
        });
        
        this.container.appendChild(this.toolbar);
    }
    
    createEditor() {
        this.editor = document.createElement('div');
        this.editor.className = 'simple-editor-content';
        this.editor.style.height = this.options.height;
        this.editor.contentEditable = !this.options.readonly;
        
        if (this.options.placeholder && !this.options.readonly) {
            this.editor.setAttribute('data-placeholder', this.options.placeholder);
        }
        
        // Establecer contenido inicial
        this.editor.innerHTML = this.element.value || '';
        
        // Event listeners
        this.editor.addEventListener('input', () => {
            this.updateOriginalElement();
        });
        
        this.editor.addEventListener('paste', (e) => {
            // Limpiar formato al pegar
            e.preventDefault();
            const text = e.clipboardData.getData('text/plain');
            document.execCommand('insertText', false, text);
        });
        
        this.container.appendChild(this.editor);
    }
    
    execCommand(command, value = null) {
        this.editor.focus();
        document.execCommand(command, false, value);
        this.updateOriginalElement();
    }
    
    updateOriginalElement() {
        this.element.value = this.editor.innerHTML;
        // Disparar evento change para formularios
        this.element.dispatchEvent(new Event('change', { bubbles: true }));
    }
    
    getContent() {
        return this.editor.innerHTML;
    }
    
    setContent(html) {
        this.editor.innerHTML = html;
        this.updateOriginalElement();
    }
    
    destroy() {
        if (this.container && this.container.parentNode) {
            this.container.parentNode.removeChild(this.container);
        }
        this.element.style.display = '';
    }
}

// CSS para el editor
const editorCSS = `
.simple-editor-container {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.simple-editor-toolbar {
    padding: 8px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    gap: 4px;
    align-items: center;
    flex-wrap: wrap;
}

.toolbar-button {
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 6px 8px;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toolbar-button:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.toolbar-button:active {
    background: #e5e7eb;
}

.toolbar-separator {
    color: #d1d5db;
    margin: 0 4px;
    font-size: 14px;
}

.simple-editor-content {
    padding: 12px;
    min-height: 200px;
    outline: none;
    line-height: 1.6;
    font-size: 14px;
    overflow-y: auto;
}

.simple-editor-content:empty:before {
    content: attr(data-placeholder);
    color: #9ca3af;
    font-style: italic;
}

.simple-editor-content:focus {
    box-shadow: inset 0 0 0 1px #3b82f6;
}

.simple-editor-content[contenteditable="false"] {
    background: #f9fafb;
    color: #6b7280;
    cursor: not-allowed;
}

.simple-editor-content p {
    margin: 0 0 1em 0;
}

.simple-editor-content p:last-child {
    margin-bottom: 0;
}

.simple-editor-content ul, .simple-editor-content ol {
    padding-left: 20px;
    margin: 0 0 1em 0;
}

.simple-editor-content li {
    margin-bottom: 0.25em;
}

.simple-editor-content strong {
    font-weight: bold;
}

.simple-editor-content em {
    font-style: italic;
}

.simple-editor-content u {
    text-decoration: underline;
}
`;

// Insertar CSS si no existe
if (!document.getElementById('simple-editor-styles')) {
    const style = document.createElement('style');
    style.id = 'simple-editor-styles';
    style.textContent = editorCSS;
    document.head.appendChild(style);
}

// Exponer globalmente
window.SimpleRichTextEditor = SimpleRichTextEditor;