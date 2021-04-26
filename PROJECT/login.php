<?php require '_dbConnection.php' ?>
<?php
session_start();
include "searchPref.php";
if (isset($_SESSION["username"])) {
    header("location: entry.php" . searchPref());
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Covid Sanitation Shop</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="frontPage.css" />
    <?php include "bootstrapHead.php" ?>
</head>

<body>
    <div id="wrapper_start">
        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'register') {
            if ($connect != "error") {
                include 'registration_form.php';
                if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])) {

                    $username = isset($_POST['username']) ? strip_tags($_POST['username']) : null;
                    $password = isset($_POST['password']) ? strip_tags($_POST['password']) : null;
                    $firstname = isset($_POST['firstname']) ? strip_tags($_POST['firstname']) : null;
                    $lastname = isset($_POST['lastname']) ? strip_tags($_POST['lastname']) : null;
                    $email = isset($_POST['email']) ? strip_tags($_POST['email']) : null;

                    $password = hash("sha256", $password);

                    try {

                        $query = "SELECT * FROM USER WHERE email = :email";
                        $stmt = $connect->prepare($query);
                        $stmt->bindValue(':email', $email);
                        $stmt->execute();

                        $stmt->setFetchMode(PDO::FETCH_ASSOC);
                        $row = $stmt->fetch();

                        if (empty($row)) {
                            $query = "INSERT INTO USER(username,firstname,lastname,email,password) VALUES (?,?,?,?,?)";
                            $stmt = $connect->prepare($query);
                            $stmt->execute([$username, $firstname, $lastname, $email, $password]);
                            $_SESSION['username'] = $row["username"];
                            $_SESSION['isAdmin'] = $row["admin"];
                            header('location: entry.php' . searchPref());
                        }
                        if (!empty($row)) {
                            echo "<script>alert('user with this email address already exists.')</script>";
                        }
                    } catch (PDOException $err) {
                        echo "<script>alert('" . $err->getMessage() . "')</script>";
                    } catch (Exception $e) {
                        echo "<script>alert('" . $e->getMessage() . "')</script>";
                    }
                    $connect = null;
                }
            }
        } else if (!isset($_GET['action'])) {
            if ($connect != "error") {
                include 'login_form.php';
                if ((isset($_POST['email']) && isset($_POST['password']))) {
                    $email = (!is_null($_POST['email'])) ? strip_tags($_POST['email']) : null;
                    $password = (!is_null($_POST['password'])) ? strip_tags($_POST['password']) : null;

                    $password = hash("sha256", $password);
                    try {
                        $query = "SELECT * FROM USER WHERE email=? AND password=?";
                        $stmt = $connect->prepare($query);
                        $stmt->execute([$email, $password]);

                        //PDO::FETCH_NUM
                        $stmt->setFetchMode(PDO::FETCH_ASSOC);
                        $row = $stmt->fetch();
                        if (empty($row))
                            echo "<script>alert('credentials entered are invalid')</script";
                        if (!empty($row)) {
                            $_SESSION["username"] = $row["username"];
                            $_SESSION['isAdmin'] = $row["admin"];
                            header("location: entry.php" . searchPref());
                        }
                    } catch (PDOException $err) {
                        echo "<script>alert('" . $err->getMessage() . "')</script>";
                    }

                    $connect = null;
                }
            }
        }
        ?>

    </div>
    </div>
    <?php include "bootstrapBody.php" ?>

</body>

</html>