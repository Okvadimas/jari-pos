@extends('layouts.base')

@section('content')
<div class="nk-content chatbot-page">

    <div class="container-fluid p-0 h-100">
        <div class="row g-0 chatbot-main h-100">

            {{-- Left Side: Knowledge Base Panel (Desktop only, hidden on mobile) --}}
            <div class="col-lg-4 kb-panel d-none d-lg-flex" id="kb-panel">
                {{-- Upload Section --}}
                <div class="cb-card">
                    <h5 class="cb-card-title"><em class="icon ni ni-upload-cloud me-2 text-primary"></em>Upload Basis Pengetahuan</h5>
                    <p class="cb-card-desc">Unggah dokumen sebagai basis pengetahuan AI</p>

                    <div class="dropzone-area" id="dropzone-area">
                        <em class="icon ni ni-upload dropzone-icon"></em>
                        <span class="dropzone-label" id="file-label">Klik atau drag file ke sini</span>
                        <span class="dropzone-hint">PDF, TXT — Maks 5MB</span>
                    </div>
                    <input type="file" id="file-input" accept=".pdf,.txt" hidden>

                    {{-- Upload Progress --}}
                    <div class="upload-progress" id="upload-progress">
                        <div class="upload-progress-bar bg-primary"></div>
                    </div>

                    <button class="btn btn-primary w-100 mt-2 text-center" id="btn-submit-upload" disabled>
                        <span>Upload Dokumen</span>
                    </button>
                </div>

                {{-- Document List Section --}}
                <div class="cb-card mt-3 flex-grow-1 d-flex flex-column" style="min-height: 250px;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <h5 class="cb-card-title mb-0"><em class="icon ni ni-folder me-2 text-primary"></em>Dokumen Tersimpan</h5>
                        <button class="btn btn-sm btn-outline-light border text-muted" id="btn-refresh-docs" title="Refresh status dokumen" style="padding: 4px 10px; font-size: 12px; border-radius: 6px;">
                            <em class="icon ni ni-reload" id="refresh-icon"></em>
                        </button>
                    </div>
                    <div class="doc-list-container flex-grow-1" id="doc-list">
                        @foreach($documents as $doc)
                            <div class="doc-item-card" data-id="{{ $doc->id }}">
                                <div class="doc-item-info">
                                    <span class="doc-item-name" title="{{ $doc->filename }}">{{ $doc->filename }}</span>
                                    @php
                                        $statusClass = match ($doc->status) {
                                            'ready', 'completed' => 'status-ready',
                                            'processing', 'pending' => 'status-processing',
                                            default => 'status-error',
                                        };
                                        $statusLabel = match ($doc->status) {
                                            'ready', 'completed' => 'Indexed',
                                            'processing' => 'Processing',
                                            'pending' => 'Pending',
                                            'failed' => 'Error',
                                            default => ucfirst($doc->status),
                                        };
                                    @endphp
                                    <span class="doc-item-status {{ $statusClass }}">
                                        <span class="status-dot"></span>{{ $statusLabel }}
                                    </span>
                                </div>
                                <div class="doc-item-actions">
                                    <div class="dropdown">
                                        <button class="btn btn-icon btn-sm" data-bs-toggle="dropdown" title="Opsi">
                                            <em class="icon ni ni-more-h"></em>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                            <ul class="link-list-opt no-bdr">
                                                <li><a href="#" class="btn-delete-doc text-danger" data-id="{{ $doc->id }}"><em class="icon ni ni-trash"></em><span>Hapus</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="doc-empty">
                                <em class="icon ni ni-inbox doc-empty-icon text-muted"></em>
                                <span class="text-muted">Belum ada dokumen.<br>Unggah file untuk memulai.</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Side: Chat --}}
            <div class="col-12 col-lg-8 chat-panel d-flex flex-column">
                {{-- Mobile KB Modal Trigger Banner (Visible only on mobile) --}}
                <div class="mobile-kb-banner d-lg-none d-flex align-items-center justify-content-between px-3 py-2 bg-white mb-2">
                    <div class="d-flex align-items-center">
                        <em class="icon ni ni-book-read text-primary me-2 fs-4"></em>
                        <div>
                            <span class="d-block fw-bold text-dark">Basis Pengetahuan</span>
                            <span class="d-block text-muted small">Kelola dokumen AI Anda</span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-primary rounded py-1 px-3" data-bs-toggle="modal" data-bs-target="#kbModal" style="font-size: 11px;">
                        Upload
                    </button>
                </div>

                {{-- Chat Header --}}
                <div class="chat-header">
                    <div class="chat-header-left">
                        <h5 class="chat-title"><em class="icon ni ni-digital-ocean fs-1 text-primary"></em> Jari AI</h5>
                    </div>
                    <div class="chat-header-right">
                        {{-- Desktop Search (Visible on md and up) --}}
                        <div class="chat-search-desktop d-none d-md-block">
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-left">
                                    <em class="icon ni ni-search"></em>
                                </div>
                                <input type="text" class="form-control" id="chat-search-desktop" placeholder="Cari pesan..." autocomplete="off" style="border-radius: 20px; padding-right: 90px;">
                                {{-- Search Actions (Count, Prev, Next) --}}
                                <div class="chat-search-actions d-none" id="search-desktop-actions">
                                    <span class="chat-search-count text-muted" id="search-desktop-count">0/0</span>
                                    <button class="btn btn-icon btn-sm btn-action text-secondary" id="search-desktop-prev"><em class="icon ni ni-chevron-up"></em></button>
                                    <button class="btn btn-icon btn-sm btn-action text-secondary" id="search-desktop-next"><em class="icon ni ni-chevron-down"></em></button>
                                </div>
                            </div>
                        </div>

                        {{-- Mobile Search Toggle (Visible only on mobile) --}}
                        <button class="chat-search-btn d-md-none" data-bs-toggle="modal" data-bs-target="#searchModal" title="Cari pesan">
                            <em class="icon ni ni-search"></em>
                        </button>
                    </div>
                </div>

                {{-- Chat Body --}}
                <div class="chat-body flex-grow-1 position-relative" id="chat-body">
                    <div class="chat-messages" id="chat-messages">
                        {{-- Welcome message --}}
                        @php
                            $hasIndexedDocs = $documents->whereIn('status', ['ready', 'completed'])->count() > 0;
                        @endphp
                        <div class="cb-msg cb-msg-bot">
                            <div class="cb-msg-body">
                                @if($hasIndexedDocs)
                                    <div class="cb-msg-bubble">Halo! Saya asisten AI Anda. Ada yang bisa saya bantu terkait dokumen yang telah diunggah?</div>
                                @else
                                    <div class="cb-msg-bubble">Halo! Sebelum kita mulai, silakan upload minimal 1 dokumen ke basis pengetahuan dan pastikan statusnya sudah <strong>Indexed</strong>. Setelah itu, Anda bisa langsung bertanya kepada saya.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chat Input --}}
                <div class="chat-footer mt-auto">
                    <div class="chat-input-wrapper">
                        <textarea id="chat-input" placeholder="Ketik pertanyaan Anda..." rows="1" maxlength="100"></textarea>
                        <span class="char-count" id="char-count">0/100</span>
                        <button class="btn btn-primary btn-icon btn-send" id="btn-send" disabled title="Kirim pesan">
                            <em class="icon ni ni-send"></em>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Mobile Knowledge Base Modal --}}
