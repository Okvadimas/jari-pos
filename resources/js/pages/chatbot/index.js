/**
 * JARI POS — Chatbot Page
 * Chat interaction, KB document upload/delete, UI state
 */
import { marked } from 'marked';

// ---- Config ----
const CONFIG   = window.ChatbotConfig || {};
const ROUTES   = CONFIG.routes || {};
const CSRF     = CONFIG.csrfToken || '';
const DROPZONE = CONFIG.dropzone || {
    maxFileSize: 5,
    maxFiles: 1,
    acceptedTypes: ['application/pdf', 'text/plain'],
    acceptedExtensions: '.pdf,.txt'
};

// Whether at least one document with status 'ready'/'completed' exists for this company
let hasIndexedDocs = CONFIG.hasIndexedDocs || false;

let isSending  = false;
let selectedFile = null;

// =============================================
// INITIALIZATION
// =============================================
$(document).ready(function () {
    bindDeleteButtons();
    initDropzone();
    updateCharCount();
    setupMobileModal();
    $('#chat-input').focus();
});

// =============================================
// MOBILE MODAL LOGIC
// =============================================
/**
 * Clone KB Panel contents into Bootstrap Modal for mobile view
 */
const setupMobileModal = () => {
    const $kbModal = $('#kbModal');
    
    // When modal opens, move content from desktop panel to modal body
    $kbModal.on('show.bs.modal', function() {
        if ($(window).width() < 992) { // lg breakpoint
            const $content = $('#kb-panel').children().detach();
            $('#mobile-kb-container').append($content);
        }
    });

    // When modal closes, move content back to desktop panel
    $kbModal.on('hidden.bs.modal', function() {
        const $content = $('#mobile-kb-container').children().detach();
        $('#kb-panel').append($content);
    });

    // Handle window resize to auto-move content if breakpoint changes
    $(window).on('resize', function() {
        if ($(window).width() >= 992) {
            if ($kbModal.hasClass('show')) {
                $kbModal.modal('hide');
            } else if ($('#mobile-kb-container').children().length > 0) {
                const $content = $('#mobile-kb-container').children().detach();
                $('#kb-panel').append($content);
            }
        }
    });
};

// =============================================
// DROPZONE INITIALIZATION
// =============================================

/**
 * Initialize Dropzone behaviors (drag/drop, click-to-browse)
 */
const initDropzone = () => {
    // Need delegated events since dropzone can move between DOM elements (modal <> panel)
    $(document).on('click', '#dropzone-area', () => $('#file-input').trigger('click'));

    $(document).on('dragover dragenter', '#dropzone-area', (e) => {
        e.preventDefault();
        e.stopPropagation();
        $('#dropzone-area').addClass('dragover');
    });

    $(document).on('dragleave dragend', '#dropzone-area', (e) => {
        e.preventDefault();
        e.stopPropagation();
        $('#dropzone-area').removeClass('dragover');
    });

    $(document).on('drop', '#dropzone-area', (e) => {
        e.preventDefault();
        e.stopPropagation();
        $('#dropzone-area').removeClass('dragover');
        const files = e.originalEvent.dataTransfer.files;
        if (files && files.length > 0) {
            handleFileSelect(files[0]);
        }
    });

    $(document).on('change', '#file-input', function () {
        if (this.files && this.files[0]) {
            handleFileSelect(this.files[0]);
        } else {
            cancelFileSelect();
        }
    });
};

// =============================================
// CHAT FUNCTIONS
// =============================================

/**
 * Send a chat message via AJAX
 */
