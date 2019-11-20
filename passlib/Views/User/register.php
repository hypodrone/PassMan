<div class="form-wrapper">
    <div class="m-auto">
        <div class="form-title">
            <h2>PassMan registration form<br /><small>Please fill in your details.</small></h2>
        </div>
        <form class="login-form" action="/user/reguser" method="post">
            <h2 class="error-msg mb-3 font-weight-normal">
            <?php
                \Passlib\Core\Session::showMessage();
            ?>
            </h2>
            <fieldset>
                <div class="form-group">
                    <label for="user" class="">Username (email)*</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Your email*" required="" autofocus="">
                </div>
                <div class="form-group">
                    <label for="pass" class="">Password*</label>
                    <input type="password" id="pass" name="pass" class="form-control" placeholder="Password*" required="">
                </div>
                <div class="form-group">
                    <label for="passconf" class="">Confirm your password*</label>
                    <input type="password" id="passconf" name="passconf" class="form-control" placeholder="Confirm Password" required="">
                </div>
            </fieldset>
            <fieldset>
                <div class="form-group">
                    <label for="firstname" class="">Firstname* and Surname</label>
                    <input type="text" id="firstname" name="firstname" class="form-control mb-2" placeholder="Firstname*" required="">
                    <label for="surname" class="sr-only">Surname</label>
                    <input type="text" id="surname" name="surname" class="form-control" placeholder="Surname">
                </div>
            </fieldset>    
            <button class="btn btn-primary btn-block" type="submit" name="regme" value="regme">Register me</button>     
            <a class="btn btn-sm btn-secondary btn-block" href="/">Log in</a>
        </form>
    </div>
</div>