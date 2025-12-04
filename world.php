<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'world';

try {
    // Connect to MySQL
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // If connection fails, show a simple message (don't reveal credentials)
    echo "Connection failed: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit;
}

// helper to safely escape values (casts null to empty string first)
function safe($value) {
    // cast to string to avoid passing null into htmlspecialchars (deprecated)
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Read GET variables
$country = $_GET['country'] ?? '';
$lookup = $_GET['lookup'] ?? '';

// Normalize country input for SQL LIKE (allow empty = all countries)
$likeCountry = "%$country%";

// ---------- COUNTRY LOOKUP ----------
if ($lookup !== "cities") {
    $stmt = $conn->prepare("SELECT name, continent, independence_year, head_of_state
                            FROM countries
                            WHERE name LIKE :country
                            ORDER BY name ASC");
    $stmt->bindValue(':country', $likeCountry);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table>";
    echo "<tr><th>Name</th><th>Continent</th><th>Independence Year</th><th>Head of State</th></tr>";

    foreach ($results as $row) {
        echo "<tr>";
        echo "<td>" . safe($row['name']) . "</td>";
        echo "<td>" . safe($row['continent']) . "</td>";
        // independence_year may be NULL in DB — safe() handles that
        echo "<td>" . safe($row['independence_year']) . "</td>";
        echo "<td>" . safe($row['head_of_state']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    exit;
}

// ---------- CITY LOOKUP ----------
$stmt = $conn->prepare("SELECT cities.name AS city, cities.district, cities.population
                        FROM cities
                        JOIN countries ON countries.code = cities.country_code
                        WHERE countries.name LIKE :country
                        ORDER BY cities.name ASC");
$stmt->bindValue(':country', $likeCountry);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table>";
echo "<tr><th>City</th><th>District</th><th>Population</th></tr>";

foreach ($results as $row) {
    echo "<tr>";
    echo "<td>" . safe($row['city']) . "</td>";
    echo "<td>" . safe($row['district']) . "</td>";
    echo "<td>" . safe($row['population']) . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
