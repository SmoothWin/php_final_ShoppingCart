<?php
session_start();
include "searchPref.php";
if ($_SESSION["isAdmin"] == false) {
    header("location: entry.php" . searchPref());
}
include "_dbConnection.php";
?>
<?php
if (isset($_POST["add"])) {

    if ($_FILES["image"]["error"] > 0) {
        $image = null;
    } else {
        $image = isset($_FILES["image"]["tmp_name"]) ? file_get_contents($_FILES['image']['tmp_name']) : null;
    }

    $name = isset($_POST["name"]) ? trim(strip_tags($_POST["name"])) : null;
    $description = isset($_POST["description"]) ? trim(strip_tags($_POST["description"])) : null;
    $quantity = isset($_POST['quantity']) ? trim(strip_tags($_POST['quantity'])) : null;
    $price = isset($_POST['price']) ? trim(strip_tags($_POST['price'])) : null;
    $sql =
        "INSERT INTO PRODUCT(name,description,quantity,price,image) VALUES(:name,:description,:quantity,:price,:image)";

    try {
        $stmt = $connect->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image', $image);

        $stmt->execute();
        header("location: entry.php" . searchPref());
    } catch (PDOException $e) {
        echo "<script>alert('" . $e->getMessage() . "')</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <label>Name</label><br>
        <input type="text" name="name"><br>
        <label>Image</label><br>
        <input type="file" name="image" accept="image/*" /><br>
        <label>Description</label><br>
        <textarea name="description" id="" cols="30" rows="10"></textarea><br>
        <label>Storage Quantity</label><br>
        <input type="number" name="quantity" /><br>
        <label>Price</label><br>
        <input type="text" name="price" /><br>
        <input type="submit" name="add" />
    </form>
    <a href="entry.php<?php echo searchPref(); ?>">Go back</a>

</body>

</html>