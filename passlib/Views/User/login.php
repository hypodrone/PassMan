<div class="form-wrapper">
    <div class="m-auto">
        <div class="form-title">
            <h2>Welcome to PassMan<br /><small>Please log in.</small></h2>
        </div>
        <form id="login-form" class="login-form" action="/user/auth" method="post">
            <h2 class="error-msg mb-3 font-weight-normal">
            <?php
                \PassMan\Core\Session::showMessage();
            ?>
            </h2>
            <fieldset>
                <div class="form-group">
                    <label for="email" class="">Username (email)</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Your email" required="" autofocus="">
                </div>
            </fieldset>
            <fieldset>
                <div class="form-group">
                    <label for="password" class="">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required="">
                </div>
            </fieldset>
            <button class="btn btn-primary btn-block" type="submit" name="login" value="login">Sign in</button> 
        </form>

        <a class="btn btn-sm btn-secondary btn-block" href="/user/register">Register</a>
        <a class="btn btn-sm btn-secondary btn-block" href="/user/forgot">Forgot your password?</a>

    </div>
</div>