const sendMessage = () => {
    const $input = $('#chat-input');
    const message = $input.val().trim();
    if (!message || isSending) return;

    // Guard: no indexed documents
    if (!hasIndexedDocs) {
        renderMessage('user', message);
        $input.val('').css('height', 'auto');
        updateCharCount();
        renderMessage('bot', 'Silakan upload minimal 1 dokumen yang sudah berhasil diindeks (status: Indexed) terlebih dahulu sebelum memulai percakapan.');
        scrollToBottom();
        return;
    }

    isSending = true;
    $('#btn-send').prop('disabled', true);

    // Render user bubble & reset input
    renderMessage('user', message);
    $input.val('').css('height', 'auto');
    updateCharCount();

    // Show typing indicator
    showTypingIndicator();

    fetch(ROUTES.ask, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'text/event-stream'
        },
        body: JSON.stringify({ message: message })
    })
    .then(async response => {
        if (!response.ok) {
            removeTypingIndicator();
            const errData = await response.json().catch(() => ({}));
            renderMessage('bot', errData.message || 'Maaf, terjadi kesalahan.');
            isSending = false;
            updateCharCount();
            return;
        }

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            removeTypingIndicator();
            const data = await response.json();
            if (data.status && data.data && data.data.reply) {
                renderMessage('bot', data.data.reply);
            } else {
                renderMessage('bot', data.message || 'Maaf, terjadi kesalahan.');
            }
            isSending = false;
            updateCharCount();
            $input.focus();
            return;
        }

        // Handle Stream
        const reader = response.body.getReader();
        const decoder = new TextDecoder('utf-8');
        
        let botReplyRaw = '';
        let buffer = '';
        let isFirstChunk = true;
        let $bubble = null;

        const processStream = async () => {
            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');
                
                // Keep the last partial line in the buffer
                buffer = lines.pop();

                for (const line of lines) {
                    if (line.startsWith('data: ') && line !== 'data: [DONE]') {
                        const dataStr = line.substring(6).trim();
                        if (!dataStr) continue;

                        try {
                            const dataObj = JSON.parse(dataStr);
                            if (dataObj.type === 'text_delta' && dataObj.delta) {
                                if (isFirstChunk) {
                                    removeTypingIndicator();
                                    const msgId = 'bot-msg-' + Date.now();
                                    const html = `
                                        <div class="cb-msg cb-msg-bot">
                                            <div class="cb-msg-body">
                                                <div class="cb-msg-bubble cb-msg-streaming" id="${msgId}"></div>
                                            </div>
                                        </div>
                                    `;
                                    $('#chat-messages').append(html);
                                    $bubble = $(`#${msgId}`);
                                    isFirstChunk = false;
                                }

                                botReplyRaw += dataObj.delta;
                                // We use formatBotMessage which renders markdown
                                $bubble.html(formatBotMessage(botReplyRaw));
                                scrollToBottom();
                            }
                        } catch (e) {
                            console.error('JSON parse error from stream line:', dataStr, e);
                        }
                    }
                }
            }
            // Parse remaining buffer
            if (buffer.startsWith('data: ') && buffer !== 'data: [DONE]') {
                const dataStr = buffer.substring(6).trim();
                if (dataStr) {
                    try {
                        const dataObj = JSON.parse(dataStr);
                        if (dataObj.type === 'text_delta' && dataObj.delta) {
                            if (isFirstChunk) {
                                removeTypingIndicator();
                                const msgId = 'bot-msg-' + Date.now();
                                const html = `
                                    <div class="cb-msg cb-msg-bot">
                                        <div class="cb-msg-body">
                                            <div class="cb-msg-bubble cb-msg-streaming" id="${msgId}"></div>
                                        </div>
                                    </div>
                                `;
                                $('#chat-messages').append(html);
                                $bubble = $(`#${msgId}`);
                                isFirstChunk = false;
                            }
                            botReplyRaw += dataObj.delta;
                            $bubble.html(formatBotMessage(botReplyRaw));
                            scrollToBottom();
                        }
                    } catch (e) {}
                }
            }

            if (isFirstChunk) {
                removeTypingIndicator();
                renderMessage('bot', 'Maaf, tidak ada balasan dari AI.');
            } else {
                $bubble.removeClass('cb-msg-streaming');
            }

            isSending = false;
            updateCharCount();
            $input.focus();
        };

        processStream().catch(err => {
            console.error('Stream processing error:', err);
            if (isFirstChunk) removeTypingIndicator();
            isSending = false;
            updateCharCount();
        });
    })
    .catch(err => {
        console.error('Chat error:', err);
        removeTypingIndicator();
        renderMessage('bot', 'Maaf, terjadi kesalahan koneksi. Silakan coba lagi.');
        isSending = false;
        updateCharCount();
    });
};

