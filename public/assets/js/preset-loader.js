/**
 * Form Preset Loader
 * 
 * Dynamically loads and displays form sections based on selected program preset
 * Usage: Include this in applicant form views and call loadPresetForProgram(programId)
 */

class PresetFormLoader {
    constructor(options = {}) {
        this.programSelectId = options.programSelectId || '#program_id';
        this.formContainerId = options.formContainerId || '#presetFormContainer';
        this.loadingClass = options.loadingClass || 'loading';
        this.apiEndpoint = options.apiEndpoint || '/api/presets/program';
        
        this.init();
    }

    /**
     * Initialize the preset loader
     */
    init() {
        const programSelect = document.querySelector(this.programSelectId);
        
        if (programSelect) {
            programSelect.addEventListener('change', (e) => {
                const programId = e.target.value;
                if (programId) {
                    this.loadPresetForProgram(programId);
                }
            });
        }
    }

    /**
     * Load preset for selected program
     */
    async loadPresetForProgram(programId) {
        const formContainer = document.querySelector(this.formContainerId);
        
        if (!formContainer) {
            console.error('Form container not found:', this.formContainerId);
            return;
        }

        // Show loading state
        formContainer.classList.add(this.loadingClass);
        formContainer.innerHTML = '<div class="alert alert-info">Loading form sections...</div>';

        try {
            const response = await fetch(`${this.apiEndpoint}/${programId}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.pages) {
                this.renderFormSections(data.pages);
            } else {
                formContainer.innerHTML = '<div class="alert alert-warning">No form sections available for this program.</div>';
            }
        } catch (error) {
            console.error('Error loading preset:', error);
            formContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    Error loading form sections. Please try again.
                </div>
            `;
        } finally {
            formContainer.classList.remove(this.loadingClass);
        }
    }

    /**
     * Render form sections to container
     */
    renderFormSections(pages) {
        const formContainer = document.querySelector(this.formContainerId);
        
        if (pages.length === 0) {
            formContainer.innerHTML = '<div class="alert alert-info">No form sections available.</div>';
            return;
        }

        let html = '<div class="row">';

        pages.forEach((page, index) => {
            html += `
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <span class="badge bg-secondary me-2">${index + 1}</span>
                                ${this.escapeHtml(page.title)}
                            </h5>
                        </div>
                        <div class="card-body">
                            ${page.description ? `<p class="text-muted mb-3">${this.escapeHtml(page.description)}</p>` : ''}
                            <div id="section-${page.id}" class="section-content">
                                <!-- Form fields will be loaded here -->
                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                        onclick="loadSectionFields(${page.id})">
                                    <i class="fas fa-spinner fa-spin"></i> Load Fields
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        formContainer.innerHTML = html;

        // Auto-load fields for first section
        if (pages.length > 0) {
            window.loadSectionFields(pages[0].id);
        }
    }

    /**
     * Escape HTML special characters
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

/**
 * Load form fields for a specific section
 * This function should be defined in your form field loader script
 */
window.loadSectionFields = async function(sectionId) {
    console.log('Loading fields for section:', sectionId);
    // This will be implemented based on your form field loading mechanism
};

/**
 * Initialize preset loader on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize if element with data-preset-loader attribute exists
    const presetLoaders = document.querySelectorAll('[data-preset-loader]');
    
    presetLoaders.forEach(element => {
        const options = {
            programSelectId: element.dataset.programSelect || '#program_id',
            formContainerId: element.dataset.formContainer || '#presetFormContainer',
            apiEndpoint: element.dataset.apiEndpoint || '/api/presets/program',
        };
        
        new PresetFormLoader(options);
    });
});

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PresetFormLoader;
}