<div class="modal fade" id="kbModal" tabindex="-1" aria-labelledby="kbModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kbModalLabel"><em class="icon ni ni-book-read me-2 text-primary"></em> Basis Pengetahuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-lighter" id="mobile-kb-container">
                <!-- Content will be cloned here for mobile via JS -->
            </div>
        </div>
    </div>
</div>

{{-- Mobile Search Modal --}}
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel"><em class="icon ni ni-search me-2"></em> Cari Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="form-control-wrap">
                    <input type="text" class="form-control" id="chat-search-mobile" placeholder="Ketik kata yang dicari..." autocomplete="off" style="border-radius: 20px; padding-right: 90px;">
                    {{-- Search Actions (Count, Prev, Next) --}}
                    <div class="chat-search-actions d-none" id="search-mobile-actions">
                        <span class="chat-search-count text-muted" id="search-mobile-count">0/0</span>
                        <button class="btn btn-icon btn-sm btn-action text-secondary" id="search-mobile-prev"><em class="icon ni ni-chevron-up"></em></button>
                        <button class="btn btn-icon btn-sm btn-action text-secondary" id="search-mobile-next"><em class="icon ni ni-chevron-down"></em></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.ChatbotConfig = {
        routes: {
            ask: "{{ route('chatbot.ask') }}",
            upload: "{{ route('chatbot.upload') }}",
            documents: "{{ route('chatbot.documents') }}",
            deleteDocument: "{{ url('/chatbot/document') }}",
        },
        csrfToken: "{{ csrf_token() }}",
        hasIndexedDocs: {{ $hasIndexedDocs ? 'true' : 'false' }},
        dropzone: {
            maxFileSize: 5,
            maxFiles: 1,
            acceptedTypes: ['application/pdf', 'text/plain'],
            acceptedExtensions: '.pdf,.txt'
        }
    };
</script>
@endsection