/**
 * Render a chat message bubble
 */
const renderMessage = (role, text) => {
    const timeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    let html = '';

    if (role === 'bot') {
        html = `
            <div class="cb-msg cb-msg-bot">
                <div class="cb-msg-body">
                    <div class="cb-msg-bubble">${formatBotMessage(text)}</div>
                </div>
            </div>
        `;
    } else {
        html = `
            <div class="cb-msg cb-msg-user">
                <div class="cb-msg-body">
                    <div class="cb-msg-bubble">${escapeHtml(text)}</div>
                </div>
            </div>
        `;
    }

    $('#chat-messages').append(html);
    scrollToBottom();
};

/**
 * Format bot message: markdown → HTML using marked.js
 */
const formatBotMessage = (text) => {

    marked.setOptions({
        breaks: true,
        gfm: true,
        highlight: function(code, lang) {
            if (lang && hljs.getLanguage(lang)) {
                try {
                    return hljs.highlight(code, { language: lang }).value;
                } catch (e) {}
            }
            return hljs.highlightAuto(code).value;
        }
    });

    return marked.parse(text);
};

/**
 * Escape HTML special characters
 */
const escapeHtml = (str) => {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
};

const showTypingIndicator = () => {
    const html = `
        <div class="cb-msg cb-msg-bot" id="typing-indicator">
            <div class="cb-msg-body">
                <div class="cb-msg-bubble cb-typing-indicator">
                    Sedang mengetik...
                </div>
            </div>
        </div>
    `;
    $('#chat-messages').append(html);
    scrollToBottom();
};

/**
 * Remove typing indicator
 */
const removeTypingIndicator = () => {
    $('#typing-indicator').remove();
};

/**
 * Scroll chat body to bottom
 */
const scrollToBottom = () => {
    const chatBody = document.getElementById('chat-body');
    if (chatBody) {
        requestAnimationFrame(() => {
            chatBody.scrollTop = chatBody.scrollHeight;
        });
    }
};

/**
 * Update character count display
 */
const updateCharCount = () => {
    let val = $('#chat-input').val() || '';
    if (val.length > 100) {
        val = val.substring(0, 100);
        $('#chat-input').val(val);
    }
    $('#char-count').text(`${val.length}/100`);
    
    // Enable only if: text present + not sending + has indexed documents
    $('#btn-send').prop('disabled', val.trim().length === 0 || isSending || !hasIndexedDocs);
};

/**
 * Auto-resize textarea
 */
const autoResize = () => {
    const el = document.getElementById('chat-input');
    if (!el) return;
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
};

// =============================================
// SEARCH FUNCTIONS
// =============================================



let currentSearchMatches = [];
let currentSearchIndex = 0;

/**
 * Filter/highlight messages based on search query
 */
const searchMessages = (query) => {
    const q = query.trim().toLowerCase();

    // Clear previous highlights
    clearSearchHighlights();
    currentSearchMatches = [];
    currentSearchIndex = 0;

    if (!q) {
        $('.cb-msg').removeClass('search-hidden');
        $('#search-desktop-actions, #search-mobile-actions').addClass('d-none');
        return;
    }

    $('#search-desktop-actions, #search-mobile-actions').removeClass('d-none');

    let matchCount = 0;

    $('.cb-msg').each(function () {
        const $msg = $(this);
        const $bubble = $msg.find('.cb-msg-bubble');
        const originalHtml = $bubble.html();

        let hasMatchInBubble = false;
        const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
        
        // Replace matching text outside of HTML tags safely
        const newHtml = originalHtml.replace(/(>[^<]*<|^[^<]*<|>[^<]*$|^[^<]*$)/g, function(match) {
            return match.replace(regex, function(m) {
                matchCount++;
                hasMatchInBubble = true;
                return `<span class="search-highlight" id="search-match-${matchCount}">${m}</span>`;
            });
        });

        if (hasMatchInBubble) {
            $bubble.html(newHtml);
        }
        
        // Unlike before, we do NOT add 'search-hidden' to elements that don't match.
        // This mimics Whatsapp's "Jump to Location" where context is preserved.
        $msg.removeClass('search-hidden');
    });

    if (matchCount > 0) {
        currentSearchMatches = Array.from({length: matchCount}, (_, i) => i + 1);
        currentSearchIndex = 1;
        updateSearchUI();
        jumpToMatch(currentSearchIndex);
    } else {
        updateSearchUI(0, 0);
    }
};

