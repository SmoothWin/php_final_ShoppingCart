<?php

include "_dbConnection.php";
session_start();
$isAdmin;
if (!isset($_SESSION["username"])) {
    header("location: login.php");
}
if ($_SESSION["isAdmin"] == false) {
    $isAdmin = false;
} else {
    $isAdmin = true;
}
$search = isset($_SESSION["search"]) ? strip_tags($_SESSION["search"]) : "";
$maximum = isset($_SESSION["maximum"]) ? strip_tags($_SESSION["maximum"]) : "";
$minimum = isset($_SESSION["minimum"]) ? strip_tags($_SESSION["minimum"]) : "";
$sort = isset($_SESSION["sort"]) ? strip_tags($_SESSION["sort"]) : "none";
if (isset($_GET["searching"])) {
    $search = isset($_GET["search"]) ? strip_tags($_GET["search"]) : "";
    $maximum = isset($_GET["maximum"]) ? strip_tags($_GET["maximum"]) : null;
    $minimum = isset($_GET["minimum"]) ? strip_tags($_GET["minimum"]) : null;
    $sort = isset($_GET["sort"]) ? strip_tags($_GET["sort"]) : "none";

    $_SESSION["search"] = $search;
    $_SESSION["maximum"] = $maximum;
    $_SESSION["minimum"] = $minimum;
    $_SESSION["sort"] = $sort;
}

include "searchPref.php";
?>

<?php

if (isset($_POST["add"])) {
    $item = array(
        "item_id"       => $_POST["hidden_id"],
        "item_name"     => $_POST["hidden_name"],
        "item_price"   => $_POST["hidden_price"],
        "item_quantity" => $_POST["quantity"],
    );
    if (isset($_SESSION["shopping_cart"])) {
        $ids = array_column($_SESSION["shopping_cart"], "item_id");
        if (in_array($item["item_id"], $ids)) {
            $key = array_search($item["item_id"], $ids);
            $_SESSION["shopping_cart"][$key]["item_quantity"] = (isset($_SESSION["shopping_cart"][$key]["item_quantity"])) ?
                $_SESSION["shopping_cart"][$key]["item_quantity"] + $_POST["quantity"] : $_POST["quantity"];
        } else {
            array_push($_SESSION["shopping_cart"], $item); //new item added to the shopping cart
        }
    } else {
        $_SESSION["shopping_cart"] = array($item); //1st time we set an item in the shopping cart
    }
}

if (isset($_POST["delete"])) {
    foreach ($_SESSION["shopping_cart"] as $keys => $values) {
        if ($values["item_id"] == $_POST["idForDelete"]) {
            unset($_SESSION["shopping_cart"][$keys]);
            header("location: entry.php?" . searchPref());
        }
    }
}
if (isset($_POST["edit-item"])) {

    $item = array(
        "item_id" => $_POST["hidden_id"],
        "item_name" => $_POST["hidden_name"],
        "item_description" => $_POST["hidden_description"],
        "item_image" => $_POST["hidden_image"],
        "item_price" => $_POST["hidden_price"],
        "item_quantity" => $_POST["hidden_quantity"]
    );
    $_SESSION["admin_edit"] = $item;

    header("location:edit.php");
}
?>

