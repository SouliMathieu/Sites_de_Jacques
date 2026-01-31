// Dropzone simple pour upload de fichiers
class SimpleDropzone {
    constructor(element, options) {
        this.element = typeof element === 'string' ? document.querySelector(element) : element;
        this.options = Object.assign({
            url: '',
            maxFilesize: 20,
            acceptedFiles: '.jpg,.jpeg,.png,.gif,.webp,.mp4,.mov,.avi',
            headers: {},
            dictDefaultMessage: 'Glissez-déposez vos fichiers ici ou cliquez pour sélectionner'
        }, options);

        this.files = [];
        this.init();
    }

    init() {
        this.setupElement();
        this.setupEvents();
        if (this.options.init) this.options.init.call(this);
    }

    setupElement() {
        this.element.innerHTML = `
            <div class="dz-message" style="text-align: center; padding: 50px 20px; border: 2px dashed #ccc; border-radius: 8px; cursor: pointer; background: #fafafa;">
                <div style="font-size: 18px; color: #666; margin-bottom: 10px;">📁</div>
                <div style="font-size: 16px; color: #666;">${this.options.dictDefaultMessage}</div>
                <div style="font-size: 12px; color: #999; margin-top: 10px;">Formats acceptés: ${this.options.acceptedFiles}</div>
            </div>
            <div class="dz-previews" style="margin-top: 20px; display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px;"></div>
            <input type="file" multiple style="display: none;" accept="${this.options.acceptedFiles}">
        `;

        this.messageElement = this.element.querySelector('.dz-message');
        this.previewsElement = this.element.querySelector('.dz-previews');
        this.fileInput = this.element.querySelector('input[type="file"]');
    }

    setupEvents() {
        // Click pour ouvrir file picker
        this.messageElement.addEventListener('click', () => {
            this.fileInput.click();
        });

        // Sélection de fichiers
        this.fileInput.addEventListener('change', (e) => {
            Array.from(e.target.files).forEach(file => this.addFile(file));
        });

        // Drag & Drop
        this.element.addEventListener('dragover', (e) => {
            e.preventDefault();
            this.messageElement.style.background = '#e8f5e8';
            this.messageElement.style.borderColor = '#4CAF50';
        });

        this.element.addEventListener('dragleave', (e) => {
            e.preventDefault();
            this.messageElement.style.background = '#fafafa';
            this.messageElement.style.borderColor = '#ccc';
        });

        this.element.addEventListener('drop', (e) => {
            e.preventDefault();
            this.messageElement.style.background = '#fafafa';
            this.messageElement.style.borderColor = '#ccc';

            Array.from(e.dataTransfer.files).forEach(file => this.addFile(file));
        });
    }

    addFile(file) {
        if (file.size > this.options.maxFilesize * 1024 * 1024) {
            alert(`Fichier trop volumineux: ${file.name} (max ${this.options.maxFilesize}MB)`);
            return;
        }

        this.files.push(file);
        this.createPreview(file);
        this.uploadFile(file);
    }

    createPreview(file) {
        const preview = document.createElement('div');
        preview.className = 'dz-preview';
        preview.style.cssText = 'position: relative; border: 1px solid #ddd; border-radius: 8px; padding: 10px; background: white;';

        const isImage = file.type.startsWith('image/');
        const isVideo = file.type.startsWith('video/');

        preview.innerHTML = `
            <div style="text-align: center;">
                <div style="font-size: 30px; margin-bottom: 5px;">
                    ${isImage ? '🖼️' : isVideo ? '🎥' : '📄'}
                </div>
                <div style="font-size: 12px; color: #666; word-break: break-all;">${file.name}</div>
                <div style="font-size: 10px; color: #999;">${(file.size / 1024).toFixed(1)} KB</div>
                <div class="progress" style="width: 100%; height: 4px; background: #eee; border-radius: 2px; margin: 5px 0; overflow: hidden;">
                    <div class="progress-bar" style="width: 0%; height: 100%; background: #4CAF50; transition: width 0.3s;"></div>
                </div>
                <button type="button" onclick="this.parentElement.parentElement.remove()" style="background: #f44336; color: white; border: none; padding: 2px 8px; border-radius: 3px; font-size: 10px; cursor: pointer;">×</button>
            </div>
        `;

        this.previewsElement.appendChild(preview);
        return preview;
    }

    uploadFile(file) {
        const formData = new FormData();
        formData.append('files', file);

        // Ajouter headers CSRF
        Object.keys(this.options.headers).forEach(key => {
            formData.append(key, this.options.headers[key]);
        });

        const preview = this.previewsElement.lastElementChild;
        const progressBar = preview.querySelector('.progress-bar');

        fetch(this.options.url, {
            method: 'POST',
            body: formData,
            headers: this.options.headers
        })
        .then(response => response.json())
        .then(data => {
            progressBar.style.width = '100%';
            if (this.options.success) {
                this.options.success(file, data);
            }
        })
        .catch(error => {
            progressBar.style.background = '#f44336';
            progressBar.style.width = '100%';
            if (this.options.error) {
                this.options.error(file, error);
            }
            console.error('Upload error:', error);
        });
    }
}

// Compatibilité avec Dropzone API
window.Dropzone = SimpleDropzone;