const updateSearchUI = (current = currentSearchIndex, total = currentSearchMatches.length) => {
    const text = `${current}/${total}`;
    $('#search-desktop-count, #search-mobile-count').text(text);
};

const jumpToMatch = (index) => {
    if (index === 0 || currentSearchMatches.length === 0) return;
    
    $('.search-highlight.active-match').removeClass('active-match');
    const $target = $(`#search-match-${index}`);
    
    if ($target.length) {
        $target.addClass('active-match');
        
        // Scroll to target smoothly within chat body container
        const chatBody = document.getElementById('chat-body');
        const targetOffsetTop = $target.offset().top;
        const chatBodyOffsetTop = $(chatBody).offset().top;
        const currentScrollTop = chatBody.scrollTop;
        
        chatBody.scroll({
            top: targetOffsetTop - chatBodyOffsetTop + currentScrollTop - 60,
            behavior: 'smooth'
        });
    }
};

const nextSearchMatch = (e) => {
    if(e) e.preventDefault();
    if (currentSearchMatches.length === 0) return;
    currentSearchIndex++;
    if (currentSearchIndex > currentSearchMatches.length) currentSearchIndex = 1;
    updateSearchUI();
    jumpToMatch(currentSearchIndex);
};

const prevSearchMatch = (e) => {
    if(e) e.preventDefault();
    if (currentSearchMatches.length === 0) return;
    currentSearchIndex--;
    if (currentSearchIndex < 1) currentSearchIndex = currentSearchMatches.length;
    updateSearchUI();
    jumpToMatch(currentSearchIndex);
};

/**
 * Clear search highlights and show all messages
 */
const clearSearchHighlights = () => {
    $('.search-highlight').each(function () {
        const parent = this.parentNode;
        parent.replaceChild(document.createTextNode(this.textContent), this);
        parent.normalize();
    });
    $('.cb-msg').removeClass('search-hidden');
};

/**
 * Escape special regex characters
 */
const escapeRegex = (str) => {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
};

// =============================================
// KNOWLEDGE BASE FUNCTIONS
// =============================================

/**
 * Handle initial file selection (preview state)
 */
const handleFileSelect = (file) => {
    if (!file) {
        cancelFileSelect();
        return;
    }

    // Validate type using config
    if (!DROPZONE.acceptedTypes.includes(file.type)) {
        NioApp.Toast('Format file tidak didukung. Gunakan PDF atau TXT.', 'warning', { position: 'top-right' });
        cancelFileSelect();
        return;
    }

    // Validate size using config (MB → bytes)
    const maxBytes = DROPZONE.maxFileSize * 1024 * 1024;
    if (file.size > maxBytes) {
        NioApp.Toast(`Ukuran file melebihi batas ${DROPZONE.maxFileSize}MB.`, 'warning', { position: 'top-right' });
        cancelFileSelect();
        return;
    }

    selectedFile = file;

    // Update UI
    $('#file-label').text(file.name);
    $('#dropzone-area').addClass('has-file');
    $('#btn-submit-upload').prop('disabled', false);
};

/**
 * Cancel file selection
 */
