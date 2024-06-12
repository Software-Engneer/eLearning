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

// Fetch student data (replace with actual student ID fetching logic)
$student_id = 1; // Example student ID, replace with your actual method to get student ID

// Fetch student grades data
$sql = "SELECT assignment_score, exam_score FROM student_grades WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$student_grades = [];
while ($row = $result->fetch_assoc()) {
    $student_grades[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Progress</title>
    <style>
        .progress-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .progress-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 20px;
            width: 300px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .progress-circle {
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }
        .progress-label {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/progressbar.js/1.0.1/progressbar.min.js"></script>
    <script>
        var studentGrades = <?php echo json_encode($student_grades); ?>;
        document.addEventListener("DOMContentLoaded", function() {
            studentGrades.forEach(function(grades, i) {
                var assignmentProgress = new ProgressBar.Circle("#assignmentProgress" + i, {
                    color: "#3498db",
                    strokeWidth: 6,
                    trailWidth: 3,
                    text: {
                        value: grades['assignment_score'] + "%",
                        className: "progressbar-text",
                        style: {
                            color: "#333",
                            position: "absolute",
                            left: "50%",
                            top: "50%",
                            padding: 0,
                            margin: 0,
                            transform: {
                                prefix: true,
                                value: "translate(-50%, -50%)"
                            }
                        }
                    },
                    step: function(state, circle) {
                        circle.setText(Math.round(circle.value() * 100) + "%");
                    }
                });
                assignmentProgress.animate(grades['assignment_score'] / 100);

                var examProgress = new ProgressBar.Circle("#examProgress" + i, {
                    color: "#e74c3c",
                    strokeWidth: 6,
                    trailWidth: 3,
                    text: {
                        value: grades['exam_score'] + "%",
                        className: "progressbar-text",
                        style: {
                            color: "#333",
                            position: "absolute",
                            left: "50%",
                            top: "50%",
                            padding: 0,
                            margin: 0,
                            transform: {
                                prefix: true,
                                value: "translate(-50%, -50%)"
                            }
                        }
                    },
                    step: function(state, circle) {
                        circle.setText(Math.round(circle.value() * 100) + "%");
                    }
                });
                examProgress.animate(grades['exam_score'] / 100);
            });
        });
    </script>
</head>
<body>

<div class="card card-outline card-primary w-fluid">
    <div class="card-header">
        <h3 class="card-title">Student Progress</h3>
    </div>
    <div class="card-body progress-container">

<?php
foreach ($student_grades as $i => $grades) {
    echo '
    <div class="progress-card">
        <div class="progress-circle" id="assignmentProgress' . $i . '"></div>
        <div class="progress-label">Assignment Progress: ' . $grades['assignment_score'] . '%</div>
        <div class="progress-circle" id="examProgress' . $i . '"></div>
        <div class="progress-label">Exam Progress: ' . $grades['exam_score'] . '%</div>
    </div>';
}
?>

    </div>
</div>

</body>
</html>
