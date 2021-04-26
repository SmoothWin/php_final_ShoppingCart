<br /><br />
<div class="container" style="width:500px;border: 1px;">
    <h3 align="center" class="text-white">Covid Sanitation Shop</h3>
    <br />
    <h3 align="center" class="text-white">Registration</h3>
    <br />
    <form method="post" id="form" action="<?php echo $_SERVER['PHP_SELF'] ?>?action=register" class="border rounded border-secondary p-5">
        <label class="text-white">Enter Username</label>
        <input type="text" name="username" class="form-control" />
        <br />
        <label class="text-white">Enter Password</label>
        <input type="password" name="password" class="form-control" />
        <br />
        <input type="text" name="firstname" placeholder="Firstname..." class="form-control" />
        <br />
        <input type="text" name="lastname" placeholder="Lastname..." class="form-control" />
        <br />
        <input type="email" name="email" placeholder="Email..." class="form-control" />
        <br />
        <input type="submit" name="sign_up" value="Sign Up" class="btn btn-info" />
        <br />
        <p align="center"><a href="login.php" class="btn btn-primary btn-lg active">Login</a></p>
    </form>