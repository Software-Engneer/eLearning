<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>



<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    include 'config.php';

    $data = "";
    foreach($_POST as $k => $v){
        if(!in_array($k, array('id','class_ids','assignment_files'))){
            $v = addslashes($v);
            if(empty($data)){
                $data .= " $k='$v' ";
            }else{
                $data .= ", $k='$v' ";
            }
        }
    }

    if(empty($_POST['id'])){
        $qry = $conn->query("INSERT INTO assignments set $data");
        if($qry){
            $id = $conn->insert_id;
            foreach($_POST['class_ids'] as $class_id){
                $conn->query("INSERT INTO assignment_class set assignment_id='$id', class_id='$class_id'");
            }
            if(isset($_FILES['assignment_files'])){
                foreach($_FILES['assignment_files']['tmp_name'] as $k => $v){
                    if(!empty($_FILES['assignment_files']['tmp_name'][$k])){
                        $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['assignment_files']['name'][$k];
                        move_uploaded_file($_FILES['assignment_files']['tmp_name'][$k],'uploads/'. $fname);
                        $conn->query("INSERT INTO assignment_files set assignment_id='$id', file_path='$fname'");
                    }
                }
            }
            echo json_encode(array('status' => 'success', 'id' => $id));
        }
    }else{
        $qry = $conn->query("UPDATE assignments set $data where id = {$_POST['id']}");
        if($qry){
            $conn->query("DELETE FROM assignment_class where assignment_id = {$_POST['id']}");
            foreach($_POST['class_ids'] as $class_id){
                $conn->query("INSERT INTO assignment_class set assignment_id='{$_POST['id']}', class_id='$class_id'");
            }
            echo json_encode(array('status' => 'success', 'id' => $_POST['id']));
        }
    }
}
?>
