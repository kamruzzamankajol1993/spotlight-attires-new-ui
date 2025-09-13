<div class="offcanvas offcanvas-end" tabindex="-1" id="signInOffcanvas" aria-labelledby="signInOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="signInOffcanvasLabel">Sign in</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        {{-- General Error Message Area --}}
        <div id="auth-error-message" class="alert alert-danger" style="display: none;"></div>

        {{-- Login Form --}}
        <form id="loginForm" novalidate>
             @csrf
            <div class="mb-3">
                <label for="loginEmail" class="form-label">Email Address *</label>
                <input type="email" class="form-control" name="email" id="loginEmail" required>
                <div class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="loginPassword" class="form-label">Password *</label>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" id="loginPassword" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="invalid-feedback"></div>
            </div>
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-dark">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                    Log In
                </button>
            </div>
             <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check"><input class="form-check-input" type="checkbox" id="rememberMe"><label class="form-check-label" for="rememberMe">Remember me</label></div>
                <a href="#" class="text-decoration-none text-dark" id="forgotPasswordLink">Lost your password?</a>
            </div>
        </form>

        {{-- NEW Forgot Password Form --}}
        <form id="forgotPasswordForm" style="display: none;" novalidate>
            @csrf
            <p>Lost your password? Please enter your email address. You will receive a link to create a new password via email.</p>
            <div class="mb-3">
                <label for="forgotEmail" class="form-label">Email Address *</label>
                <input type="email" class="form-control" name="email" id="forgotEmail" required>
                <div class="invalid-feedback"></div>
            </div>
             <div class="d-grid mb-3">
                <button type="submit" class="btn btn-dark">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                    Reset Password
                </button>
            </div>
             <a href="#" class="text-decoration-none text-dark" id="backToLoginLink">&larr; Back to Login</a>
        </form>

        {{-- Registration Form --}}
<form id="registerForm" style="display: none;" novalidate>
     @csrf
    <div class="mb-3"><label for="registerName" class="form-label">Full Name *</label><input type="text" class="form-control" id="registerName" name="name" required><div class="invalid-feedback"></div></div>
  
    <div class="mb-3"><label for="registerEmail" class="form-label">Email *</label><input type="email" class="form-control" id="registerEmail" name="email" required><div class="invalid-feedback"></div></div>
    <div class="mb-3"><label for="registerPhone" class="form-label">Phone *</label><input type="tel" class="form-control" id="registerPhone" name="phone" required><div class="invalid-feedback"></div></div>
    <div class="mb-3"><label for="registerPassword" class="form-label">Password *</label><div class="input-group"><input type="password" class="form-control" id="registerPassword" name="password" required><button class="btn btn-outline-secondary toggle-password" type="button"><i class="bi bi-eye"></i></button></div><div class="invalid-feedback"></div></div>
    <div class="mb-3"><label for="confirmPassword" class="form-label">Confirm Password *</label><input type="password" class="form-control" id="confirmPassword" name="password_confirmation" required><div class="invalid-feedback"></div></div>
   
    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-dark">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
            Register
        </button>
    </div>
</form>
        
        {{-- OTP Verification Form --}}
        <form id="otpForm" style="display: none;" novalidate>
            @csrf
            <p class="text-center">A 6-digit verification code has been sent to your phone. Please enter it below.</p>
            <div class="d-flex justify-content-center gap-2 mb-3" id="otp-inputs">
                <input type="text" class="form-control form-control-lg text-center otp-input" maxlength="1" required>
                <input type="text" class="form-control form-control-lg text-center otp-input" maxlength="1" required>
                <input type="text" class="form-control form-control-lg text-center otp-input" maxlength="1" required>
                <input type="text" class="form-control form-control-lg text-center otp-input" maxlength="1" required>
                <input type="text" class="form-control form-control-lg text-center otp-input" maxlength="1" required>
                <input type="text" class="form-control form-control-lg text-center otp-input" maxlength="1" required>
            </div>
             <div class="invalid-feedback d-block text-center mb-3"></div>
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-dark">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                    Verify OTP
                </button>
            </div>
            <div class="text-center">
                <p class="small">Didn't receive the code? <a href="#" id="resendOtpLink">Resend OTP</a></p>
                <span id="resend-loader" class="spinner-border spinner-border-sm" role="status" style="display: none;"></span>
            </div>
        </form>

        <div id="authToggleSection" class="text-center">
            <i class="bi bi-person-circle fs-1 text-secondary"></i>
            <p class="mt-3" id="toggleText">No account yet?</p>
            <a href="#" class="btn btn-outline-dark rounded-pill px-4" id="toggleFormBtn">Create An Account</a>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>

    function previewImage(event) {
        const imagePreview = document.getElementById('imagePreview');
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.src = '#';
            imagePreview.style.display = 'none';
        }
    }
