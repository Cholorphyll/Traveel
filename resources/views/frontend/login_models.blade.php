    <!-- Login/Signup - Start-->
    <div class="modal tr-authentication tr-login-modal" id="signInModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="tr-auth-section">
            <div class="tr-auth-header">
              <h5 class="d-none d-md-block">Log in or Sign up</h5>
              <h5 class="d-block d-sm-block d-md-none">Sign up / Log in</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal">Close Button</button>
            </div> 
            <form action="" method="post" id="userlogin">
              @csrf
              <h3>Welcome to Travell</h3>  
              @if(session('success'))
                  <div class="alert alert-info d-block">
                      {{ session('success') }}
                  </div>
              @endif
              @if(session('error'))
                  <div class="alert alert-danger d-block">
                      {{ session('error') }}
                  </div>
              @endif
              <div class="alert alert-info success-msg d-none"> </div>
              <div class="tr-field-row">
                <div class="tr-field tr-email-field">
                  <label class="tr-field-label" for="emailAddress">Email address</label>
                  <input type="email" class="" id="emailAddress" name="email" value=""> 
                  <span class="d-none email-value"></span>                
               
                  <span class="text-danger email-msg"></span>
             
                </div>
              </div>
              <button type="submit" class="tr-btn">Continue</button>
            </form >
            <div class="tr-forgot-btn">
              <button type="submit" class="tr-anchor-btn" data-bs-toggle="modal" data-bs-target="#signUpModal" >Sign up</button></label>
            </div>
            <!--
            <div class="tr-or">or</div>
            <div class="tr-auth-options">
              <button type="button"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M23.9998 12.2758C24.0009 11.4581 23.9303 10.6418 23.789 9.83594H12.2437V14.4574H18.8563C18.721 15.1955 18.4346 15.8992 18.0143 16.5259C17.5941 17.1526 17.0488 17.6894 16.4112 18.104V21.1038H20.3577C22.6685 19.0156 23.9998 15.9274 23.9998 12.2758Z" fill="#4285F4"/><path d="M12.2396 23.9998C15.5434 23.9998 18.3251 22.9365 20.3536 21.1033L16.4071 18.1034C15.3087 18.8334 13.894 19.25 12.2396 19.25C9.04634 19.25 6.33599 17.1401 5.36679 14.2969H1.30127V17.3884C2.32023 19.3758 3.88269 21.0465 5.81422 22.214C7.74575 23.3814 9.97032 23.9997 12.2396 23.9998Z" fill="#34A853"/><path d="M5.36913 14.3019C4.85675 12.812 4.85675 11.1986 5.36913 9.70874V6.61719H1.30361C0.446421 8.28905 0 10.1342 0 12.0053C0 13.8764 0.446421 15.7216 1.30361 17.3934L5.36913 14.3019Z" fill="#FBBC04"/><path d="M12.2396 4.75061C13.9855 4.72265 15.6725 5.36921 16.9359 6.55054L20.4301 3.12567C18.2144 1.08587 15.2792 -0.0340098 12.2396 0.000787282C9.97032 0.000888653 7.74575 0.619188 5.81422 1.78666C3.88269 2.95412 2.32023 4.62481 1.30127 6.6122L5.36679 9.70375C6.33599 6.86052 9.04634 4.75061 12.2396 4.75061Z" fill="#EA4335"/></svg>Continue with Google</button>
              <button type="button"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_2272_39793)"><path d="M24 12C24 5.37264 18.6274 0 12 0C5.37264 0 0 5.37264 0 12C0 17.6275 3.87456 22.3498 9.10128 23.6467V15.6672H6.62688V12H9.10128V10.4198C9.10128 6.33552 10.9498 4.4424 14.9597 4.4424C15.72 4.4424 17.0318 4.59168 17.5685 4.74048V8.06448C17.2853 8.03472 16.7933 8.01984 16.1822 8.01984C14.2147 8.01984 13.4544 8.76528 13.4544 10.703V12H17.3741L16.7006 15.6672H13.4544V23.9122C19.3963 23.1946 24.0005 18.1354 24.0005 12H24Z" fill="#0866FF"/><path d="M16.7002 15.6662L17.3737 11.999H13.454V10.702C13.454 8.76429 14.2143 8.01885 16.1818 8.01885C16.7929 8.01885 17.2849 8.03373 17.5681 8.06349V4.73949C17.0314 4.59021 15.7196 4.44141 14.9593 4.44141C10.9493 4.44141 9.10087 6.33453 9.10087 10.4188V11.999H6.62646V15.6662H9.10087V23.6457C10.0292 23.8761 11.0002 23.999 11.9996 23.999C12.4916 23.999 12.9769 23.9688 13.4535 23.9112V15.6662H16.6997H16.7002Z" fill="white"/></g><defs><clipPath id="clip0_2272_39793"><rect width="24" height="24" fill="white"/></clipPath></defs></svg>Continue with Facebook</button>
              <button type="button"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_2272_39796)"><path d="M21.792 18.7033C21.429 19.5418 20.9994 20.3136 20.5016 21.0232C19.8231 21.9906 19.2676 22.6602 18.8395 23.0321C18.1758 23.6424 17.4647 23.955 16.7032 23.9728C16.1566 23.9728 15.4973 23.8172 14.73 23.5017C13.9601 23.1876 13.2525 23.0321 12.6056 23.0321C11.9271 23.0321 11.1994 23.1876 10.4211 23.5017C9.64153 23.8172 9.01355 23.9817 8.53342 23.9979C7.80322 24.0291 7.07539 23.7076 6.3489 23.0321C5.88521 22.6276 5.30523 21.9343 4.61043 20.9521C3.86498 19.9033 3.25211 18.687 2.77198 17.3004C2.25777 15.8026 2 14.3523 2 12.9482C2 11.3398 2.34754 9.95259 3.04367 8.79011C3.59076 7.85636 4.31859 7.11979 5.22953 6.57906C6.14046 6.03834 7.12473 5.76279 8.18469 5.74516C8.76467 5.74516 9.52524 5.92457 10.4704 6.27715C11.4129 6.63091 12.0181 6.81032 12.2834 6.81032C12.4817 6.81032 13.154 6.60054 14.2937 6.18234C15.3714 5.7945 16.281 5.63391 17.0262 5.69717C19.0454 5.86012 20.5624 6.6561 21.5712 8.09013C19.7654 9.18432 18.8721 10.7169 18.8898 12.6829C18.9061 14.2142 19.4617 15.4886 20.5535 16.5004C21.0483 16.97 21.6009 17.333 22.2156 17.5907C22.0823 17.9774 21.9416 18.3477 21.792 18.7033ZM17.161 0.480137C17.161 1.68041 16.7225 2.8011 15.8484 3.83841C14.7937 5.07155 13.5179 5.78413 12.1343 5.67168C12.1167 5.52769 12.1065 5.37614 12.1065 5.21688C12.1065 4.06462 12.6081 2.83147 13.4989 1.82321C13.9436 1.3127 14.5092 0.888228 15.1951 0.549615C15.8796 0.216055 16.5269 0.031589 17.1358 0C17.1536 0.160458 17.161 0.320926 17.161 0.480121V0.480137Z" fill="black"/></g><defs><clipPath id="clip0_2272_39796"><rect width="24" height="24" fill="white"/></clipPath></defs></svg>Continue with Apple</button>
            </div>
            -->
          </div>
        </div>
      </div>
    </div>
    <!-- Login/Signup - End-->

    <!-- Signup - Start-->
    <div class="modal tr-authentication" id="signUpModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="tr-auth-section">
            <div class="tr-auth-header">
              <h5 class="d-none d-md-block">Sign up</h5>
              <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#signInModal">Back Button</button>
            </div>
            <form id="register">
              <h3>Finish signing up</h3>             

              <div class="alert alert-info success-msg d-none"></div>
              <div class="alert alert-danger error-msg d-none"></div>
				  <h5 class="msg"></h5>
              <div class="tr-field-row">
                <h6>Legal name</h6>
                <div class="tr-field tr-first-name-field">
                  <label class="tr-field-label" for="firstName">First name</label>
                  <input type="text" id="firstName" name="first_name" value="">
                  <span class="text-danger first_name_msg"></span>
                </div>
              </div>
              <div class="tr-field-row">
                <div class="tr-field tr-surname-field">
                  <label class="tr-field-label" for="surnameName">Last name</label>
                  <input type="text" id="surnameName" name="last_name" value="">
                  <span class="text-danger last_name_msg"></span>
                  <p>Make sure this matches the name on your government ID</p>
                </div>
              </div>
              <div class="tr-field-row">
                <h6>Current City</h6>
                <div class="tr-field tr-city-field">
                  <label class="tr-field-label" for="search_city" style="margin-bottom: 8px; display: block;">Current town/city</label>
                  <input type="text"  name="ctname" value="" id="search_city" autocomplete="off">
                  <input type="hidden" name='city' id="selected_city_id" class="form-control rounded-3" placeholder="City">
                  <div class="hotel_recent_his search-box-info tr-recent-searchs-modal" id="recentSearchsDestination">
                    <div class="autoCompletewrapper" id="citylist"></div>
                  </div>
                  <span class="text-danger currentCity_msg"></span>
                </div>
              </div>
              <div class="tr-field-row">
                <h6>Contact Info</h6>
                <div class="tr-field tr-email-field">
                  <label class="tr-field-label" for="emailSignUp">Email</label>
                  <input type="email" id="emailSignUp" name="username" value="">
                  <span class="text-danger username_msg"></span>
                </div>
              </div>
              <div class="tr-field-row">
                <div class="tr-field tr-password-field">
                  <label class="tr-field-label" for="passwordSignUp">Password</label>
                  <input type="password" id="passwordSignUp" name="password" value="" pattern=".*[A-Za-z].*" title="Password must contain at least one character">
                  <small class="form-text text-muted">Password must contain at least one character</small>
                  <span class="text-danger password_msg"></span>
                </div>
              </div>
              <div class="tr-field-row">
                <div class="tr-field">
                  <label class="tr-check-box"><input type="checkbox" class="term" id="" name="" value="" ><span class="checkmark"></span>I agree to Travell’s <a href="#">Terms of service</a>, <a href="#">Payments Terms</a> of service and <a href="#">Anti-Discrimination Policy</a> and acknowledge the <a href="#">Privacy Policy</a>.
                  </label>
					<span class="text-danger term_msg"></span>
                </div>
              </div>
              <button type="submit" class="tr-btn">Continue</button></label>
            </form>            
          </div>
                  <style>
                  #search_city {
              margin-top: 8px !important;
            }
            /* Force the city field to maintain its structure */
            .tr-city-field {
              display: flex !important;
              flex-direction: column !important;
            }
            /* Ensure the label always has space below it */
            .tr-city-field .tr-field-label::after {
              content: '' !important;
              display: block !important;
              height: 8px !important;
              width: 100% !important;
            }
          </style>
        </div>
      </div>
    </div>
    <!-- Signup - End-->

    <!-- Login - Start-->
    <div class="modal tr-authentication" id="logInModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <style>
                  #search_city {
              margin-top: 8px !important;
            }
            /* Force the city field to maintain its structure */
            .tr-city-field {
              display: flex !important;
              flex-direction: column !important;
            }
            /* Ensure the label always has space below it */
            .tr-city-field .tr-field-label::after {
              content: '' !important;
              display: block !important;
              height: 8px !important;
              width: 100% !important;
            }
          </style>
          <div class="tr-auth-section">
            <div class="tr-auth-header">
              <h5 class="d-none d-md-block">Login</h5>
              <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#signInModal">Back Button</button>
            </div>
            <form action="" method="post">
            @csrf
              <h3 class="d-block d-sm-block d-md-none">Login</h3>
              <div class="tr-field-row">
                <div class="tr-field tr-password-field">
                  <label class="tr-field-label" for="passwordLogin">Password</label>
                  <input type="password" id="passwordLogin" name="password" value="">
                 
                  <span class="text-danger password-msg ml-3"></span>
                
                </div>
              </div>
              <button type="submit" class="tr-btn">Login</button></label>
            </form>
            <div class="tr-forgot-btn">
              <button type="submit" class="tr-anchor-btn" data-bs-toggle="modal" data-bs-target="#forgotPswdModal" >Forgot Password</button></label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Login - End-->

    <!-- Forgot Password - Start-->
    <div class="modal tr-authentication" id="forgotPswdModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="tr-auth-section">
            <div class="tr-auth-header">
              <h5 class="d-none d-md-block">Forgot Password</h5>
              <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#logInModal">Back Button</button>
            </div>
            <form  action="" method="post">
              <h3 class="d-block d-sm-block d-md-none">Forgot Password</h3>

              <div class="alert alert-info forgot-success-msg d-none"></div>
              <div class="tr-field-row">
                <div class="tr-field tr-email-field">
                  <label class="tr-field-label" for="emailForgotPassword">Email address</label>
                  <input type="email" id="emailForgotPassword" name="" value="">
                  <span class="tr-validation-message"></span>
                </div>
              </div>
              <button type="submit" class="tr-btn button-msg">Email resent link</button></label>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Forgot Password - End-->

    <!-- Forgot Password Alert - Start-->
    <div class="modal tr-authentication" id="forgotPswdResendModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="tr-auth-section">
            <div class="tr-auth-header">
              <h5 class="d-none d-md-block">Forgot Password</h5>
              <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#logInModal">Back Button</button>
            </div>
            <form>
              <h3 class="d-block d-sm-block d-md-none">Forgot Password</h3>
              <div class="alert alert-info">A link to reset password has been sent to abc@gmail.com</div>
              <div class="tr-field-row">
                <div class="tr-field tr-email-field">
                  <label class="tr-field-label" for="emailForgotPassword">Email address</label>
                  <input type="email" id="emailForgotPassword" name="" value="">
                </div>
              </div>
              <button type="submit" class="tr-btn">Re-send link</button></label>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Forgot Password Alert - End-->

    <!-- Reset Password - Start-->
    <div class="modal tr-authentication tr-reset-pswd" id="resetPswdModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="tr-auth-section">
            <div class="tr-auth-header">
              <h5 class="d-none d-md-block">Reset Password</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal">Back Button</button>
            </div>
            @if(session('success'))
                  <div class="alert alert-info">
                      {{ session('success') }}
                  </div>
              @endif
              @if(session('error'))
                  <div class="alert alert-danger">
                      {{ session('error') }}
                  </div>
              @endif
            <form id="reset-password">
              <h3 class="d-block d-sm-block d-md-none">Reset Password</h3>
              <div class="tr-field-row">
                <div class="tr-field tr-password-field">
                  <label class="tr-field-label" for="newPassword">New Password</label>
                  <input type="password" id="newPassword" name="" value="">
                  <input type="hidden" id="token" name="" value="">
                  <span class="password-msg"></span>
                </div>
              </div>
              <div class="tr-field-row">
                <div class="tr-field tr-password-field">
                  <label class="tr-field-label" for="confirmPassword">Confirm Password</label>
                  <input type="password" id="confirmPassword" name="" value="">
                  <span class="confirm-password-msg"></span>
                </div>
              </div>
              <button type="submit" class="tr-btn">Reset</button></label>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Reset Password - End-->  
    
    <script src="{{ asset('/js/sign_in.js')}}"></script>