const cancelFileSelect = () => {
    selectedFile = null;
    $('#file-input').val('');
    $('#file-label').text('Klik atau drag file ke sini');
    $('#dropzone-area').removeClass('has-file');
    $('#btn-submit-upload').prop('disabled', true);
    $('#upload-progress').removeClass('show');
};

/**
 * Upload a document
 */
const uploadDocument = () => {
    if (!selectedFile) return;

    const formData = new FormData();
    formData.append('file', selectedFile);

    // Show progress
    $('#upload-progress').addClass('show');
    $('#btn-submit-upload').prop('disabled', true);
    $('#file-input').prop('disabled', true);

    $.ajax({
        url: ROUTES.upload,
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF },
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            if (data.status) {
                NioApp.Toast(data.message, 'success', { position: 'top-right' });
                refreshDocumentList();
            } else {
                NioApp.Toast(data.message || 'Gagal mengunggah dokumen.', 'error', { position: 'top-right' });
            }
        },
        error: function (xhr) {
            console.error('Upload error:', xhr);
            NioApp.Toast('Gagal mengunggah dokumen. Periksa koneksi Anda.', 'error', { position: 'top-right' });
        },
        complete: function () {
            cancelFileSelect();
            $('#file-input').prop('disabled', false);
        }
    });
};

/**
 * Format bytes to readable string
 */
const formatBytes = (bytes, decimals = 2) => {
    if (!+bytes) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
};

/**
 * Delete a document
 */
const deleteDocumentById = (id) => {
    Swal.fire({
        title: 'Hapus Dokumen?',
        text: 'Dokumen akan dihapus dari basis pengetahuan dan tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `${ROUTES.deleteDocument}/${id}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF },
                success: function (data) {
                    if (data.status) {
                        NioApp.Toast(data.message, 'success');
                        refreshDocumentList();
                    } else {
                        NioApp.Toast(data.message || 'Gagal menghapus dokumen.', 'error');
                    }
                },
                error: function (xhr) {
                    console.error('Delete error:', xhr);
                    NioApp.Toast('Gagal menghapus dokumen.', 'error');
                }
            });
        }
    });
};

/**
 * Refresh document list via AJAX
 */
const refreshDocumentList = (showSpinner = false) => {
    if (showSpinner) {
        $('#refresh-icon').addClass('spin-animation');
        $('#btn-refresh-docs').prop('disabled', true);
    }

    $.ajax({
        url: ROUTES.documents,
        type: 'GET',
        success: function (data) {
            if (data.status && Array.isArray(data.data)) {
                renderDocumentList(data.data);
            }
        },
        error: function (xhr) {
            console.error('Refresh docs error:', xhr);
        },
        complete: function () {
            if (showSpinner) {
                setTimeout(() => {
                    $('#refresh-icon').removeClass('spin-animation');
                    $('#btn-refresh-docs').prop('disabled', false);
                }, 400);
            }
        }
    });
};

/**
 * Render document list as cards
 */
