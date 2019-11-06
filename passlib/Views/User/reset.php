<div class="form-wrapper">
    <div class="m-auto">
        <div class="form-title">
            <h2>PassMan password reset<br /><small>Please provide your new password.</small></h2>
        </div>
        <form id="login-form" class="login-form" action="/user/resetpass" method="post">
            <h2 class="error-msg mb-3 font-weight-normal">
            <?php
                \PassMan\Core\Session::showMessage();
            ?>
            </h2>
            <fieldset>
                <div class="form-group">
                    <label for="password" class="">Password</label>
                    <input type="password" id="respassword" name="respassword" class="form-control" placeholder="Password" required="">
                </div>
            </fieldset>
            <fieldset>
                <div class="form-group">
                    <label for="respasswordconf" class="">Confirm your password</label>
                    <input type="password" id="respasswordconf" name="respasswordconf" class="form-control" placeholder="Confirm Password" required="">
                </div>
            </fieldset>

            <button class="btn btn-primary btn-block" type="submit" name="resetpass" value="resetpass">Reset password</button>
        </form>
        <a class="btn btn-sm btn-secondary btn-block" href="/user/register">Register</a>
        <a class="btn btn-sm btn-secondary btn-block" href="/">Log in</a>
    </div>
</div>