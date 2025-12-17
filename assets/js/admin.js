// ============================================================================
// FILE: assets/js/admin.js - Admin Panel JavaScript
// ============================================================================

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Confirm before deleting
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
                return false;
            }
        });
    });
    
    // Auto-save warning
    let hasUnsavedChanges = false;
    
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                hasUnsavedChanges = true;
            });
        });
        
        form.addEventListener('submit', function() {
            hasUnsavedChanges = false;
        });
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
    
    // Table search/filter
    const searchInputs = document.querySelectorAll('[data-table-search]');
    searchInputs.forEach(input => {
        const tableId = input.dataset.tableSearch;
        const table = document.querySelector(`#${tableId}`);
        
        if (table) {
            input.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }
    });
    
    // Character counter
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        const counter = document.createElement('div');
        counter.className = 'char-counter';
        counter.style.cssText = 'text-align: right; font-size: 0.85rem; color: #666; margin-top: 0.25rem;';
        
        const updateCounter = () => {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${remaining} characters remaining`;
            counter.style.color = remaining < 50 ? '#ff6600' : '#666';
        };
        
        textarea.parentNode.insertBefore(counter, textarea.nextSibling);
        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });
    
    // Image preview
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = input.parentNode.querySelector('.image-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'image-preview';
                        preview.style.cssText = 'margin-top: 1rem;';
                        input.parentNode.appendChild(preview);
                    }
                    preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; max-height: 200px; border-radius: 5px;">`;
                };
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Sortable tables
    const sortableTables = document.querySelectorAll('table[data-sortable]');
    sortableTables.forEach(table => {
        const headers = table.querySelectorAll('th[data-sort]');
        
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.innerHTML += ' ↕';
            
            header.addEventListener('click', function() {
                const column = this.dataset.sort;
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                
                const currentOrder = this.dataset.order || 'asc';
                const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
                this.dataset.order = newOrder;
                
                rows.sort((a, b) => {
                    const aValue = a.children[this.cellIndex].textContent;
                    const bValue = b.children[this.cellIndex].textContent;
                    
                    if (newOrder === 'asc') {
                        return aValue.localeCompare(bValue, undefined, {numeric: true});
                    } else {
                        return bValue.localeCompare(aValue, undefined, {numeric: true});
                    }
                });
                
                rows.forEach(row => tbody.appendChild(row));
            });
        });
    });
    
    // Copy to clipboard
    const copyButtons = document.querySelectorAll('[data-copy]');
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const text = this.dataset.copy;
            navigator.clipboard.writeText(text).then(() => {
                const originalText = this.textContent;
                this.textContent = 'Copied!';
                setTimeout(() => {
                    this.textContent = originalText;
                }, 2000);
            });
        });
    });
    
    // Bulk actions
    const selectAllCheckbox = document.querySelector('#select-all');
    if (selectAllCheckbox) {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Real-time validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.style.borderColor = '#ff0000';
                let error = this.parentNode.querySelector('.validation-error');
                if (!error) {
                    error = document.createElement('div');
                    error.className = 'validation-error';
                    error.style.color = '#ff0000';
                    error.style.fontSize = '0.85rem';
                    error.style.marginTop = '0.25rem';
                    error.textContent = 'Please enter a valid email address';
                    this.parentNode.appendChild(error);
                }
            } else {
                this.style.borderColor = '';
                const error = this.parentNode.querySelector('.validation-error');
                if (error) error.remove();
            }
        });
    });
    
    // Toast notifications
    window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${type === 'success' ? '#4CAF50' : '#f44336'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 10001;
            animation: slideIn 0.3s ease-out;
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };
    
    // Loading indicator
    window.showLoading = function() {
        const loading = document.createElement('div');
        loading.id = 'loading-indicator';
        loading.innerHTML = '<div class="spinner"></div>';
        loading.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10002;
        `;
        
        const spinner = loading.querySelector('.spinner');
        spinner.style.cssText = `
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #FF6600;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        `;
        
        document.body.appendChild(loading);
    };
    
    window.hideLoading = function() {
        const loading = document.getElementById('loading-indicator');
        if (loading) loading.remove();
    };
    
    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(400px); opacity: 0; }
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
});

// Utility functions
function formatCurrency(amount) {
    return 'K' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}