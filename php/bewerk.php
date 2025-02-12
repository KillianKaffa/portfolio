<?php
// Database configuratie
$hostname = 'localhost';      // Hostnaam (bijv. 'localhost' of '127.0.0.1')
$username = '87249'; // Gebruikersnaam voor de database
$password = '988K42703';      // Wachtwoord voor de database
$database = '87249_portfolio'; // Naam van de database

// Maak de verbinding
$conn = new mysqli($hostname, $username, $password, $database);

// Controleer of de ID is ingesteld in de URL
if (!isset($_GET['id'])) {
    header('Location: admin.php'); // Stuur gebruiker terug naar de admin-pagina als er geen ID is ingesteld
    exit;
}

$id = $_GET['id'];

// Haal de huidige gegevens van het project op
$query = "SELECT * FROM projecten WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    // Het opgegeven project bestaat niet, stuur gebruiker terug naar de admin-pagina
    header('Location: admin.php');
    exit;
}

// Verwerk het bewerkingsformulier als het is ingediend
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bewerken'])) {
    $project_naam = $_POST['title'];
    $project_beschrijving = $_POST['description'];
    $project_beschrijving_nl = $_POST['description_nl'];
    $project_image = $_POST['image'];
    $project_link = $_POST['link'];
    $project_datum = $_POST['project_date'];

    // Voer hier de code uit om het project bij te werken in de database
    $update_query = "UPDATE projecten SET title = ?, description = ?, description_nl = ?, image = ?, link = ?, project_date = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssssi", $project_naam, $project_beschrijving, $project_beschrijving_nl, $project_image, $project_link, $project_datum, $id);
    $stmt->execute();

    // Stuur de gebruiker terug naar de admin-pagina na de bewerking
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" href="admin.css">
</head>
<body>
    <!-- Voeg hier het bewerkingsformulier toe -->
    <h2>Bewerk Project</h2>
    <form method="POST">
        <label for="title">Titel:</label>
        <input type="text" id="title" name="title" value="<?php echo $row['title']; ?>" required><br><br>

        <label for="description">Beschrijving:</label>
        <textarea id="description" name="description" required><?php echo $row['description']; ?></textarea><br><br>

        <label for="description_nl">Beschrijving (NL):</label>
        <textarea id="description_nl" name="description_nl" required><?php echo $row['description_nl']; ?></textarea><br><br>

        <label for="image">Afbeelding:</label>
        <input type="text" id="image" name="image" value="<?php echo $row['image']; ?>" required><br><br>

        <label for="link">Link:</label>
        <input type="text" id="link" name="link" value="<?php echo $row['link']; ?>" style="max-width: 500px;"><br><br>

        <label for="project_date">Datum:</label>
        <input type="date" id="project_date" name="project_date" value="<?php echo $row['project_date']; ?>" required><br><br>

        <input type="submit" name="bewerken" value="Bewerken">
    </form>
</body>
</html>
