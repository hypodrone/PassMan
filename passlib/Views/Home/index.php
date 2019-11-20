<section class="h-100">
    <div class="container-fluid h-100 d-flex flex-column">
        <div class="row">
            <div class="col-12">
                <nav class="navbar bg-dark navbar-dark navbar-expand-sm">
                    <span class="navbar-text">
                        <h1><a href="/">PassMan</a></h1>
                        <span class="username">
                            <?php
                                echo \Passlib\Core\Session::get("user_email");
                            ?>
                        </span>
                    </span>    
                    <span class="ml-auto">
                        <a class="btn btn-primary" href="/user/logout">Logout</a>
                    </span>
                </nav>
            </div>
        </div>
        <div class="row h-100 flex-fill">
            <div class="col-sm-9 order-sm-2 content-data">
                <div class="error-msg font-weight-normal">
                    <?php
                        \Passlib\Core\Session::showMessage();
                    ?>
                </div>
                <h3>Add new password:</h3>
                
                <form class="form add-password" role="form" action="/home/add" method="post">
                    <div class="row">
                        <div class="col-sm-5">
                            <label for="service" class="sr-only">Service</label>
                            <input type="text" id="service" name="service" class="form-control" placeholder="Service" required="" autofocus="" autocomplete="new-password">
                        </div>
                        <div class="col-sm-5">
                            <label for="password" class="sr-only">Password</label>
                            <div class="password-group"><input type="password" id="password" 
                                        name="password" class="form-control password-box" placeholder="Password" autocomplete="new-password">
                                <a href="#!" class="password-visibility"><i class="fa fa-eye"></i></a>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-primary" type="submit" name="add" value="add">Add</button>
                        </div>
                    </div>
                </form>
                <h3 class="mt-5">Stored passwords:</h3>
                <?php 
                    $passwords_qty = count($model['pass']);
                    if ( $passwords_qty == 0 ):
                ?>
                    <p><strong>No passwords on this page :(</strong></p>
                <?php
                    else:
                ?>
                <p>Here are your stored passwords:</p>
                <?php foreach ($model['pass'] as $row): ?>
                    <form class="form password-row" role="form" action="/home/modify" method="post">
                        <div class="row">
                            <div class="col-sm-6 col-md-4">
                                <label for="service" class="sr-only">Service</label>
                                <input type="text" id="service<?php echo $row['id']; ?>" name="service" class="form-control" 
                                            value="<?php echo $row['service']; ?>">
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <label for="password" class="sr-only">Password</label>
                                <div class="password-group">
                                    <input type="password" id="password<?php echo $row['id']; ?>" 
                                            name="password" class="form-control password-box" value="<?php echo $row['srvpsswd']; ?>">
                                    <a href="#!" class="password-visibility"><i class="fa fa-eye"></i></a>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-4">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button class="btn btn-primary" name="modify" value="update" type="submit">Update</button>
                                <button class="btn btn-primary" name="modify" value="delete" type="submit" onclick="return confirm('Are you sure?');">Delete</button>
                            </div>
                        </div>
                        </fieldset>
                        </form>
                <?php endforeach ?>
                <?php 
                    endif;
                ?>
                <div class="pagination-wrap">
                    <?php
                        echo \Passlib\Helpers\Pagination::showPagination($model['stats']['total'],$model['other']['current'],$model['other']['rows_pp']);
                    ?>
                </div>
            </div>
            <div class="col-sm-3 order-sm-1 content-stats">
                <h3>Statistics</h3> 
                <p>You have <?php echo $pass_qty = $model['stats']['total']; echo " password" . ($pass_qty == 1 ? "" : "s ");?> set.</p>
                <p>There <?php $users_qty = $model['stats']['users_qty']; echo $users_qty == 1 ? " is $users_qty user " : " are $users_qty users ";?> registered.</p>
                
                <?php if( substr(\Passlib\Core\Session::get("user_last_login_date"), 0, 4) != "1970" ) : ?>
                <hr>
                <p>Your last login:<br>
                <?php
                    echo \Passlib\Core\Session::get("user_last_login_date");
                ?>
                <br>
                <?php
                    echo \Passlib\Core\Session::get("user_last_login_time");
                ?>
                </p>
                <?php endif; ?>
                
                <hr>
                <!-- This needs to be a form to pass post variable rather than by get and prevent deleting user by typing in url directly! -->
                <form id="login-form" class="login-form" action="/user/delete" method="post">
                    <button class = "btn btn-danger btn-sm" name="delete" value="delete" type = "submit" onclick="return confirm('Are you sure?');">Delete me!</button>   
                </form>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript" src="/assets/scripts/showpass.js"></script>