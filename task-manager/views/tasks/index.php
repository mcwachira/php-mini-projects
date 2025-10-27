<?php
$page = $_GET['page'] ?? 'add';

include '../../includes/header.php';
switch ($page) {
    case 'add':
        include './add.php';
        break;

    case 'edit':
        include './edit.php';
        break;

    case 'delete':
        include './delete.php';
        break;
    default:
        # code...
        break;
}

include '../../includes/footer.php';
