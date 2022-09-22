@include('includes.head')


<div class="container">
    <div class="login-wrapper col-xl-8 col-lg-11 col-xs-12 sumb--putShadowbox">
        <div class="login-content row">

            <div class="login-content-left col-xl-4 col-lg-4 col-md-12">
                <img src="img/sumb_logo.png" class="login--logo">

                <div class="register-link">
                    Don't you have account? <a href="/signup">Sign up here</a>
                </div>
            </div>
                        
            <div class="login-content-right col-xl-8 col-lg-8 col-md-12">

                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">User Login</a>
                        <a class="nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Accountant Login</a>
                    </div>

                                
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        @isset($err) 
                        <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                            {{ $errors[$err][0] }}
                        </div>
                        @endisset
                        <div class="login-form">
                            <form action="\login" method="post">
                                @csrf
                                <div class="form-group">
                                    <label class="sumb-text--black">User Email Address</label>
                                    <input class="au-input au-input--full" type="email" name="email" placeholder="Email" @if (!empty($request['email'])) value="{{ $request['email'] }}" @endif>
                                </div>
                                <div class="form-group">
                                    <label class="sumb-text--black">Password</label>
                                    <input class="au-input au-input--full" type="password" name="password" placeholder="Password">
                                </div>
                                <div class="login-checkbox m-b-10">
                                    <label class="sumb-text--black">
                                        <input type="checkbox" name="remember"><span>Remember Me</span>
                                    </label>
                                    <label>
                                        <a href="#">Forgotten Password?</a>
                                    </label>
                                </div>
                                <button class="au-btn au-btn--block sumb-btn--yellow m-b-30" type="submit">sign in</button>
                                <div class="social-login-content">
                                    <span class="sumb-text--black">OR</span>

                                    <label class="sumb-text--black">Continue with social media</label>

                                    <ul class="social-button">
                                        <li><a href="#" class="facebook" title="Facebook">&nbsp;</a></li>
                                        <li><a href="#" class="twitter" title="Twitter">&nbsp;</a></li>
                                        <li><a href="#" class="linkedIn" title="LinkedIn">&nbsp;</a></li>
                                        <li><a href="#" class="google" title="Google">&nbsp;</a></li>
                                    </ul>
                                </div>
                            </form>
                                        
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <div class="login-form">
                            <form action="\login" method="post">
                                @csrf
                                <div class="form-group">
                                    <label class="sumb-text--black">Accountant Email Address</label>
                                    <input class="au-input au-input--full" type="email" name="email" placeholder="Email" @if (!empty($request['email'])) value="{{ $request['email'] }}" @endif>
                                </div>
                                <div class="form-group">
                                    <label class="sumb-text--black">Password</label>
                                    <input class="au-input au-input--full" type="password" name="password" placeholder="Password">
                                </div>
                                <div class="login-checkbox m-b-10">
                                    <label class="sumb-text--black">
                                        <input type="checkbox" name="remember"><span>Remember Me</span>
                                    </label>
                                    <label>
                                        <a href="#">Forgotten Password?</a>
                                    </label>
                                </div>
                                <button class="au-btn au-btn--block sumb-btn--yellow m-b-30" type="submit">sign in</button>
                                <div class="social-login-content">
                                    <span class="sumb-text--black">OR</span>

                                    <label class="sumb-text--black">Continue with social media</label>

                                    <ul class="social-button">
                                        <li><a href="#" class="facebook" title="Facebook">&nbsp;</a></li>
                                        <li><a href="#" class="twitter" title="Twitter">&nbsp;</a></li>
                                        <li><a href="#" class="linkedIn" title="LinkedIn">&nbsp;</a></li>
                                        <li><a href="#" class="google" title="Google">&nbsp;</a></li>
                                    </ul>
                                </div>
                            </form>
                                        
                        </div>
                    </div>


                </div>
            </div>
                        
            <!--Show Only on Mobiles/Tablets-->
            <div class="login-content-left show-mobile--tablet col-md-12">
                <div class="register-link">
                    Don't you have account? <a href="/signup">Sign up here</a>
                </div>
            </div>

        </div>
    </div>
</div>

@include('includes.footer')

</body>

</html>
<!-- end document-->