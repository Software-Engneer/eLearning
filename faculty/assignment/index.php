<?php
// config.php
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

// settings.php (example settings class)
class Settings {
    public function chk_flashdata($key) {
        return isset($_SESSION['flashdata'][$key]);
    }

    public function flashdata($key) {
        $value = $_SESSION['flashdata'][$key] ?? '';
        unset($_SESSION['flashdata'][$key]);
        return $value;
    }

    public function userdata($key) {
        return $_SESSION['user'][$key] ?? '';
    }
}

// // Initialize settings
// $_settings = new Settings();
// session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments</title>
    <!-- Include Bootstrap CSS and DataTables CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
</head>
<body>

<?php if($_settings->chk_flashdata('success')): ?>
    <script>
        alert("<?php echo $_settings->flashdata('success'); ?>");
    </script>
<?php endif; ?>

<div class="card card-outline card-primary w-fluid">
    <div class="card-header">
        <h3 class="card-title">Assignments</h3>
        <div class="card-tools">
            <a class="btn btn-block btn-sm btn-default btn-flat border-primary new_assignment" href="javascript:void(0)"><i class="fa fa-plus"></i> Add Assignment</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-compact table-striped">
            <colgroup>
                <col width="5%">
                <col width="20%">
                <col width="20%">
                <col width="40%">
                <col width="15%">
            </colgroup>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                $academic_year_id = $_settings->userdata('academic_id');
                $faculty_id = $_settings->userdata('faculty_id');

                $qry = $conn->query("SELECT a.*, s.subject_code FROM assignments a INNER JOIN subjects s ON s.id = a.subject_id WHERE a.academic_year_id = '{$academic_year_id}' AND a.faculty_id = '{$faculty_id}'");
                while ($row = $qry->fetch_assoc()):
                    $desc = html_entity_decode($row['description']);
                    $desc = stripslashes($desc);
                    $desc = strip_tags($desc);
                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['subject_code']; ?></td>
                    <td><span class="truncate"><?php echo $desc; ?></span></td>
                    <td class="text-center">
                        <div class="btn-group dropdown">
                            <button type="button" class="btn btn-default btn-block btn-flat dropdown-toggle dropdown-hover dropdown-icon btn-sm" data-toggle="dropdown" aria-expanded="false">
                                Action
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu w-auto" role="menu">
                                <a class="dropdown-item action_load" href="./?page=assignment/view_assignment&id=<?php echo $row['id']; ?>">View</a>
                                <div class="divider"></div>
                                <a class="dropdown-item action_delete" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">Delete</a>
                            </div>
                        </div>
                    </td>    
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Include jQuery, Bootstrap JS, and DataTables JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function(){
        $('.new_assignment').click(function(){
            location.href = "./?page=assignment/manage_assignment";
        });

        $('.action_delete').click(function(){
            _conf("Are you sure to delete this assignment?", "delete_assignment", [$(this).attr('data-id')]);
        });

        $('table').DataTable();
    });

    function delete_assignment(id){
        start_loader();
        $.ajax({
            url: 'delete_assignment.php',
            method: 'POST',
            data: {id: id},
            dataType: 'json',
            error: function(err){
                console.log(err);
                alert("An error occurred.");
                end_loader();
            },
            success: function(resp){
                if (resp.status == 'success') {
                    location.reload();
                } else {
                    console.log(resp);
                    alert("An error occurred.");
                }
                end_loader();
            }
        });
    }
</script>
</body>
</html>
