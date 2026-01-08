@extends('layouts.base')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Hi, {{ ucwords(Auth::user()->name) }}</h3>
                            </div><!-- .nk-block-head-content -->
                            
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->

                    <div class="nk-block">

                    </div><!-- .nk-block -->
                    
                </div>
            </div>
        </div>
    </div>
@endsection