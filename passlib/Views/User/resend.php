<div class="form-wrapper">
    <div class="m-auto">
        <div class="form-title">
            <h2>PassMan password reset<br /><small>Please enter your log in.</small></h2>
        </div>
        <form id="login-form" class="login-form" action="/user/resend" method="post">
            <h2 class="error-msg mb-3 font-weight-normal">
            <?php
                \PassMan\Core\Session::showMessage();
            ?>
            </h2>
            <fieldset>
                <div class="form-group">
                    <label for="email" class="">Input your Username*</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Your email*" required="" autofocus="">
                </div>
            </fieldset>
            <button class="btn btn-primary btn-block" type="submit" name="resendpass" value="resendpass">Reset password</button>
        </form>
        <a class="btn btn-sm btn-secondary btn-block" href="/user/register">Register</a>
        <a class="btn btn-sm btn-secondary btn-block" href="/">Log in</a>
    </div>
</div>