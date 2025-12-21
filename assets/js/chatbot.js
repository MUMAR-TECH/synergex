/**
 * ============================================================================
 * CHATBOT WIDGET JAVASCRIPT
 * ============================================================================
 * AI Chatbot for Synergex Solutions
 */

class SynergexChatbot {
    constructor() {
        this.sessionId = null;
        this.conversationId = null;
        this.isOpen = false;
        this.apiUrl = window.CHATBOT_CONFIG?.apiUrl || '/synergex/api/chatbot.php';
        
        this.init();
    }
    
    /**
     * Initialize chatbot
     */
    async init() {
        // Create chatbot HTML
        this.createChatbotHTML();
        
        // Attach event listeners
        this.attachEventListeners();
        
        // Initialize session
        await this.initSession();
        
        // Show welcome message after a delay
        setTimeout(() => {
            this.showNotification();
        }, 3000);
    }
    
    /**
     * Create chatbot HTML structure
     */
    createChatbotHTML() {
        const html = `
            <div class="chatbot-widget" id="chatbotWidget">
                <!-- Toggle Button -->
                <button class="chatbot-toggle" id="chatbotToggle" aria-label="Toggle chatbot">
                    <svg class="chat-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                    </svg>
                    <svg class="close-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                    <span class="chatbot-notification" id="chatbotNotification" style="display: none;">1</span>
                </button>
                
                <!-- Chatbot Window -->
                <div class="chatbot-window" id="chatbotWindow">
                    <!-- Header -->
                    <div class="chatbot-header">
                        <div class="chatbot-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="#27ae60">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                            </svg>
                        </div>
                        <div class="chatbot-header-info">
                            <h3>Synergex Assistant</h3>
                            <p>Typically replies instantly</p>
                        </div>
                    </div>
                    
                    <!-- Messages Area -->
                    <div class="chatbot-messages" id="chatbotMessages">
                        <!-- Messages will be added here -->
                    </div>
                    
                    <!-- Input Area -->
                    <div class="chatbot-input">
                        <input 
                            type="text" 
                            id="chatbotInput" 
                            placeholder="Type your message..."
                            autocomplete="off"
                            aria-label="Type your message"
                        />
                        <button class="chatbot-send-btn" id="chatbotSend" aria-label="Send message">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Footer -->
                    <div class="chatbot-footer">
                        Powered by Synergex AI
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', html);
    }
    
    /**
     * Attach event listeners
     */
    attachEventListeners() {
        const toggle = document.getElementById('chatbotToggle');
        const sendBtn = document.getElementById('chatbotSend');
        const input = document.getElementById('chatbotInput');
        
        toggle.addEventListener('click', () => this.toggleChat());
        sendBtn.addEventListener('click', () => this.sendMessage());
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });
    }
    
    /**
     * Initialize chatbot session
     */
    async initSession() {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'init' })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.sessionId = data.session_id;
                this.conversationId = data.conversation_id;
                
                // Add greeting message
                this.addBotMessage(data.greeting, true);
            }
        } catch (error) {
            console.error('Chatbot initialization error:', error);
        }
    }
    
    /**
     * Toggle chat window
     */
    toggleChat() {
        this.isOpen = !this.isOpen;
        const toggle = document.getElementById('chatbotToggle');
        const window = document.getElementById('chatbotWindow');
        const notification = document.getElementById('chatbotNotification');
        
        toggle.classList.toggle('active');
        window.classList.toggle('active');
        
        if (this.isOpen) {
            document.getElementById('chatbotInput').focus();
            notification.style.display = 'none';
        }
    }
    
    /**
     * Show notification badge
     */
    showNotification() {
        if (!this.isOpen) {
            const notification = document.getElementById('chatbotNotification');
            notification.style.display = 'flex';
        }
    }
    
    /**
     * Send user message
     */
    async sendMessage() {
        const input = document.getElementById('chatbotInput');
        const message = input.value.trim();
        
        if (!message) return;
        
        // Clear input
        input.value = '';
        
        // Add user message to chat
        this.addUserMessage(message);
        
        // Show typing indicator
        this.showTyping();
        
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'send_message',
                    message: message,
                    session_id: this.sessionId,
                    conversation_id: this.conversationId
                })
            });
            
            const data = await response.json();
            
            // Hide typing indicator
            this.hideTyping();
            
            if (data.success) {
                // Add bot response
                this.addBotMessage(data.response);
                
                // Add suggestions if available
                if (data.suggestions && data.suggestions.length > 0) {
                    this.addSuggestions(data.suggestions);
                }
            } else {
                this.addBotMessage('Sorry, I encountered an error. Please try again.');
            }
        } catch (error) {
            this.hideTyping();
            console.error('Send message error:', error);
            this.addBotMessage('Sorry, I\'m having trouble connecting. Please try again later.');
        }
    }
    
    /**
     * Add user message to chat
     */
    addUserMessage(message) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const messageHTML = `
            <div class="message user">
                <div class="message-bubble">${this.escapeHtml(message)}</div>
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="white">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
            </div>
        `;
        
        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        this.scrollToBottom();
    }
    
    /**
     * Add bot message to chat
     */
    addBotMessage(message, isGreeting = false) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const formattedMessage = this.formatMessage(message);
        
        const messageHTML = `
            <div class="message bot">
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="white">
                        <path d="M20 9V7c0-1.1-.9-2-2-2h-3c0-1.66-1.34-3-3-3S9 3.34 9 5H6c-1.1 0-2 .9-2 2v2c-1.66 0-3 1.34-3 3s1.34 3 3 3v4c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-4c1.66 0 3-1.34 3-3s-1.34-3-3-3zM7.5 11.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5S9.83 13 9 13s-1.5-.67-1.5-1.5zM16 17H8v-2h8v2zm-.5-4c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                    </svg>
                </div>
                <div class="message-bubble">${formattedMessage}</div>
            </div>
        `;
        
        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        this.scrollToBottom();
        
        // Show notification if chat is closed
        if (!this.isOpen && !isGreeting) {
            this.showNotification();
        }
    }
    
    /**
     * Add quick suggestions
     */
    addSuggestions(suggestions) {
        const messagesContainer = document.getElementById('chatbotMessages');
        
        let suggestionsHTML = '<div class="quick-suggestions">';
        suggestions.forEach(suggestion => {
            suggestionsHTML += `
                <button class="suggestion-btn" onclick="synergexChatbot.sendSuggestion('${this.escapeHtml(suggestion)}')">
                    ${this.escapeHtml(suggestion)}
                </button>
            `;
        });
        suggestionsHTML += '</div>';
        
        messagesContainer.insertAdjacentHTML('beforeend', suggestionsHTML);
        this.scrollToBottom();
    }
    
    /**
     * Send suggestion as message
     */
    sendSuggestion(suggestion) {
        document.getElementById('chatbotInput').value = suggestion;
        this.sendMessage();
    }
    
    /**
     * Show typing indicator
     */
    showTyping() {
        const messagesContainer = document.getElementById('chatbotMessages');
        const typingHTML = `
            <div class="message bot">
                <div class="message-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="white">
                        <path d="M20 9V7c0-1.1-.9-2-2-2h-3c0-1.66-1.34-3-3-3S9 3.34 9 5H6c-1.1 0-2 .9-2 2v2c-1.66 0-3 1.34-3 3s1.34 3 3 3v4c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-4c1.66 0 3-1.34 3-3s-1.34-3-3-3zM7.5 11.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5S9.83 13 9 13s-1.5-.67-1.5-1.5zM16 17H8v-2h8v2zm-.5-4c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                    </svg>
                </div>
                <div class="typing-indicator active" id="typingIndicator">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </div>
            </div>
        `;
        
        messagesContainer.insertAdjacentHTML('beforeend', typingHTML);
        this.scrollToBottom();
    }
    
    /**
     * Hide typing indicator
     */
    hideTyping() {
        const typing = document.getElementById('typingIndicator');
        if (typing) {
            typing.closest('.message').remove();
        }
    }
    
    /**
     * Scroll to bottom of messages
     */
    scrollToBottom() {
        const messagesContainer = document.getElementById('chatbotMessages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    /**
     * Format message with line breaks and links
     */
    formatMessage(message) {
        // Convert line breaks
        message = message.replace(/\n/g, '<br>');
        
        // Convert URLs to links
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        message = message.replace(urlRegex, '<a href="$1" target="_blank">$1</a>');
        
        return message;
    }
    
    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize chatbot when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.synergexChatbot = new SynergexChatbot();
    });
} else {
    window.synergexChatbot = new SynergexChatbot();
}
