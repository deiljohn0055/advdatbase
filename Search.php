<?php
// Database connection settings
$servername = "localhost";
$username = "root"; // Change if your database username is different
$password = ""; // Change if your database password is set
$dbname = "inventory_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$search_query = "";
$items = [];

// Search items in the database
if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    $stmt = $conn->prepare("SELECT * FROM items WHERE name LIKE ?");
    $like_query = "%" . $search_query . "%";
    $stmt->bind_param("s", $like_query);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
if (isset($_POST['save'])) {
    $name = trim($_POST['name']);
    $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
    $date_received = $_POST['date_received']; // Add further date validation
    $expiry_date = $_POST['expiry_date']; // Add further date validation
    $type = $_POST['type'];

    // Proceed if inputs are valid
    if ($quantity && strtotime($date_received) && strtotime($expiry_date)) {
        $stmt = $conn->prepare("INSERT INTO items (name, quantity, date_received, expiry_date, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $name, $quantity, $date_received, $expiry_date, $type);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
    }
}


// Fetch all items for display
$all_items = [];
$result = $conn->query("SELECT * FROM items") or die($conn->error);
while ($row = $result->fetch_assoc()) {
    $all_items[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Items - Jollibee Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f9;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        td {
            text-align: center;
        }
        .quantity-indicator {
            padding: 5px;
            border-radius: 5px;
            color: white;
        }
        .low {
            background-color: #dc3545; /* Red */
        }
        .medium {
            background-color: #fd7e14; /* Orange */
        }
        .high {
            background-color: #28a745; /* Green */
        }
        .form {
            margin-top: 20px;
        }
        .form input {
            margin: 5px 0;
            padding: 10px;
            width: calc(100% - 22px);
        }
        .form button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Items in Inventory</h2>

        <!-- Search form -->
        <form class="form" action="search.php" method="GET">
    <input type="text" name="search_query" placeholder="Enter item name to search" value="<?php echo htmlspecialchars($search_query); ?>" required>
    <button type="submit" name="search">Search</button>
</form>

        <!-- Display search results -->
        <?php if (!empty($items)): ?>
            <h3>Search Results:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Date Received</th>
                        <th>Expiry Date</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $row): 
                        $quantity = $row['quantity'];
                        $quantityClass = '';
                        if ($quantity < 50) {
                            $quantityClass = 'low';
                        } elseif ($quantity <= 100) {
                            $quantityClass = 'medium';
                        } else {
                            $quantityClass = 'high';
                        }
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td>
                                <span class="quantity-indicator <?php echo $quantityClass; ?>">
                                    <?php echo htmlspecialchars($row['quantity']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['date_received']); ?></td>
                            <td><?php echo htmlspecialchars($row['expiry_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['type']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (isset($_POST['search'])): ?>
            <p>No items found for "<strong><?php echo htmlspecialchars($search_query); ?></strong>"</p>
        <?php endif; ?>

        <!-- Display all items -->
        <h3>All Inventory Items:</h3>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Date Received</th>
                    <th>Expiry Date</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_items as $row): 
                    $quantity = $row['quantity'];
                    $quantityClass = '';
                    if ($quantity < 50) {
                        $quantityClass = 'low';
                    } elseif ($quantity <= 100) {
                        $quantityClass = 'medium';
                    } else {
                        $quantityClass = 'high';
                    }
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>
                            <span class="quantity-indicator <?php echo $quantityClass; ?>">
                                <?php echo htmlspecialchars($row['quantity']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($row['date_received']); ?></td>
                        <td><?php echo htmlspecialchars($row['expiry_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
