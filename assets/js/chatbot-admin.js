/**
 * ============================================================================
 * CHATBOT ADMIN JAVASCRIPT
 * ============================================================================
 */

// Tab switching
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
    
    // Load data if needed
    if (tabName === 'knowledge') {
        loadKnowledge();
    } else if (tabName === 'settings') {
        loadSettings();
    }
}

// View conversation details
async function viewConversation(conversationId) {
    try {
        const response = await fetch('ajax-handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=get_conversation&conversation_id=${conversationId}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayConversation(data.conversation, data.messages);
            document.getElementById('conversationModal').style.display = 'flex';
        } else {
            alert('Error loading conversation');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error loading conversation');
    }
}

function displayConversation(conversation, messages) {
    let html = `
        <div class="conversation-info">
            <p><strong>Session:</strong> ${conversation.session_id}</p>
            ${conversation.visitor_name ? `<p><strong>Name:</strong> ${conversation.visitor_name}</p>` : ''}
            ${conversation.visitor_email ? `<p><strong>Email:</strong> ${conversation.visitor_email}</p>` : ''}
            ${conversation.visitor_phone ? `<p><strong>Phone:</strong> ${conversation.visitor_phone}</p>` : ''}
            <p><strong>Status:</strong> ${conversation.status}</p>
            <p><strong>Started:</strong> ${new Date(conversation.started_at).toLocaleString()}</p>
        </div>
        <hr>
        <div class="conversation-messages">
    `;
    
    messages.forEach(msg => {
        const senderClass = msg.sender === 'user' ? 'user' : 'bot';
        const senderLabel = msg.sender === 'user' ? '👤 Visitor' : '🌱 Bot';
        
        html += `
            <div class="conv-message ${senderClass}">
                <div class="conv-sender">${senderLabel}</div>
                <div class="conv-text">${msg.message.replace(/\n/g, '<br>')}</div>
                <div class="conv-time">${new Date(msg.created_at).toLocaleString()}</div>
            </div>
        `;
    });
    
    html += '</div>';
    
    document.getElementById('conversationDetails').innerHTML = html;
}

function closeConversationModal() {
    document.getElementById('conversationModal').style.display = 'none';
}

// View knowledge details
async function viewKnowledge(id) {
    try {
        const response = await fetch('ajax-handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=get_knowledge_item&id=${id}`
        });
        
        const data = await response.json();
        
        if (data.success && data.item) {
            const item = data.item;
            const content = `
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                    <strong>ID:</strong> ${item.id} | 
                    <strong>Category:</strong> <span class="badge badge-info">${item.category}</span> | 
                    <strong>Priority:</strong> ${item.priority} | 
                    <strong>Status:</strong> <span class="badge badge-${item.is_active ? 'success' : 'secondary'}">${item.is_active ? 'Active' : 'Inactive'}</span>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 0.5rem; color: #1A3E7F;">Question:</h3>
                    <div style="padding: 1rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;">
                        ${item.question}
                    </div>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 0.5rem; color: #1A3E7F;">Answer:</h3>
                    <div style="padding: 1rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; white-space: pre-wrap;">
                        ${item.answer}
                    </div>
                </div>
                
                ${item.keywords ? `
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 0.5rem; color: #1A3E7F;">Keywords:</h3>
                    <div style="padding: 1rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;">
                        ${item.keywords.split(',').map(k => `<span class="badge" style="margin: 2px;">${k.trim()}</span>`).join('')}
                    </div>
                </div>
                ` : ''}
                
                <div style="text-align: right; margin-top: 1.5rem;">
                    <button onclick="closeViewKnowledgeModal(); editKnowledge(${item.id});" class="btn btn-primary">
                        ✏️ Edit
                    </button>
                    <button onclick="closeViewKnowledgeModal();" class="btn btn-secondary">
                        Close
                    </button>
                </div>
            `;
            
            document.getElementById('viewKnowledgeContent').innerHTML = content;
            document.getElementById('viewKnowledgeModal').style.display = 'flex';
        } else {
            alert('Error loading knowledge details');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error loading knowledge details');
    }
}

function closeViewKnowledgeModal() {
    document.getElementById('viewKnowledgeModal').style.display = 'none';
}

// Load knowledge base
async function loadKnowledge() {
    try {
        const response = await fetch('ajax-handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=get_knowledge'
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayKnowledge(data.knowledge);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function displayKnowledge(knowledge) {
    let html = '<div class="table-responsive"><table class="data-table">';
    html += '<thead><tr><th>Question</th><th>Category</th><th>Priority</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
    
    if (knowledge.length === 0) {
        html += '<tr><td colspan="5" style="text-align: center;">No knowledge items yet</td></tr>';
    } else {
        knowledge.forEach(item => {
            html += `
                <tr>
                    <td>${item.question}</td>
                    <td><span class="badge">${item.category}</span></td>
                    <td>${item.priority}</td>
                    <td><span class="badge badge-${item.is_active ? 'success' : 'secondary'}">${item.is_active ? 'Active' : 'Inactive'}</span></td>
                    <td>
                        <button onclick="editKnowledge(${item.id})" class="btn btn-sm btn-primary">Edit</button>
                        <button onclick="deleteKnowledge(${item.id})" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
            `;
        });
    }
    
    html += '</tbody></table></div>';
    document.getElementById('knowledgeList').innerHTML = html;
}

// Knowledge modals
function showAddKnowledgeModal() {
    document.getElementById('knowledgeModalTitle').textContent = 'Add Knowledge';
    document.getElementById('knowledgeForm').reset();
    document.getElementById('knowledge_id').value = '';
    document.getElementById('knowledgeModal').style.display = 'flex';
}

async function editKnowledge(id) {
    try {
        const response = await fetch('ajax-handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=get_knowledge_item&id=${id}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            const item = data.item;
            document.getElementById('knowledgeModalTitle').textContent = 'Edit Knowledge';
            document.getElementById('knowledge_id').value = item.id;
            document.getElementById('knowledge_question').value = item.question;
            document.getElementById('knowledge_answer').value = item.answer;
            document.getElementById('knowledge_category').value = item.category;
            document.getElementById('knowledge_keywords').value = item.keywords || '';
            document.getElementById('knowledge_priority').value = item.priority;
            document.getElementById('knowledge_active').checked = item.is_active == 1;
            document.getElementById('knowledgeModal').style.display = 'flex';
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function deleteKnowledge(id) {
    if (!confirm('Are you sure you want to delete this knowledge item? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch('ajax-handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete_knowledge&id=${id}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Knowledge item deleted successfully!');
            // Reload the page to refresh the list
            window.location.reload();
        } else {
            alert('Error deleting knowledge item: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting knowledge item');
    }
}

function closeKnowledgeModal() {
    document.getElementById('knowledgeModal').style.display = 'none';
}

// Handle knowledge form submission
document.addEventListener('DOMContentLoaded', function() {
    const knowledgeForm = document.getElementById('knowledgeForm');
    if (knowledgeForm) {
        knowledgeForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'save_knowledge');
            
            try {
                const response = await fetch('ajax-handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    closeKnowledgeModal();
                    alert('Knowledge saved successfully!');
                    // Reload the page to show updated list
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error saving knowledge');
            }
        });
    }
});

// Load settings
async function loadSettings() {
    try {
        const response = await fetch('ajax-handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=get_chatbot_settings'
        });
        
        const data = await response.json();
        
        if (data.success) {
            const settings = data.settings;
            document.querySelector('[name="chatbot_enabled"]').value = settings.chatbot_enabled || '1';
            document.querySelector('[name="chatbot_name"]').value = settings.chatbot_name || 'Synergex Assistant';
            document.querySelector('[name="chatbot_greeting"]').value = settings.chatbot_greeting || '';
            document.querySelector('[name="chatbot_color"]').value = settings.chatbot_color || '#27ae60';
            document.querySelector('[name="chatbot_position"]').value = settings.chatbot_position || 'bottom-right';
            document.querySelector('[name="offline_message"]').value = settings.offline_message || '';
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Handle settings form submission
document.addEventListener('DOMContentLoaded', function() {
    const settingsForm = document.getElementById('chatbotSettingsForm');
    if (settingsForm) {
        settingsForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'save_chatbot_settings');
            
            try {
                const response = await fetch('ajax-handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Settings saved successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error saving settings');
            }
        });
    }
});

// Close modals when clicking outside
window.onclick = function(event) {
    const conversationModal = document.getElementById('conversationModal');
    const knowledgeModal = document.getElementById('knowledgeModal');
    const viewKnowledgeModal = document.getElementById('viewKnowledgeModal');
    
    if (event.target === conversationModal) {
        conversationModal.style.display = 'none';
    }
    if (event.target === knowledgeModal) {
        knowledgeModal.style.display = 'none';
    }
    if (event.target === viewKnowledgeModal) {
        viewKnowledgeModal.style.display = 'none';
    }
}
