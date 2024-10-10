//deil
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
$name = "";
$quantity = "";
$date_received = "";
$expiry_date = "";
$type = "";
$id = 0;
$update = false;

// Insert data into the database
if (isset($_POST['save'])) {
    $stmt = $conn->prepare("INSERT INTO items (name, quantity, date_received, expiry_date, type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $name, $quantity, $date_received, $expiry_date, $type);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
}

// Edit existing data
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update = true;
    $result = $conn->query("SELECT * FROM items WHERE id=$id") or die($conn->error);
    if ($result->num_rows > 0) {
        $row = $result->fetch_array();
        $name = $row['name'];
        $quantity = $row['quantity'];
        $date_received = $row['date_received'];
        $expiry_date = $row['expiry_date'];
        $type = $row['type'];
    }
}

// Update data
if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE items SET name=?, quantity=?, date_received=?, expiry_date=?, type=? WHERE id=?");
    $stmt->bind_param("sisssi", $name, $quantity, $date_received, $expiry_date, $type, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
}

// Delete data
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM items WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jollibee Inventory</title>
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
        .form input, .form select {
            margin: 5px 0;
            padding: 10px;
            width: calc(100% - 22px);
        }
        .form button {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form button.update {
            background-color: #ffc107;
        }
        .form button.delete {
            background-color: #dc3545;
        }
        .filter-btn {
            margin: 5px;
            padding: 10px;
            background-color: #17a2b8;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Jollibee Inventory Management</h2>

        <!-- Filter buttons -->
        <div>
            <button class="filter-btn" onclick="filterItems('All')">Show All</button>
            <button class="filter-btn" onclick="filterItems('Frozen')">Frozen</button>
            <button class="filter-btn" onclick="filterItems('Chilled')">Chilled</button>
            <button class="filter-btn" onclick="filterItems('Dry')">Dry</button>
            <button class="filter-btn" onclick="filterItems('Drinks')">Drinks</button>
        </div>

        <!-- Form to add or update items -->
        <form class="form" id="itemForm" action="index.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="text" name="name" placeholder="Item Name" value="<?php echo $name; ?>" required>
            <input type="number" name="quantity" placeholder="Quantity" value="<?php echo $quantity; ?>" required>
            <h4>Delivery Date: </h4>
            <input type="date" name="date_received" placeholder="Date Received" value="<?php echo $date_received; ?>" required>
            <h4>Expiration Date: </h4>
            <input type="date" name="expiry_date" placeholder="Expiry Date" value="<?php echo $expiry_date; ?>" required>
            <select name="type" required>
                <option value="" disabled <?php echo $type == "" ? "selected" : ""; ?>>Select Type</option>
                <option value="Frozen" <?php echo $type == "Frozen" ? "selected" : ""; ?>>Frozen</option>
                <option value="Chilled" <?php echo $type == "Chilled" ? "selected" : ""; ?>>Chilled</option>
                <option value="Dry" <?php echo $type == "Dry" ? "selected" : ""; ?>>Dry</option>
                <option value="Drinks" <?php echo $type == "Drinks" ? "selected" : ""; ?>>Drinks</option>
            </select>
            <?php if ($update == true): ?>
                <button class="update" type="submit" name="update">Update</button>
            <?php else: ?>
                <button type="submit" name="save">Add</button>
            <?php endif; ?>
        </form>

        <!-- Display items in the table, ordered by expiry_date for FIFO -->
        <table id="itemTable">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Date Received</th>
                    <th>Expiry Date</th>
                    <th>Type</th>
                    <th colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and sort data from the database by expiry_date (FIFO)
                $result = $conn->query("SELECT * FROM items ORDER BY expiry_date ASC") or die($conn->error);
                while ($row = $result->fetch_assoc()): 
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
                    <tr class="item-row" data-type="<?php echo $row['type']; ?>">
                        <td><?php echo $row['name']; ?></td>
                        <td>
                            <span class="quantity-indicator <?php echo $quantityClass; ?>">
                                <?php echo $row['quantity']; ?>
                            </span>
                        </td>
                        <td><?php echo $row['date_received']; ?></td>
                        <td><?php echo $row['expiry_date']; ?></td>
                        <td><?php echo $row['type']; ?></td>
                        <td>
                            <a href="index.php?edit=<?php echo $row['id']; ?>" class="update">Edit</a>
                        </td>
                        <td>
                            <a href="index.php?delete=<?php echo $row['id']; ?>" class="delete">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript to filter items -->
    <script>
        function filterItems(type) {
            var rows = document.querySelectorAll(".item-row");
            rows.forEach(row => {
                if (type === 'All' || row.getAttribute('data-type') === type) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>