<?php if (isset($search) || isset($maximum) || isset($minimum) || isset($sort)) {
    $sql = "SELECT i.* FROM PRODUCT i";
    if (!empty($search) && !empty($maximum) && !empty($minimum)) {
        $sql .= " WHERE";
        $sql .= " (i.name LIKE :search or i.description LIKE :search)";
        $sql .= " AND (i.price <= :maximum AND i.price >= :minimum)";
    } else if (!empty($search) && !empty($maximum)) {
        $sql .= " WHERE";
        $sql .= " (i.name LIKE :search or i.description LIKE :search)";
        $sql .= " AND i.price < :maximum";
    } else if (!empty($search) && !empty($minimum)) {
        $sql .= " WHERE";
        $sql .= " (i.name LIKE :search or i.description LIKE :search)";
        $sql .= " AND i.price > :minimum";
    } else if (!empty($search)) {
        $sql .= " WHERE";
        $sql .= " i.name LIKE :search or i.description LIKE :search";
    } else if (!empty($maximum) && !empty($minimum)) {
        $sql .= " WHERE";
        $sql .= " i.price <= :maximum";
        $sql .= " AND i.price >= :minimum";
    } else if (!empty($maximum)) {
        $sql .= " WHERE";
        $sql .= " i.price <= :maximum AND i.price >= 0";
    } else if (!empty($minimum)) {
        $sql .= " WHERE";
        $sql .= " i.price >= :minimum";
    }

    if ($sort == "highest") {
        $sql .= " ORDER BY i.price DESC";
    } else if ($sort == "lowest") {
        $sql .= " ORDER BY i.price ASC";
    }

    try {
        $stmt = $connect->prepare($sql);

        (!empty($search)) ? $stmt->bindValue(':search', '%' . $search . '%') : '';
        (!empty($maximum)) ? $stmt->bindValue(':maximum', $maximum) : null;
        (!empty($minimum)) ? $stmt->bindValue(':minimum', $minimum) : null;

        $stmt->execute();

        //PDO::FETCH_NUM
        $stmt->setFetchMode(PDO::FETCH_ASSOC); // set the resulting array to associative
        $connect = null;
    } catch (PDOException $e) {
        echo "<script>alert('" . $e->getMessage() . "')</script>";
    }
}


?>
<!DOCTYPE html>
<html>

