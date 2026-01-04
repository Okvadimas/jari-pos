@extends('layouts.base', ['title' => 'Page Not Found - 404'])

@section('body_attribute')
     class="authentication-bg"
@endsection

@section('content')
     <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
           <div class="container">
                 <div class="row justify-content-center">
                       <div class="col-xl-6">
                              <div class="card auth-card">
                                    <div class="card-body p-0">
                                          <div class="row align-items-center g-0">
                                                <div class="col">
                                                       <div class="p-4">
                                                             <div class="mx-auto mb-4 text-center">
                                                                   <div class="mx-auto text-center auth-logo">
                                                                         <a href="{{ route('root') }}" class="logo-dark">
                                                                                <img src="/images/logo-dark.png" height="30" alt="logo dark">
                                                                         </a>

                                                                         <a href="{{ route('root') }}" class="logo-light">
                                                                                <img src="/images/logo-white.png" height="30" alt="logo light">
                                                                         </a>
                                                                   </div>

                                                                   <img src="/images/404.svg" alt="auth" height="250" class="mt-5 mb-3" />

                                                                   <h2 class="fs-22 lh-base fw-bold">Page Not Found !</h2>
                                                                   <p class="text-muted mt-1 mb-4">The page you're trying to reach seems to have gone <br /> missing in the digital wilderness.</p>

                                                                   <div class="text-center">
                                                                         <a href="{{ route('root') }}" class="btn btn-success">Back to Home</a>
                                                                   </div>
                                                             </div>
                                                       </div>
                                                </div> <!-- end col -->
                                          </div> <!-- end row -->

                                    </div> <!-- end card-body -->
                              </div> <!-- end card -->

                       </div> <!-- end col -->
                 </div> <!-- end row -->
           </div>
     </div>
@endsection