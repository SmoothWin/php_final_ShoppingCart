<br /><br />
<div class="container" style="width:500px;border: 1px;">
    <h3 align="center" class="text-white">Covid Sanitation Shop</h3>
    <br />
    <h3 align="center" class="text-white">Login</h3>
    <br />
    <form method="post" id="form" action="<?php echo $_SERVER['PHP_SELF'] ?>" class="border rounded border-secondary p-5">
        <label class="text-white">Enter Email</label>
        <input type="email" name="email" class="form-control" />
        <br />
        <label class="text-white">Enter Password</label>
        <input type="password" name="password" class="form-control" />
        <br />
        <input type="submit" name="login" value="Login" class="btn btn-info" />
        <br />
        <p align="center"><a href="login.php?action=register" class="btn btn-primary btn-lg active">Register</a></p>
    </form>