<head>
    <title>Covid Store</title>
    <link rel="stylesheet" href="frontPage.css" />
    <?php include "bootstrapHead.php" ?>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light px-5 fixed-top">
        <h3 align="center" class="navbar-header">Covid Shop</h3>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 r-5 col-12 d-flex justify-content-around">

                <li class="nav-item dropdown" id="welcome">
                    <?php
                    echo '<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Welcome - '
                        . $_SESSION["username"] . '</a>';
                    ?>
                    <ul class="dropdown-menu">
                        <li class="dropdown-item">
                            <?php
                            echo '<a class="nav-link text-danger" href="logout.php">Logout</a>';
                            ?>
                        </li>
                    </ul>
                </li>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img width="20px" src="uploads/shoppingCart.png" /> Shopping Cart
                    </a>
                    <ul class="dropdown-menu" id="shopping_cart_list">
                        <li class="dropdown-item">
                            <table class="col-12 table">
                                <tr>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                                <?php
                                if (isset($_SESSION["shopping_cart"])) {
                                    foreach ($_SESSION["shopping_cart"] as $keys => $values) { ?>
                                        <tr>
                                            <td class="col-6"><?php echo isset($values["item_name"]) ? $values["item_name"] : ""; ?></td>
                                            <td class="col-1" align="right"><?php echo isset($values["item_quantity"]) ? $values["item_quantity"] : ""; ?></td>
                                            <td class="col-1"><?php echo isset($values["item_price"]) ? '$ ' . $values["item_price"] : ""; ?></td>
                                            <td class="col-2"><?php echo isset($values["item_price"]) && isset($values["item_quantity"]) ? '$ ' . $values["item_price"] * $values["item_quantity"] : ""; ?></td>
                                            <td class="col-2">
                                                <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
                                                    <input type="hidden" name="idForDelete" value="<?php echo $values["item_id"] ?>">
                                                    <!-- error here when you add then 
                                                add above 1 then delete that item and then u add another item -->
                                                    <input type="submit" name="delete" class="btn btn-success btn-sm" value="Remove">
                                                </form>
                                            </td>
                                        </tr>
                                <?php } //end: foreach
                                } //end: if (isset($_SESSION["shopping_cart"])) {
                                ?>
                            </table>
                        </li>
                    </ul>
                </li>
        </div>
        </li>


        </ul>
        </div>
    </nav>
    <div class="container">
        <div id="wrapper" class="p-5 mt-4">
            <div class="">
                <form method="get" action="<?php echo $_SERVER['PHP_SELF'] ?>" class="d-flex align-items-start flex-column">
                    <div>
                        <h4 class="text-info">Search...</h4>

                        <input style="height:30px;" type="text" name="search" value="<?php echo $search ?>" placeholder="Name..." />
                        <input style="height:30px;" type="submit" style="margin-top:5px;" class="mx-2 btn btn-success btn-sm" name="searching" value="Search" />
                    </div>
                    <div class="mt-2">
                        <span>
                            Max Price:
                            <input style="width:70px;" style="height:30px;" type="number" name="maximum" value="<?php echo $maximum ?>" placeholder="9999">
                        </span>
                        <span>
                            Min Price:
                            <input style="width:50px;" style="height:30px;" type="number" name="minimum" value="<?php echo $minimum ?>" placeholder="0">
                        </span>
                    </div>
                    <div class="mt-2">
                        <span>
                            Sort by:
                            <select style="width:130px;" style="height:30px;" name="sort" id="sort">
                                <option value="highest">Highest Price</option>
                                <option value="lowest">Lowest Price</option>
                                <option value="none">None</option>
                            </select>
                        </span>
                    </div>

                </form>
                <?php
                if ($_SESSION["isAdmin"] == true) {
                ?>
                    <form action="addProduct.php" method="POST" align="right">
                        <input class="btn btn-sm btn-danger" style="height:30px;" type="submit" value="Add Item" />
                    </form>
                <?php
                }
                ?>
            </div>
            <hr>
            <div id="items" class="col-12 mx-auto">
                <?php
                $row = $stmt->fetchAll();
                $searchTitle = (!empty($search))
                    ? sizeof($row) . " searches for \"" . $search . "\"" : "";
                echo "<h3>$searchTitle</h3>";


                foreach ($row as $k => $product) {
                ?>
                    <div class="mx-auto col-10">
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="d-flex">

                            <div id="left" class="bg-light col-3 p-1 pb-3" align="center">
                                <h4><?php echo $product["name"] ?></h4>
                                <?php if (!is_null($product['image'])) {
                                    echo '<img style="width:80%;" class="border border-secondary rounded" src="data:image/jpeg;base64,' . base64_encode($product['image']) . '"style="width:200px; height: 200px;"/><br />';
                                } ?>
                                <h4>$<?php echo $product["price"] ?></h4>
                                <h4>Quantity left: <?php echo $product["quantity"] ?></h4>
                                <label for="quantity">Quantity</label> <input class="col-4" type="number" name="quantity" value="1" /><br>
                                <input type="hidden" name="hidden_id" value="<?php echo $product["id"] ?>" />
                                <input type="hidden" name="hidden_name" value="<?php echo $product["name"] ?>" />
                                <input type="hidden" name="hidden_price" value="<?php echo $product["price"] ?>" />
                                <input type="hidden" name="hidden_description" value="<?php echo $product['description'] ?>" />
                                <input type="hidden" name="hidden_quantity" value="<?php echo $product['quantity'] ?>" />
                                <input type="hidden" name="hidden_image" value="<?php if (!is_null($product['image'])) {
                                                                                    echo base64_encode($product['image']);
                                                                                }  ?>" />
                                <input class="btn btn-primary btn-sm active mt-3" type="submit" name="add" value="Add to Cart" />
                                <?php
                                if ($isAdmin == true) {
                                ?>
                                    <input type="submit" class="btn btn-danger btn-sm active mt-3" name="edit-item" value="edit" />
                                <?php
                                }
                                ?>

                            </div id="right">
                            <div>
                                <br><br>
                                <p class="px-4"><b>Description:</b> <br> <?php echo $product["description"] ?></p>
                            </div>

                        </form>

                    </div>
                    <hr>
                <?php
                }
                ?>

            </div>


        </div>
        <script>
            document.getElementById('sort').value = "<?php echo $sort ?>";
        </script>
        <?php include "bootstrapBody.php" ?>
</body>

</html>