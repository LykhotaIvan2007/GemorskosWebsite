<?php

declare(strict_types=1);


error_reporting(E_ALL);
ini_set('display_errors', '1');

ob_start();
session_start();


function getDb(): PDO
{
    return new PDO(
        "mysql:host=localhost;port=3306;dbname=gemorskos;charset=utf8mb4",
        "root",
        "root",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
}


function downloadExcel(string $mode, string $term = ''): void
{
    
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    try {
        $db = getDb();

        if ($mode === 'search' && trim($term) !== '') {
            $stmt = $db->prepare("
                SELECT client_name, client_company, client_phone, client_email
                FROM clients
                WHERE client_name    LIKE :wd
                   OR client_company LIKE :wd
                   OR client_phone   LIKE :wd
                   OR client_email   LIKE :wd
                ORDER BY client_name
            ");
            $stmt->bindValue(':wd', '%' . $term . '%', PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $stmt = $db->query("
                SELECT client_name, client_company, client_phone, client_email
                FROM clients
                ORDER BY client_name
            ");
        }

        $filename = 'clients_' . date('Y-m-d_H-i-s') . '.xls';
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        echo "\xEF\xBB\xBF";

        echo "<table border='1'>";
        echo "<tr>
                <th>Name</th>
                <th>Company name</th>
                <th>Phone</th>
                <th>Email</th>
              </tr>";

        while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>" . htmlspecialchars((string)$row['client_name']) . "</td>
                    <td>" . htmlspecialchars((string)$row['client_company']) . "</td>
                    <td>" . htmlspecialchars((string)$row['client_phone']) . "</td>
                    <td>" . htmlspecialchars((string)$row['client_email']) . "</td>
                  </tr>";
        }

        echo "</table>";
        exit;

    } catch (Exception $ex) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Download error: " . $ex->getMessage();
        exit;
    }
}


function createTable(): void
{
    try {
        $db = getDb();
        $stmt = $db->query("
            SELECT client_name, client_company, client_phone, client_email
            FROM clients
            ORDER BY client_name
        ");

        while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>" . htmlspecialchars((string)$row['client_name']) . "</td>
                    <td>" . htmlspecialchars((string)$row['client_company']) . "</td>
                    <td>" . htmlspecialchars((string)$row['client_phone']) . "</td>
                    <td>" . htmlspecialchars((string)$row['client_email']) . "</td>
                  </tr>";
        }
    } catch (Exception $ex) {
        echo "<tr><td colspan='4'>Error: " . htmlspecialchars($ex->getMessage()) . "</td></tr>";
    }
}

function searchTable(string $word): void
{
    $word = trim($word);
    if ($word === '') {
        createTable();
        return;
    }

    try {
        $db = getDb();
        $stmt = $db->prepare("
            SELECT client_name, client_company, client_phone, client_email
            FROM clients
            WHERE client_name    LIKE :wd
               OR client_company LIKE :wd
               OR client_phone   LIKE :wd
               OR client_email   LIKE :wd
            ORDER BY client_name
        ");
        $stmt->bindValue(':wd', "%{$word}%", PDO::PARAM_STR);
        $stmt->execute();

        $any = false;
        while ($row = $stmt->fetch()) {
            $any = true;
            echo "<tr>
                    <td>" . htmlspecialchars((string)$row['client_name']) . "</td>
                    <td>" . htmlspecialchars((string)$row['client_company']) . "</td>
                    <td>" . htmlspecialchars((string)$row['client_phone']) . "</td>
                    <td>" . htmlspecialchars((string)$row['client_email']) . "</td>
                  </tr>";
        }

        if (!$any) {
            echo "<tr><td colspan='4'>No results</td></tr>";
        }
    } catch (Exception $ex) {
        echo "<tr><td colspan='4'>Error: " . htmlspecialchars($ex->getMessage()) . "</td></tr>";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'download') {
        $typed = trim((string)($_POST['searchName'] ?? ''));

        $mode = $_SESSION['mode'] ?? 'all';
        $term = (string)($_SESSION['search_term'] ?? '');

        if ($typed !== '') {
            $mode = 'search';
            $term = $typed;
        }

        downloadExcel($mode, $term);
    }

    if ($action === 'show') {
        $_SESSION['mode'] = 'all';
        unset($_SESSION['search_term']);
        header("Location: check.php");
        exit;
    }

    if ($action === 'search') {
        $q = trim((string)($_POST['searchName'] ?? ''));

        if ($q === '') {
            $_SESSION['mode'] = 'all';
            unset($_SESSION['search_term']);
        } else {
            $_SESSION['mode'] = 'search';
            $_SESSION['search_term'] = $q;
        }

        header("Location: check.php");
        exit;
    }

    header("Location: check.php");
    exit;
}


if (!isset($_SESSION['mode'])) {
    $_SESSION['mode'] = 'all';
}

$mode = (string)($_SESSION['mode'] ?? 'all');
$term = (string)($_SESSION['search_term'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/check.css">
    <title>Check</title>
</head>
<body>
<?php include "header.php"; ?>

<div class="centr">
    <form action="check.php" method="POST">
        <input type="text" placeholder="search client" name="searchName" value="<?= htmlspecialchars($term) ?>">
        <button type="submit" name="action" value="search">search</button>
        <button type="submit" name="action" value="show">show all clients</button>
        <button type="submit" name="action" value="download">download</button>
    </form>
</div>

<div class="centralDiv">
    <table>
        <tr>
            <th>Name</th>
            <th>Company name</th>
            <th>Phone</th>
            <th>Email</th>
        </tr>

        <?php
        if ($mode === 'search') {
            searchTable($term);
        } else {
            createTable();
        }
        ?>
    </table>
</div>

<?php include "footer.php"; ?>
</body>
</html>
