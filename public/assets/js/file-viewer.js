/**
 * Visor universal de archivos con popup
 */

class FileViewer {
    constructor() {
        this.modal = null;
        this.createModal();
    }

    createModal() {
        // Crear el modal si no existe
        if (document.getElementById('file-viewer-modal')) {
            this.modal = document.getElementById('file-viewer-modal');
            return;
        }

        const modalHTML = `
            <div id="file-viewer-modal" class="file-viewer-modal" style="display: none;">
                <div class="file-viewer-overlay" onclick="fileViewer.close()"></div>
                <div class="file-viewer-container">
                    <div class="file-viewer-header">
                        <div class="file-info">
                            <h3 id="file-viewer-title">Cargando...</h3>
                            <div id="file-viewer-meta" class="file-meta"></div>
                        </div>
                        <div class="file-viewer-actions">
                            <button id="file-viewer-download" class="btn-action" title="Descargar">
                                <i class="fas fa-download"></i>
                            </button>
                            <button onclick="fileViewer.close()" class="btn-action btn-close" title="Cerrar">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="file-viewer-content">
                        <div id="file-viewer-loading" class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Cargando archivo...</span>
                        </div>
                        <div id="file-viewer-body"></div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('file-viewer-modal');
        
        // Agregar estilos CSS
        this.addStyles();
        
        // Cerrar con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.style.display !== 'none') {
                this.close();
            }
        });
    }

    addStyles() {
        if (document.getElementById('file-viewer-styles')) return;

        const styles = `
            <style id="file-viewer-styles">
                .file-viewer-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 10000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .file-viewer-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.8);
                    backdrop-filter: blur(4px);
                }

                .file-viewer-container {
                    position: relative;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
                    max-width: 90vw;
                    max-height: 90vh;
                    width: 800px;
                    height: 600px;
                    display: flex;
                    flex-direction: column;
                    overflow: hidden;
                }

                .file-viewer-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 20px;
                    border-bottom: 1px solid #e5e7eb;
                    background: #f9fafb;
                }

                .file-info h3 {
                    margin: 0 0 5px 0;
                    font-size: 1.2rem;
                    color: #1f2937;
                    font-weight: 600;
                }

                .file-meta {
                    font-size: 0.9rem;
                    color: #6b7280;
                }

                .file-viewer-actions {
                    display: flex;
                    gap: 10px;
                }

                .btn-action {
                    width: 40px;
                    height: 40px;
                    border: none;
                    border-radius: 8px;
                    background: #f3f4f6;
                    color: #374151;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.2s ease;
                }

                .btn-action:hover {
                    background: #e5e7eb;
                    transform: scale(1.05);
                }

                .btn-close:hover {
                    background: #fee2e2;
                    color: #dc2626;
                }

                .file-viewer-content {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: relative;
                    overflow: hidden;
                }

                .loading-spinner {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 15px;
                    color: #6b7280;
                }

                .loading-spinner i {
                    font-size: 2rem;
                }

                #file-viewer-body {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .file-viewer-image {
                    max-width: 100%;
                    max-height: 100%;
                    object-fit: contain;
                    border-radius: 8px;
                }

                .file-viewer-pdf {
                    width: 100%;
                    height: 100%;
                    border: none;
                }

                .file-viewer-text {
                    width: 100%;
                    height: 100%;
                    padding: 20px;
                    font-family: 'Courier New', monospace;
                    font-size: 14px;
                    line-height: 1.5;
                    background: #f8f9fa;
                    border: none;
                    resize: none;
                    overflow: auto;
                }

                .file-viewer-unsupported {
                    text-align: center;
                    padding: 40px;
                    color: #6b7280;
                }

                .file-viewer-unsupported i {
                    font-size: 4rem;
                    margin-bottom: 20px;
                    color: #d1d5db;
                }

                .download-btn {
                    margin-top: 20px;
                    padding: 12px 24px;
                    background: #1e3a8a;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: 600;
                    transition: all 0.2s ease;
                }

                .download-btn:hover {
                    background: #1e40af;
                    transform: translateY(-1px);
                }

                @media (max-width: 768px) {
                    .file-viewer-container {
                        width: 95vw;
                        height: 95vh;
                        margin: 10px;
                    }
                    
                    .file-viewer-header {
                        padding: 15px;
                    }
                    
                    .file-info h3 {
                        font-size: 1rem;
                    }
                }
            </style>
        `;

        document.head.insertAdjacentHTML('beforeend', styles);
    }

    async open(attachmentId) {
        try {
            this.modal.style.display = 'flex';
            this.showLoading();

            // Obtener información del archivo
            const response = await fetch(`file-viewer.php?id=${attachmentId}&action=info`);
            const fileInfo = await response.json();

            this.updateHeader(fileInfo);
            this.setupDownload(attachmentId, fileInfo.name);

            // Mostrar el archivo según su tipo
            if (fileInfo.is_image) {
                this.showImage(attachmentId, fileInfo.name);
            } else if (fileInfo.is_pdf) {
                this.showPdf(attachmentId, fileInfo.name);
            } else if (fileInfo.is_text && fileInfo.size < 1024 * 1024) {
                this.showText(attachmentId);
            } else {
                this.showUnsupported(fileInfo);
            }

        } catch (error) {
            console.error('Error al abrir archivo:', error);
            this.showError('Error al cargar el archivo');
        }
    }

    showLoading() {
        document.getElementById('file-viewer-loading').style.display = 'flex';
        document.getElementById('file-viewer-body').innerHTML = '';
    }

    hideLoading() {
        document.getElementById('file-viewer-loading').style.display = 'none';
    }

    updateHeader(fileInfo) {
        document.getElementById('file-viewer-title').textContent = fileInfo.name;
        
        const sizeFormatted = this.formatFileSize(fileInfo.size);
        const uploadDate = new Date(fileInfo.uploaded_at).toLocaleDateString();
        
        document.getElementById('file-viewer-meta').innerHTML = `
            <div>${sizeFormatted} • ${fileInfo.type}</div>
            <div>Subido por ${fileInfo.uploaded_by} el ${uploadDate}</div>
            <div>Subtarea: ${fileInfo.subtask}</div>
        `;
    }

    setupDownload(attachmentId, filename) {
        const downloadBtn = document.getElementById('file-viewer-download');
        downloadBtn.onclick = () => {
            const link = document.createElement('a');
            link.href = `file-viewer.php?id=${attachmentId}&action=download`;
            link.download = filename;
            link.click();
        };
    }

    showImage(attachmentId, filename) {
        this.hideLoading();
        document.getElementById('file-viewer-body').innerHTML = `
            <img src="file-viewer.php?id=${attachmentId}" 
                 alt="${filename}" 
                 class="file-viewer-image"
                 onload="this.style.opacity=1"
                 style="opacity:0;transition:opacity 0.3s ease">
        `;
    }

    showPdf(attachmentId, filename) {
        this.hideLoading();
        document.getElementById('file-viewer-body').innerHTML = `
            <iframe src="file-viewer.php?id=${attachmentId}" 
                    class="file-viewer-pdf"
                    title="${filename}">
            </iframe>
        `;
    }

    async showText(attachmentId) {
        try {
            const response = await fetch(`file-viewer.php?id=${attachmentId}`);
            const text = await response.text();
            
            this.hideLoading();
            document.getElementById('file-viewer-body').innerHTML = `
                <textarea class="file-viewer-text" readonly>${text}</textarea>
            `;
        } catch (error) {
            this.showError('Error al cargar el archivo de texto');
        }
    }

    showUnsupported(fileInfo) {
        this.hideLoading();
        document.getElementById('file-viewer-body').innerHTML = `
            <div class="file-viewer-unsupported">
                <i class="fas fa-file"></i>
                <h3>Vista previa no disponible</h3>
                <p>Este tipo de archivo no se puede visualizar en el navegador.</p>
                <p><strong>${fileInfo.name}</strong> (${this.formatFileSize(fileInfo.size)})</p>
                <button class="download-btn" onclick="document.getElementById('file-viewer-download').click()">
                    <i class="fas fa-download"></i> Descargar archivo
                </button>
            </div>
        `;
    }

    showError(message) {
        this.hideLoading();
        document.getElementById('file-viewer-body').innerHTML = `
            <div class="file-viewer-unsupported">
                <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                <h3>Error</h3>
                <p>${message}</p>
            </div>
        `;
    }

    close() {
        this.modal.style.display = 'none';
        document.getElementById('file-viewer-body').innerHTML = '';
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// Inicializar el visor global
window.fileViewer = new FileViewer();

// Función global para abrir archivos
window.openFileViewer = function(attachmentId) {
    window.fileViewer.open(attachmentId);
};
