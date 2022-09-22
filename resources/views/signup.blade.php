@include('includes.head')


<div class="container">
    <div class="login-wrapper col-xl-8 col-lg-11 col-xs-12 sumb--putShadowbox">
        <div class="login-content row">

            <div class="login-content-left col-xl-4 col-lg-4 col-md-12">
                <img src="img/sumb_logo.png" class="login--logo">

                <div class="register-link">
                    Do you think you have an account? <a href="/">Login here</a>
                </div>
            </div>
                        
            <div class="login-content-right col-xl-8 col-lg-8 col-md-12">
                        @isset($err) 
                        <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                            {{ $errors[$err][0] }}
                        </div>
                        @endisset
                        <div class="login-form" style="padding-top:30px;">
                            <form action="\register" method="post">
                                @csrf
                                <div class="form-check form-switch">
                                    <input class="form-check-input" name="accountant" type="checkbox" role="switch" id="flexSwitchCheckDefault" @isset($form_accountant) checked @endisset>
                                    <label class="form-check-label" for="flexSwitchCheckDefault"><b>Are you an Accountant?</b></label><br><br>
                                  </div>
                                <div class="form-group">
                                    <label>User Email Address</label>
                                    <input class="au-input au-input--full" type="email" name="email" placeholder="Email" required value="@isset($form_email) {{$form_email}} @endisset">
                                </div>
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input class="au-input au-input--full" type="fullname" name="fullname" placeholder="Full Name" required value="@isset($form_fullname) {{$form_fullname}} @endisset">
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input class="au-input au-input--full" type="password" name="password1" placeholder="Password" required>
                                </div>
                                <div class="form-group">
                                    <label>Retry Password</label>
                                    <input class="au-input au-input--full" type="password" name="password2" placeholder="Retry Password" required>
                                </div>
                                <button class="au-btn au-btn--block au-btn--green m-b-20" type="submit">Register</button>
                            
                            </form>
                    </div>
                </div>
            </div>
                        
            <!--Show Only on Mobiles/Tablets-->
            <div class="login-content-left show-mobile--tablet col-md-12">
                <div class="register-link">
                    Don't you have account? <a href="#">Sign up here</a>
                </div>
            </div>

        </div>
    </div>
</div>

@include('includes.footer')

</body>

</html>
<!-- end document-->

@include('includes.footer')

</body>

</html>
<!-- end document-->