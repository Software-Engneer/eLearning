<?php
// Check if the 'id' parameter is present in the GET request
if (!isset($_GET['id'])) {
    // Alert the user and redirect to the assignments page if 'id' is not set
    echo "<script>alert('Assignment ID is required.'); location.replace('./?page=assignment');</script>";
    exit;
}

$assignmentId = $_GET['id'];

// Fetch the assignment details along with the subject code
$assignmentQuery = $conn->query("
    SELECT a.*, s.subject_code 
    FROM assignments a 
    INNER JOIN subjects s ON s.id = a.subject_id 
    WHERE a.id = {$assignmentId}
");

// Check if the assignment exists
if ($assignmentQuery->num_rows > 0) {
    $assignmentDetails = $assignmentQuery->fetch_assoc();
    $assignmentDetails['description'] = html_entity_decode($assignmentDetails['description']);
    
    $associatedClasses = [];

    // Fetch the classes associated with the assignment
    $classesQuery = $conn->query("
        SELECT c.*, d.department, co.course, s.subject_code, s.description 
        FROM assignment_class ac 
        INNER JOIN class c ON c.id = ac.class_id 
        INNER JOIN subjects s ON s.id = c.subject_id 
        INNER JOIN department d ON d.id = c.department_id 
        INNER JOIN course co ON co.id = c.course_id 
        WHERE ac.assignment_id = {$assignmentId}
    ");
    
    while ($class = $classesQuery->fetch_assoc()) {
        $associatedClasses[] = $class;
    }
} else {
    // Alert the user and redirect to the assignments page if the assignment ID is invalid
    echo "<script>alert('Invalid Assignment ID.'); location.replace('./?page=assignment');</script>";
    exit;
}
?>

<div class="card card-outline card-primary">
    <div class="card-header">
        <!-- Display the title of the assignment -->
        <h3 class="card-title"><?php echo $assignmentDetails['title']; ?></h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <dl>
                <dt>Title</dt>
                <dd><?php echo $assignmentDetails['title']; ?></dd> <!-- Display the assignment title -->
                
                <dt>Subject</dt>
                <dd><?php echo $assignmentDetails['subject_code']; ?></dd> <!-- Display the subject code -->
                
                <dt>Description</dt>
                <dd><?php echo $assignmentDetails['description']; ?></dd> <!-- Display the assignment description -->
                
                <dt>Classes</dt>
                <dd>
                    <ul>
                        <?php foreach ($associatedClasses as $class): ?>
                            <!-- Display each class associated with the assignment -->
                            <li><?php echo $class['course'] . ' ' . $class['level'] . '-' . $class['section']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </dd>
                
                <dt>Files</dt>
                <dd>
                    <?php 
                    // Fetch the files associated with the assignment
                    $filesQuery = $conn->query("SELECT * FROM assignment_files WHERE assignment_id = {$assignmentId}");
                    while ($file = $filesQuery->fetch_assoc()): ?>
                        <!-- Display links to each file -->
                        <a href="uploads/assignments/<?php echo $file['file_path']; ?>" target="_blank"><?php echo $file['file_path']; ?></a><br>
                    <?php endwhile; ?>
                </dd>
            </dl>
        </div>
    </div>
    <div class="card-footer">
        <div class="col-md-12">
            <!-- Button to go back to the assignments page -->
            <a href="./?page=assignment" class="btn btn-flat btn-default">Back</a>
        </div>
    </div>
</div>
