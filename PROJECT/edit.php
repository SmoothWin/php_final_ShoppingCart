<?php
session_start();
include "searchPref.php";
if ($_SESSION["isAdmin"] == false) {
    header("location: entry.php?" . searchPref());
}
include "_dbConnection.php";
?>
<?php
if (isset($_POST['debug'])) {
    echo $_SESSION["admin_edit"]["item_image"];
}
if (isset($_POST["delete"])) {
    $id = strip_tags($_SESSION["admin_edit"]["item_id"]);

    $sql =
        "DELETE FROM PRODUCT WHERE id = :id";

    try {
        $stmt = $connect->prepare($sql);

        $stmt->bindParam(':id', $id);

        $stmt->execute();
        header("location: entry.php?" . searchPref());
    } catch (PDOException $e) {
        echo "<script>alert('" . $e->getMessage() . "')</script>";
    }
}
if (isset($_POST["edit"])) {
    if ($_FILES["image"]["error"] > 0) {
        $image = base64_decode($_SESSION["admin_edit"]["item_image"]);
    } else {
        $image = isset($_FILES["image"]["tmp_name"]) ? file_get_contents($_FILES['image']['tmp_name']) : null;
    }
    $name = trim(strip_tags($_POST["name"]));
    $description = trim(strip_tags($_POST["description"]));
    $quantity = trim(strip_tags($_POST['quantity']));
    $price = trim(strip_tags($_POST['price']));

    $id = strip_tags($_SESSION["admin_edit"]["item_id"]);

    $sql =
        "UPDATE PRODUCT SET name = :name, description = :description,
         quantity = :quantity, price = :price, image = :image WHERE id = :id";

    try {
        $stmt = $connect->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        header("location: entry.php?" . searchPref());
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
        <input type="text" name="name" value="<?php echo $_SESSION["admin_edit"]["item_name"] ?>"><br>
        <label>Image</label><br>

        <?php if (!empty($_SESSION["admin_edit"]["item_image"])) {
            echo '<img src="data:image/jpeg;base64,' . $_SESSION["admin_edit"]["item_image"] . '"style="width:200px; height: 200px;"/><br />';
        } ?>
        <input type="file" name="image" accept="image/*" /><br>
        <label>Description</label><br>
        <textarea name="description" id="" cols="30" rows="10"><?php echo $_SESSION["admin_edit"]["item_description"] ?>
        </textarea><br>
        <label>Storage Quantity</label><br>
        <input type="number" name="quantity" value="<?php echo $_SESSION["admin_edit"]["item_quantity"] ?>" /><br>
        <label>Price</label><br>
        <input type="text" name="price" value="<?php echo $_SESSION["admin_edit"]["item_price"] ?>" /><br>
        <input type="submit" name="edit" value="Save changes" />
        <input type="submit" name="delete" value="Remove Item" />
    </form>
    <a href="entry.php<?php echo searchPref(); ?>">Go back</a>

</body>

</html>