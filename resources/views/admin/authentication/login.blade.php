<!DOCTYPE html>
<html lang="en">
<head>
    @stack('meta')
    @include('layouts.admin.dependency.css')
    @stack('css')
</head>

<body>
		<!-- Main Wrapper -->
        <div class="main-wrapper">
			<div class="account-content">
				<div class="row login-wrapper m-0">
                    <div class="col-lg-6 p-0">
                        <div class="login-content">
                            <form action="https://dreamspos.dreamstechnologies.com/html/template/index.html">
                                <div class="login-userset">
                                    <div class="login-logo logo-normal">
                                    <img src="{{ asset('resource/admin/assets/img/logo.svg')}}" alt="img">
                                </div>
                                <a href="/" class="login-logo logo-white">
                                    <img src="{{ asset('resource/admin/assets/img/logo-white.svg') }}"  alt="Img">
                                </a>
                                <div class="login-userheading">
                                    <h3>Sign In</h3>
                                    <h4>Access the Dreamspos panel using your email and passcode.</h4>
                                </div>
                                <div class="mb-3">
                                        <label class="form-label">Email Address</label>
                                        <div class="input-group">
                                            <input type="text" value="" class="form-control border-end-0">
                                            <span class="input-group-text border-start-0">
                                                <i class="ti ti-mail"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <div class="pass-group">
                                            <input type="password" class="pass-input form-control">
                                            <span class="ti toggle-password ti-eye-off text-gray-9"></span>
                                        </div>
                                    </div>
                                <div class="form-login authentication-check">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="custom-control custom-checkbox">
                                                <label class="checkboxs ps-4 mb-0 pb-0 line-height-1">
                                                    <input type="checkbox">
                                                    <span class="checkmarks"></span>Remember me
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <a class="forgot-link" href="forgot-password-2.html">Forgot Password?</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-login">
                                    <button type="submit" class="btn btn-login">Sign In</button>
                                </div>
                                <div class="signinform">
                                    <h4>New on our platform?<a href="register-2.html" class="hover-a"> Create an account</a></h4>
                                </div>
                                <div class="form-setlogin or-text">
                                    <h4>OR</h4>
                                </div>
                                <div class="form-sociallink">
                                    <div class="d-flex align-items-center justify-content-center flex-wrap">
                                        <div class="text-center me-2 flex-fill">
                                            <a href="javascript:void(0);"
                                                class="br-10 p-2 btn btn-info d-flex align-items-center justify-content-center">
                                                <img class="img-fluid m-1" src="{{ asset('resource/admin/assets/img/icons/facebook-logo.svg') }}" alt="Facebook">
                                            </a>
                                        </div>
                                        <div class="text-center me-2 flex-fill">
                                            <a href="javascript:void(0);"
                                                class="btn btn-white br-10 p-2  border d-flex align-items-center justify-content-center">
                                                <img class="img-fluid m-1" src="{{ asset('resource/admin/assets/img/icons/google-logo.svg')}}" alt="Facebook">
                                            </a>
                                        </div>
                                        <div class="text-center flex-fill">
                                            <a href="javascript:void(0);"
                                                class="bg-dark br-10 p-2 btn btn-dark d-flex align-items-center justify-content-center">
                                                <img class="img-fluid m-1" src="{{ asset('resource/admin/assets/img/icons/apple-logo.svg')}}" alt="Apple">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="my-4 d-flex justify-content-center align-items-center copyright-text">
                                        <a href="{{ route('home') }}"><p>Copyright &copy; 2025 DreamsPOS</p></a>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6 p-0">
                        <div class="login-img">
                            <img src="{{ asset('resource/admin/assets/img/authentication/authentication-01.svg') }}" alt="img">
                        </div>
                    </div>
                </div>
			</div>
        </div>
		<!-- /Main Wrapper -->
    {{-- 1️⃣ Load required JS files FIRST --}}
    @include('layouts.admin.dependency.js')
    @stack('scripts')
</body>
</html>
