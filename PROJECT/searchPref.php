<?php
function searchPref()
{
    $s = (isset($_SESSION["search"])) ? $_SESSION["search"] : null;
    $max = (isset($_SESSION["maximum"])) ? $_SESSION["maximum"] : null;
    $min = (isset($_SESSION["minimum"])) ? $_SESSION["minimum"] : null;
    $so = (isset($_SESSION["sort"])) ? $_SESSION["sort"] : null;

    return "?search=" . $s . "&maximum=" . $max .
        "&minimum=" . $min . "&sort=" . $so;
}
