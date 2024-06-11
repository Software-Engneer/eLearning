<!DOCTYPE html>
<html>
<head>
    <title>Student Progress</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/progressbar.js/1.0.1/progressbar.min.js"></script>
    <style>
        .progress-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
        }
        .progress-card {
            width: 45%;
            margin: 1%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .progress-circle {
            width: 100px;
            height: 100px;
        }
        .progress-label {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
</script>
<?php endif; ?>

<div class="card card-outline card-primary w-fluid">
    <div class="card-header">
        <h3 class="card-title">Student Progress</h3>
    </div>
    <div class="card-body progress-container">
        <?php 
        $i = 1;
        $academic_year_id = $_settings->userdata('academic_id');
        $student_id = $_settings->userdata('student_id');
        $class_id_result = $conn->query("SELECT class_id FROM student_class WHERE academic_year_id = {$academic_year_id} AND student_id = '{$student_id}'");

        if ($class_id_result->num_rows > 0):
            $class_id = $class_id_result->fetch_assoc()['class_id'];
            $qry = $conn->query("SELECT l.*, s.subject_code, IFNULL(g.assignment_score, 0) as assignment_score, IFNULL(g.exam_score, 0) as exam_score 
                                 FROM lessons l 
                                 INNER JOIN subjects s ON s.id = l.subject_id 
                                 LEFT JOIN (SELECT lesson_id, assignment_score, exam_score FROM student_grades WHERE student_id = '{$student_id}') g 
                                 ON l.id = g.lesson_id 
                                 WHERE l.academic_year_id = '{$academic_year_id}' 
                                 AND l.id IN (SELECT lesson_id FROM lesson_class WHERE class_id = '{$class_id}')");

            while ($row = $qry->fetch_assoc()):
        ?>
        <div class="progress-card">
            <h5 class="card-title"><?php echo $row['title']; ?></h5>
            <h6 class="card-subtitle mb-2 text-muted"><?php echo $row['subject_code']; ?></h6>
            <p class="card-text"><?php echo strip_tags(stripslashes(html_entity_decode($row['description']))); ?></p>
            <div class="progress-circle" id="assignmentProgress<?php echo $i; ?>"></div>
            <div class="progress-label">Assignment Progress</div>
            <div class="progress-circle" id="examProgress<?php echo $i; ?>"></div>
            <div class="progress-label">Exam Progress</div>
        </div>
        <script>
            var assignmentProgress<?php echo $i; ?> = new ProgressBar.Circle('#assignmentProgress<?php echo $i; ?>', {
                color: '#3498db',
                strokeWidth: 6,
                trailWidth: 3,
                text: {
                    value: '<?php echo $row['assignment_score']; ?>%',
                    className: 'progressbar-text',
                    style: {
                        color: '#333',
                        position: 'absolute',
                        left: '50%',
                        top: '50%',
                        padding: 0,
                        margin: 0,
                        transform: {
                            prefix: true,
                            value: 'translate(-50%, -50%)'
                        }
                    }
                },
                step: function(state, circle) {
                    circle.setText(Math.round(circle.value() * 100) + '%');
                }
            });
            assignmentProgress<?php echo $i; ?>.animate(<?php echo $row['assignment_score'] / 100; ?>);

            var examProgress<?php echo $i; ?> = new ProgressBar.Circle('#examProgress<?php echo $i; ?>', {
                color: '#e74c3c',
                strokeWidth: 6,
                trailWidth: 3,
                text: {
                    value: '<?php echo $row['exam_score']; ?>%',
                    className: 'progressbar-text',
                    style: {
                        color: '#333',
                        position: 'absolute',
                        left: '50%',
                        top: '50%',
                        padding: 0,
                        margin: 0,
                        transform: {
                            prefix: true,
                            value: 'translate(-50%, -50%)'
                        }
                    }
                },
                step: function(state, circle) {
                    circle.setText(Math.round(circle.value() * 100) + '%');
                }
            });
            examProgress<?php echo $i; ?>.animate(<?php echo $row['exam_score'] / 100; ?>);
        </script>
        <?php $i++; endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    $(document).ready(function(){
        // Custom initialization if needed
    });
</script>
</body>
</html>
