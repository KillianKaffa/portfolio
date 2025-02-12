<?php
session_start();

// Database configuratie
$hostname = 'localhost';
$username = '87249';
$password = '988K42703';
$database = '87249_portfolio';

// Maak de verbinding
$conn = new mysqli($hostname, $username, $password, $database);

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['ingelogd']) || $_SESSION['ingelogd'] !== true) {
    header('Location: login.php');
    exit;
}

// Haal projectgegevens op uit de database
$query = "SELECT * FROM projecten";
$result = $conn->query($query);

// Voeg een nieuw project toe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toevoegen'])) {
    // Vul hier de code in om het project toe te voegen aan de database
    $nieuw_project_naam = $_POST['nieuw_project_naam'];
    $nieuw_project_beschrijving = $_POST['nieuw_project_beschrijving'];
    $nieuw_project_beschrijving_nl = $_POST['nieuw_project_beschrijving_nl'];
    $nieuw_project_image = $_POST['nieuw_project_image'];
    $nieuw_project_link = $_POST['nieuw_project_link'];
    $nieuw_project_datum = $_POST['nieuw_project_datum'];

    $insert_query = "INSERT INTO projecten (title, description, description_nl, image, link, project_date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssssss", $nieuw_project_naam, $nieuw_project_beschrijving, $nieuw_project_beschrijving_nl, $nieuw_project_image, $nieuw_project_link, $nieuw_project_datum);
    $stmt->execute();
    $stmt->close();

    // Na toevoegen, stuur de gebruiker terug naar dezelfde pagina
    header('Location: admin.php');
    exit;
}

// Bewerk een bestaand project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bewerken'])) {
    $project_id = $_POST['project_id'];
    $project_naam = $_POST['project_naam'];
    $project_beschrijving = $_POST['project_beschrijving'];
    $project_beschrijving_nl = $_POST['project_beschrijving_nl'];
    $project_image = $_POST['project_image'];
    $project_link = $_POST['project_link'];
    $project_datum = $_POST['project_datum'];

    // Voer hier de code uit om het project bij te werken in de database
    $update_query = "UPDATE projecten SET title = ?, description = ?, description_nl = ?, image = ?, link = ?, project_date = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssssi", $project_naam, $project_beschrijving, $project_beschrijving_nl, $project_image, $project_link, $project_datum, $project_id);
    $stmt->execute();
    $stmt->close();

    // Na bewerken, stuur de gebruiker terug naar dezelfde pagina
    header('Location: admin.php');
    exit;
}

// Verwijder een bestaand project
if (isset($_GET['verwijderen'])) {
    $project_id = $_GET['verwijderen'];

    // Voer hier de code uit om het project uit de database te verwijderen
    $delete_query = "DELETE FROM projecten WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $stmt->close();

    // Na verwijderen, stuur de gebruiker terug naar dezelfde pagina
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" href="./css/admin.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Voeg hier de HTML-inhoud toe voor het beheer van projecten -->

    <!-- Voeg formulieren toe voor toevoegen, bewerken en verwijderen van projecten -->
    <h2>Projecten Beheren</h2>
    <form method="post" action="admin.php">
        <label for="nieuw_project_naam">Titel:</label>
        <input type="text" name="nieuw_project_naam" required>
        
        <label for="nieuw_project_beschrijving">Beschrijving:</label>
        <textarea name="nieuw_project_beschrijving" required></textarea>
        
        <label for="nieuw_project_beschrijving_nl">Beschrijving (Nederlands):</label>
        <textarea name="nieuw_project_beschrijving_nl" required></textarea>
        
        <label for="nieuw_project_image">Afbeelding URL:</label>
        <input type="text" name="nieuw_project_image" required>
        
        <label for="nieuw_project_link">Link:</label>
        <input type="text" name="nieuw_project_link" required>
        
        <label for="nieuw_project_datum">Datum:</label>
        <input type="text" name="nieuw_project_datum" required>
        
        <button type="submit" name="toevoegen">Voeg toe</button>
    </form>

    <table>
    <tr>
        <th>Titel</th>
        <th>Beschrijving</th>
        <th>Afbeelding</th>
        <th>Link</th>
        <th>Datum</th>
        <th>Acties</th>
    </tr>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['title'] . "</td>";
        echo "<td>" . $row['description'] . "</td>";
        echo "<td><img src='" . $row['image'] . "' width='100' height='100'></td>";
        echo "<td>" . $row['link'] . "</td>"; 
        echo "<td>" . $row['project_date'] . "</td>";
        echo "<td>";
        echo "<button class='bewerk-btn' data-id='" . $row['id'] . "'>Bewerk</button> ";
        echo "<a href='admin.php?verwijderen=" . $row['id'] . "'>Verwijder</a>";
        echo "</td>";
        echo "</tr>";
    }
    ?>
