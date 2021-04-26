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
    <?php include "bootstrapHead.php" ?>
    <link rel="stylesheet" href="frontPage.css" />
    <title>Add Item</title>
</head>

<body>
    <h3 align="center" class="text-white">Covid Sanitation Shop</h3>
    <br />
    <h3 align="center" class="text-white">Add Item</h3>
    <br />
    <div class="container" style="width:500px;border: 1px;">
        <div id="wrapper" class="border bg-light rounded border-secondary p-5" align="center">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <label>Name</label><br>
                <input type="text" name="name"><br>
                <label>Image</label><br>
                <input type="file" name="image" accept="image/*" class="btn border border-primary btn-sm" /><br>
                <label>Description</label><br>
                <textarea name="description" id="" cols="30" rows="10"></textarea><br>
                <label>Storage Quantity</label><br>
                <input type="number" name="quantity" /><br>
                <label>Price</label><br>
                <input type="text" name="price" /><br>
                <br>
                <input type="submit" name="add" />
            </form>
            <br>
            <a href="entry.php<?php echo searchPref(); ?>" class="btn btn-secondary btn-lg border border-dark">Go back</a>
        </div>
    </div>
</body>

</html>