</script>
<script>
// NEW DYNAMIC AUTH SCRIPT
$(document).ready(function() {
    const offcanvasEl = document.getElementById('signInOffcanvas');
    const offcanvasTitle = $('#signInOffcanvasLabel');
    const loginForm = $('#loginForm');
    const registerForm = $('#registerForm');
    const otpForm = $('#otpForm');
    const forgotPasswordForm = $('#forgotPasswordForm'); // New form
    const forgotPasswordLink = $('#forgotPasswordLink'); // New link
    const backToLoginLink = $('#backToLoginLink'); // New link
    const toggleFormBtn = $('#toggleFormBtn');
    const toggleText = $('#toggleText');
    const authToggleSection = $('#authToggleSection');
    const authErrorDiv = $('#auth-error-message');

    // --- Helper Functions ---
    function clearErrors() {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        authErrorDiv.hide().text('');
    }

    function showLoader(form) {
        form.find('button[type="submit"] .spinner-border').show();
        form.find('button[type="submit"]').prop('disabled', true);
    }

    function hideLoader(form) {
        form.find('button[type="submit"] .spinner-border').hide();
        form.find('button[type="submit"]').prop('disabled', false);
    }

    function displayErrors(errors) {
        clearErrors();
        $.each(errors, function(field, messages) {
            const input = $(`#${field}, [name="${field}"]`).first();
            input.addClass('is-invalid');
            input.closest('.mb-3').find('.invalid-feedback').text(messages[0]);
        });
    }

    // --- Form Toggling Logic ---
    toggleFormBtn.on('click', function(e) {
        e.preventDefault();
        clearErrors();
        if (loginForm.is(':visible')) {
            loginForm.hide();
            registerForm.show();
            offcanvasTitle.text('Register');
            toggleText.text('Already have an account?');
            toggleFormBtn.text('Sign In');
        } else {
            registerForm.hide();
            loginForm.show();
            offcanvasTitle.text('Sign in');
            toggleText.text('No account yet?');
            toggleFormBtn.text('Create An Account');
        }
    });

    // --- NEW: Toggle to Forgot Password Form ---
    forgotPasswordLink.on('click', function(e) {
        e.preventDefault();
        clearErrors();
        loginForm.hide();
        authToggleSection.hide();
        forgotPasswordForm.show();
        offcanvasTitle.text('Reset Password');
    });

    // --- NEW: Toggle back to Login Form ---
    backToLoginLink.on('click', function(e) {
        e.preventDefault();
        clearErrors();
        forgotPasswordForm.hide();
        loginForm.show();
        authToggleSection.show();
        offcanvasTitle.text('Sign in');
    });

    
    

    // --- Password Toggle ---
    $('.toggle-password').on('click', function() {
        const input = $(this).prev('input');
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    // --- Login Form Submission ---
    loginForm.on('submit', function(e) {
        e.preventDefault();
        clearErrors();
        showLoader($(this));
        
        $.ajax({
            url: '{{ route("customer.login") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect_url;
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    displayErrors(xhr.responseJSON.errors);
                } else {
                    authErrorDiv.text(xhr.responseJSON.message || 'An unexpected error occurred.').show();
                }
            },
            complete: function() {
                hideLoader(loginForm);
            }
        });
    });

    // --- Registration Form Submission ---
    registerForm.on('submit', function(e) {
        e.preventDefault();
        clearErrors();
        showLoader($(this));
        
        const formData = new FormData(this);

        $.ajax({
            url: '{{ route("customer.register") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    registerForm.hide();
                    otpForm.show();
                    authToggleSection.hide();
                    offcanvasTitle.text('Verify Your Account');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    displayErrors(xhr.responseJSON.errors);
                } else {
                     authErrorDiv.text(xhr.responseJSON.message || 'An unexpected error occurred.').show();
                }
            },
            complete: function() {
                hideLoader(registerForm);
            }
        });
    });

     // --- NEW: Forgot Password Form Submission ---
    forgotPasswordForm.on('submit', function(e) {
        e.preventDefault();
        clearErrors();
        showLoader($(this));

        $.ajax({
            url: '{{ route("password.email") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success){
                    forgotPasswordForm.hide();
                    authErrorDiv.removeClass('alert-danger').addClass('alert-success').text(response.message).show();
                    setTimeout(() => {
                        $('#backToLoginLink').click();
                         authErrorDiv.removeClass('alert-success').addClass('alert-danger').hide();
                    }, 5000);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    displayErrors(xhr.responseJSON.errors);
                } else {
                    authErrorDiv.text(xhr.responseJSON.message || 'An unexpected error occurred.').show();
                }
            },
            complete: function() {
                hideLoader(forgotPasswordForm);
            }
        });
    });

    // --- OTP Form Logic ---
    const otpInputs = $('.otp-input');
    otpInputs.on('keyup', function(e) {
        if (e.key >= 0 && e.key <= 9) {
            $(this).next('.otp-input').focus();
        } else if (e.key === 'Backspace') {
            $(this).prev('.otp-input').focus();
        }
    });

    otpForm.on('submit', function(e) {
        e.preventDefault();
        clearErrors();
        showLoader($(this));

        let otp = '';
        otpInputs.each(function() {
            otp += $(this).val();
        });

        $.ajax({
            url: '{{ route("customer.verifyOtp") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                otp: otp
            },
            success: function(response) {
                 if (response.success) {
                    window.location.href = response.redirect_url;
                }
            },
            error: function(xhr) {
                 otpForm.find('.invalid-feedback').text(xhr.responseJSON.message || 'Verification failed.');
            },
            complete: function() {
                hideLoader(otpForm);
            }
        });
    });

    // --- Resend OTP Logic ---
    $('#resendOtpLink').on('click', function(e) {
        e.preventDefault();
        $('#resend-loader').show();
        $(this).hide();

        $.ajax({
            url: '{{ route("customer.resendOtp") }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 3000
                });
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON.message });
            },
            complete: function() {
                $('#resend-loader').hide();
                $('#resendOtpLink').show();
            }
        });
    });
});
</script>