// Filament Smart Export JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Smart Export functionality
    window.FilamentSmartExport = {
        // Toggle all columns in a group
        toggleGroup: function(groupId) {
            const checkboxes = document.querySelectorAll(`[data-group="${groupId}"]`);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
        },

        // Update preview table
        updatePreview: function(data) {
            const previewContainer = document.getElementById('smart-export-preview');
            if (!previewContainer) return;

            let html = '<table class="smart-export-preview-table">';
            
            // Headers
            html += '<thead><tr>';
            Object.keys(data[0] || {}).forEach(key => {
                html += `<th>${key}</th>`;
            });
            html += '</tr></thead>';
            
            // Body
            html += '<tbody>';
            data.forEach(row => {
                html += '<tr>';
                Object.values(row).forEach(value => {
                    html += `<td>${value || '-'}</td>`;
                });
                html += '</tr>';
            });
            html += '</tbody></table>';
            
            previewContainer.innerHTML = html;
        },

        // Format file size
        formatFileSize: function(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },

        // Show loading state
        showLoading: function() {
            const loadingHtml = `
                <div class="smart-export-loading">
                    <svg class="smart-export-loading-spinner" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke-width="4" stroke-opacity="0.25"/>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                    <span>Generando exportación...</span>
                </div>
            `;
            
            const container = document.getElementById('smart-export-status');
            if (container) {
                container.innerHTML = loadingHtml;
            }
        },

        // Hide loading state
        hideLoading: function() {
            const container = document.getElementById('smart-export-status');
            if (container) {
                container.innerHTML = '';
            }
        },

        // Validate form
        validateForm: function() {
            const selectedColumns = document.querySelectorAll('input[name*="columns"]:checked');
            
            if (selectedColumns.length === 0) {
                alert('Por favor selecciona al menos una columna para exportar');
                return false;
            }
            
            return true;
        },

        // Auto-select recommended columns
        selectRecommended: function() {
            const recommendedColumns = ['id', 'name', 'email', 'created_at'];
            
            recommendedColumns.forEach(column => {
                const checkbox = document.querySelector(`input[value="${column}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }
    };

    // Auto-initialize
    console.log('Filament Smart Export initialized');
});

// Export progress tracking
function trackExportProgress(exportId) {
    const checkProgress = setInterval(async () => {
        try {
            const response = await fetch(`/api/export-progress/${exportId}`);
            const data = await response.json();
            
            if (data.status === 'completed') {
                clearInterval(checkProgress);
                window.FilamentSmartExport.hideLoading();
                window.location.href = data.download_url;
            } else if (data.status === 'failed') {
                clearInterval(checkProgress);
                window.FilamentSmartExport.hideLoading();
                alert('Error al generar la exportación');
            }
        } catch (error) {
            console.error('Error checking export progress:', error);
        }
    }, 1000);
}
