<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Page</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f0f0f0;
            padding: 10px;
        }
        .card {
            background-color: #fff;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .card-body {
            margin-top: 5px;
        }
        table {
            font-style: italic;
            color: #474646;
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .add-new-btn {
            border: none;
            padding: 5px;
            width: 15%;
            font-size: small;
            margin-left: auto;
            cursor: pointer;
        }
        .action-menu {
            position: relative;
            display: inline-block;
        }
        .action-menu-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 100px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .action-menu-content button {
            display: block;
            width: 100%;
            text-align: left;
            border: none;
            background-color: transparent;
            cursor: pointer;
            padding: 8px;
        }
        .action-menu-content button:hover {
            background-color: #f1f1f1;
        }
        .action-menu-button {
            background-color: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            margin: 0;
            width: 24px;
            height: 24px;
            position: relative;
        }
        .action-menu-button::after {
            content: '⋮'; /* Unicode character for three dots */
            font-size: 20px;
            color: #333;
        }
        .action-menu-button:hover::after {
            color: #555;
        }
        #addAssignmentForm {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
        }
        #assignmentsCard {
            display: block;
        }
    </style>
    <!-- Include jQuery UI for the date picker -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>

<div id="assignmentsCard" class="card">
    <div class="card-header">
        <h2 style="margin: 0; font-size: 24px; color: #333;">Assignments</h2>
        <button class="add-new-btn" onclick="toggleForm()">Add New</button>
    </div>
    
    <div class="card-body">
        <table id="assignmentTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Deadline</th>
                    <th>Attachments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "elearning";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch assignments from database
                $sql = "SELECT id, title, description, deadline, attachments FROM assignments";
                $result = $conn->query($sql);

                if ($result->num_rows > 0):
                    while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row["id"] ?></td>
                            <td><?= $row["title"] ?></td>
                            <td><?= $row["description"] ?></td>
                            <td><?= $row["deadline"] ?></td>
                            <td><?= $row["attachments"] ?></td>
                            <td>
                                <div class='action-menu'>
                                    <button class="action-menu-button" onclick='toggleMenu(this)'></button>
                                    <div class='action-menu-content'>
                                        <button onclick="viewAssignment('<?= $row["title"] ?>')">View</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr><td colspan='6'>No assignments found</td></tr>
                <?php endif;

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Include the add_assignment.php form -->
<?php include 'add_assignment.php'; ?>

<script>
    function toggleForm() {
        var form = document.getElementById('addAssignmentForm');
        var assignmentsCard = document.getElementById('assignmentsCard');
        if (form.style.display === 'block') {
            form.style.display = 'none';
            assignmentsCard.style.display = 'block';
        } else {
            form.style.display = 'block';
            assignmentsCard.style.display = 'none';
        }
    }
    
    function hideForm() {
        document.getElementById('addAssignmentForm').style.display = 'none';
        document.getElementById('assignmentsCard').style.display = 'block';
    }
    
    function toggleMenu(button) {
        var menu = button.nextElementSibling;
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }

    // Close any open menus when clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.action-menu-button')) {
            var menus = document.querySelectorAll('.action-menu-content');
            for (var i = 0; i < menus.length; i++) {
                var openMenu = menus[i];
                if (openMenu.style.display === 'block') {
                    openMenu.style.display = 'none';
                }
            }
        }
    }

    $(function() {
        $("#deadline").datepicker({
            dateFormat: "yy-mm-dd"
        });
    });
</script>

</body>
</html>
