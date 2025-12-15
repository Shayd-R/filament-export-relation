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
                // Trigger change event for Livewire
                checkbox.dispatchEvent(new Event('change', { bubbles: true }));
            });
        },

        // Enhanced Column Selector Functions
        selectAllMain: function() {
            const checkboxes = document.querySelectorAll('[data-column-group="main"] input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (!checkbox.checked) {
                    checkbox.checked = true;
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        },

        selectAllRelation: function(relationKey) {
            const checkboxes = document.querySelectorAll(`[data-column-group="relation-${relationKey}"] input[type="checkbox"]`);
            checkboxes.forEach(checkbox => {
                if (!checkbox.checked) {
                    checkbox.checked = true;
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        },

        // Search filtering for columns
        filterColumns: function(searchQuery, containerId) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const items = container.querySelectorAll('[data-column-item]');
            const query = searchQuery.toLowerCase();

            items.forEach(item => {
                const label = item.getAttribute('data-column-label')?.toLowerCase() || '';
                if (query === '' || label.includes(query)) {
                    item.style.display = '';
                    item.classList.remove('hidden');
                } else {
                    item.style.display = 'none';
                    item.classList.add('hidden');
                }
            });
        },

        // Toggle HasMany section
        toggleHasManySection: function(buttonElement) {
            const contentElement = buttonElement.nextElementSibling;
            const chevron = buttonElement.querySelector('.chevron-icon');
            
            if (contentElement.style.display === 'none' || contentElement.style.display === '') {
                contentElement.style.display = 'block';
                chevron?.classList.add('rotated');
            } else {
                contentElement.style.display = 'none';
                chevron?.classList.remove('rotated');
            }
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

        // Highlight BelongsTo fields within HasMany
        highlightBelongsToInHasMany: function() {
            const belongsToContainers = document.querySelectorAll('[data-belongs-to-hasmany]');
            belongsToContainers.forEach(container => {
                container.classList.add('belongs-to-hasmany-container');
                const select = container.querySelector('select');
                if (select) {
                    select.classList.add('belongs-to-hasmany-select');
                }
            });
        },

        // Initialize enhanced features
        initEnhanced: function() {
            this.highlightBelongsToInHasMany();
            
            // Add data attributes for easier targeting
            document.querySelectorAll('[wire\\:model*="selectedColumns.main"]').forEach(el => {
                el.closest('.space-y-2')?.setAttribute('data-column-group', 'main');
            });

            // Smooth scroll for long lists
            const columnGrids = document.querySelectorAll('.grid.grid-cols-2');
            columnGrids.forEach(grid => {
                grid.classList.add('column-grid-container');
            });
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