</table>

<!-- Voeg een verborgen bewerkingsformulier toe -->
<form id="bewerk-form" method="post" action="admin.php" style="display:none;">
    <input type="hidden" name="bewerken" value="1">
    <input type="hidden" name="project_id" id="bewerk-project-id">
    <input type="text" name="project_naam" id="bewerk-project-naam" placeholder="Titel">
    <textarea name="project_beschrijving" id="bewerk-project-beschrijving" placeholder="Beschrijving"></textarea>
    <textarea name="project_beschrijving_nl" id="bewerk-project-beschrijving-nl" placeholder="Beschrijving (Nederlands)"></textarea>
    <input type="text" name="project_image" id="bewerk-project-image" placeholder="Afbeelding URL">
    <input type="text" name="project_link" id="bewerk-project-link" placeholder="Link">
    <input type="text" name="project_datum" id="bewerk-project-datum" placeholder="Datum">
    <button type="submit">Opslaan</button>
    <button id="annuleer-bewerking">Annuleren</button>
</form>
<!-- Voeg formulieren toe voor toevoegen, bewerken en verwijderen van projecten -->
<h2>Projecten Beheren</h2>
<form method="post" action="admin.php">
    <label for="nieuw_project_naam">Titel:</label>
    <input type="text" name="nieuw_project_naam" required>

    <label for="nieuw_project_beschrijving">Beschrijving:</label>
    <textarea name="nieuw_project_beschrijving" required></textarea>

    <label for="nieuw_project_beschrijving_nl">Beschrijving (Nederlands):</label>
    <textarea name="nieuw_project_beschrijving_nl" required></textarea>

    <label for="nieuw_project_image">Afbeelding URL:</label>
    <input type="text" name="nieuw_project_image" required>

    <label for="nieuw_project_link">Link:</label>
    <input type="text" name="nieuw_project_link" required>

    <label for="nieuw_project_datum">Datum:</label>
    <input type="text" name="nieuw_project_datum" required>

    <button type="submit" name="toevoegen">Voeg toe</button>
</form>


<!-- JavaScript voor bewerken -->
<script>
    $(document).ready(function() {
        $(".bewerk-btn").click(function() {
            var projectID = $(this).data("id");
            var tr = $(this).closest("tr");
            var titel = tr.find("td:eq(0)").text();
            var beschrijving = tr.find("td:eq(1)").text();
            var beschrijving_nl = tr.find("td:eq(2)").text();
            var afbeelding = tr.find("td:eq(3)").text();
            var link = tr.find("td:eq(4)").text();
            var datum = tr.find("td:eq(5)").text();

            // Vul de bewerkingsformulier velden in
            $("#bewerk-project-id").val(projectID);
            $("#bewerk-project-naam").val(titel);
            $("#bewerk-project-beschrijving").val(beschrijving);
            $("#bewerk-project-beschrijving-nl").val(beschrijving_nl);
            $("#bewerk-project-image").val(afbeelding);
            $("#bewerk-project-link").val(link);
            $("#bewerk-project-datum").val(datum);

            // Laat het bewerkingsformulier zien
            $("#bewerk-form").show();
        });

        $("#annuleer-bewerking").click(function() {
            // Verberg het bewerkingsformulier
            $("#bewerk-form").hide();
        });
    });
</script>

</body>
</html>