const renderDocumentList = (documents) => {
    const $list = $('#doc-list');
    $list.empty();

    // Track previous state to detect first-indexed transition
    const hadIndexedBefore = hasIndexedDocs;

    // Recompute indexed docs state so button stays accurate without page reload
    hasIndexedDocs = documents.some(d => d.status === 'ready' || d.status === 'completed');
    updateCharCount(); // re-evaluate button disabled state

    // If first transition from no-indexed -> has-indexed, update the welcome message
    if (!hadIndexedBefore && hasIndexedDocs) {
        const $firstBotBubble = $('#chat-messages .cb-msg-bot:first-child .cb-msg-bubble');
        if ($firstBotBubble.length) {
            $firstBotBubble.html('Halo! Saya asisten AI Anda. Ada yang bisa saya bantu terkait dokumen yang telah diunggah?');
        }
    }

    if (documents.length === 0) {
        $list.html(`
            <div class="doc-empty">
                <em class="icon ni ni-inbox doc-empty-icon text-muted"></em>
                <span class="text-muted">Belum ada dokumen.<br>Unggah file untuk memulai.</span>
            </div>
        `);
        return;
    }

    documents.forEach(function (doc) {
        let statusClass = 'status-error';
        let statusLabel = 'Error';

        if (doc.status === 'ready' || doc.status === 'completed') {
            statusClass = 'status-ready';
            statusLabel = 'Indexed';
        } else if (doc.status === 'processing' || doc.status === 'pending') {
            statusClass = 'status-processing';
            statusLabel = doc.status === 'processing' ? 'Processing' : 'Pending';
        }

        $list.append(`
            <div class="doc-item-card" data-id="${doc.id}">
                <div class="doc-item-info">
                    <span class="doc-item-name" title="${escapeHtml(doc.filename)}">${escapeHtml(doc.filename)}</span>
                    <span class="doc-item-status ${statusClass}">
                        <span class="status-dot"></span>${statusLabel}
                    </span>
                </div>
                <div class="doc-item-actions">
                    <div class="dropdown">
                        <button class="btn btn-icon btn-sm" data-bs-toggle="dropdown" title="Opsi">
                            <em class="icon ni ni-more-h"></em>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                            <ul class="link-list-opt no-bdr">
                                <li><a href="#" class="btn-delete-doc text-danger" data-id="${doc.id}"><em class="icon ni ni-trash"></em><span>Hapus</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `);
    });

    bindDeleteButtons();
};

/**
 * Bind delete buttons
 */
const bindDeleteButtons = () => {
    // Delegated binding since elements might be recreated
    $('.doc-list-container').off('click', '.btn-delete-doc').on('click', '.btn-delete-doc', function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Hide dropdown
        const $dropdown = $(this).closest('.dropdown-menu');
        if ($dropdown.hasClass('show')) {
            $dropdown.removeClass('show');
        }
        
        deleteDocumentById($(this).data('id'));
    });
};

// =============================================
// EVENT LISTENERS
// =============================================

// Send on click
$('#btn-send').on('click', function (e) {
    e.preventDefault();
    sendMessage();
});

// Enter → send, Shift+Enter → new line
$('#chat-input').on('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

// Auto-resize + char count
$('#chat-input').on('input', function () {
    autoResize();
    updateCharCount();
});

// Handle Paste (Text Only)
$('#chat-input').on('paste', function(e) {
    e.preventDefault();
    let text = (e.originalEvent || e).clipboardData.getData('text/plain');
    if (text) {
        const currentLen = $(this).val().length;
        if (text.length + currentLen > 100) {
            text = text.substring(0, 100 - currentLen);
        }
        const start = this.selectionStart;
        const end = this.selectionEnd;
        const val = $(this).val();
        $(this).val(val.substring(0, start) + text + val.substring(end));
        this.selectionStart = this.selectionEnd = start + text.length;
        $(this).trigger('input');
    }
});

// Upload button (delegated since it can move to modal)
$(document).on('click', '#btn-submit-upload', function() {
    uploadDocument();
});

// Refresh document status button
$(document).on('click', '#btn-refresh-docs', function() {
    refreshDocumentList(true);
});

// Desktop Search input
$('#chat-search-desktop').on('input', function () {
    const val = $(this).val();
    $('#chat-search-mobile').val(val); // sync
    searchMessages(val);
});

// Mobile Search input
$('#chat-search-mobile').on('input', function () {
    const val = $(this).val();
    $('#chat-search-desktop').val(val); // sync
    searchMessages(val);
});

// Search Prev / Next Handlers
$('#search-desktop-prev, #search-mobile-prev').on('click', prevSearchMatch);
$('#search-desktop-next, #search-mobile-next').on('click', nextSearchMatch);

// Focus mobile search on modal open
$('#searchModal').on('shown.bs.modal', function () {
    $('#chat-search-mobile').focus();
});

// Clear highlights when mobile modal is closed if it's empty
$('#searchModal').on('hidden.bs.modal', function () {
    if (!$('#chat-search-mobile').val()) {
        clearSearchHighlights();
    }
});

