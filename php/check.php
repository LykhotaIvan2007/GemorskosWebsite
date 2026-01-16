<?php
// check.php (no $_GET, no querystring; uses SESSION + PRG to prevent resubmit on reload)

session_start();

// ----------------------------
// POST/Redirect/GET (no GET params, no $_GET usage)
// ----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Show all
    if (isset($_POST['show'])) {
        $_SESSION['mode'] = 'all';
        unset($_SESSION['search_term']);
        header("Location: check.php"); // redirect clears POST
        exit;
    }

    // Search
    if (isset($_POST['search'])) {
        $q = trim((string)($_POST['searchName'] ?? ''));

        if ($q === '') {
            $_SESSION['mode'] = 'all';
            unset($_SESSION['search_term']);
        } else {
            $_SESSION['mode'] = 'search';
            $_SESSION['search_term'] = $q;
        }

        header("Location: check.php"); // redirect clears POST
        exit;
    }

    header("Location: check.php");
    exit;
}

// Defaults on first load
if (!isset($_SESSION['mode'])) {
    $_SESSION['mode'] = 'all';
}

function getDb(): PDO
{
    return new PDO(
        "mysql:host=localhost;port=3306;dbname=gemorskos;charset=utf8",
        "root",
        "root",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
}

function createTable(): void
{
    try {
        $db = getDb();
        $stmt = $db->query("SELECT client_name, client_company, client_phone, client_email FROM clients");

        while ($row = $stmt->fetch()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['client_name']) . "</td>
                    <td>" . htmlspecialchars($row['client_company']) . "</td>
                    <td>" . htmlspecialchars($row['client_phone']) . "</td>
                    <td>" . htmlspecialchars($row['client_email']) . "</td>
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
        ");

        $stmt->bindValue(':wd', "%{$word}%", PDO::PARAM_STR);
        $stmt->execute();

        $any = false;
        while ($row = $stmt->fetch()) {
            $any = true;
            echo "<tr>
                    <td>" . htmlspecialchars($row['client_name']) . "</td>
                    <td>" . htmlspecialchars($row['client_company']) . "</td>
                    <td>" . htmlspecialchars($row['client_phone']) . "</td>
                    <td>" . htmlspecialchars($row['client_email']) . "</td>
                  </tr>";
        }

        if (!$any) {
            echo "<tr><td colspan='4'>No results</td></tr>";
        }
    } catch (Exception $ex) {
        echo "<tr><td colspan='4'>Error: " . htmlspecialchars($ex->getMessage()) . "</td></tr>";
    }
}

$mode = $_SESSION['mode'] ?? 'all';
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
        <button type="submit" name="search">search</button>
        <button type="submit" name="show">show all clients</button>
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
