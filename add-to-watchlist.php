<?php
session_start();

if (!isset($_SESSION['watchlist'])) {
    $_SESSION['watchlist'] = [];
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (!in_array($id, $_SESSION['watchlist'])) {
        $_SESSION['watchlist'][] = $id;
    }
}

header("Location: watchlist.php");
exit;
