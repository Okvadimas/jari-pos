<div class="nk-sidebar nk-sidebar-fixed is-light " data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head" style="padding: 20px">
        <div class="nk-sidebar-brand">
            <a href="{{ session()->get('tipe') == 'siswa' ? base_url('dashboard-siswa') : base_url('dashboard') }}"
                class="logo-link nk-sidebar-logo d-flex">
                @if (session()->get('tipe') == 'panel')
                    <img class="logo-img" src="{{ base_asset('assets/images/kesatrian-panel.png') }}"
                        srcset="{{ base_asset('assets/images/kesatrian-panel.png') }} 2x" alt="logo">
                @elseif (session()->get('tipe') == 'siswa')
                    <img class="logo-img" src="{{ base_asset('assets/images/kesatrian-panel.png') }}"
                        srcset="{{ base_asset('assets/images/kesatrian-panel.png') }} 2x" alt="logo-dark">
                @endif
                <img class="logo-small logo-img logo-img-small"
                    src="{{ base_asset('assets/images/logo-yayasan-500.png') }}"
                    srcset="{{ base_asset('assets/images/logo-yayasan-500.png') }} 2x" alt="logo-small">
            </a>
        </div>
        <div class="nk-menu-trigger me-n2">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none"
                data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex"
                data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
        </div>
    </div><!-- .nk-sidebar-element -->
    <div class="nk-sidebar-element">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    <li class="nk-menu-item">
                        <a href="{{ session()->get('tipe') == 'siswa' ? base_url('dashboard-siswa') : base_url('dashboard') }}"
                            class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-home-fill"></em></span>
                            <span class="nk-menu-text">Dashboard
                                {{ session()->get('tipe') == 'siswa' ? 'Siswa' : '' }}</span>
                        </a>
                    </li>
                    
                    @if (session()->get('tipe') == 'panel') 
                        {!! $menu !!}
                    @endif

                    <!-- Ambil menu dari controller -->
                    {{-- {!! $menu !!} --}}
                </ul><!-- .nk-menu -->
            </div><!-- .nk-sidebar-menu -->
        </div><!-- .nk-sidebar-content -->
    </div><!-- .nk-sidebar-element -->
</div>