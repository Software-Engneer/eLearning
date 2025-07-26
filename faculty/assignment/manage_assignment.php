<?php
// Check if 'success' flashdata is set and display a success alert
if ($_settings->chk_flashdata('success')): ?>
    <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success');
    </script>
<?php endif; ?>

<?php
$academic_year_id = $_settings->userdata('academic_id');
$faculty_id = $_settings->userdata('faculty_id');

$assignment = array();
$class_arr = array();

if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM assignments WHERE id = {$_GET['id']}");
    $assignment = $qry->fetch_assoc();

    if($assignment) {
        foreach($assignment as $k => $v){
            $$k = $v;
        }
        if(isset($description)) {
            $description = html_entity_decode(stripslashes($description));
        }

        $qry2 = $conn->query("SELECT * FROM assignment_class WHERE assignment_id = {$_GET['id']}");
        while($row = $qry2->fetch_assoc()){
            $class_arr[] = $row['class_id'];
        }
    }
}
?>


<style>
    .form-group.note-form-group.note-group-select-from-files {
        display: none;
    }
</style>

<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><?php echo isset($id) ? "Edit Assignment" : "New Assignment" ?></h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <form action="" id="manage-assignment">
                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                <input type="hidden" name="faculty_id" value="<?php echo $faculty_id ?>">
                <input type="hidden" name="academic_year_id" value="<?php echo $academic_year_id ?>">

                <div class="form-group">
                    <label for="title" class="control-label">Title</label>
                    <input type="text" class="form-control" name="title" value="<?php echo isset($title) ? $title : "" ?>" required>
                </div>

                <div class="form-group">
                    <label for="subject_id" class="control-label">Subject</label>
                    <select name="subject_id" id="subject_id" class="custom-select custom-select-sm select2" required>
                        <option></option>
                        <?php 
                        $subject = $conn->query("SELECT * FROM subjects ORDER BY subject_code ASC");
                        while($row = $subject->fetch_assoc()):
                        ?>
                            <option value="<?php echo $row['id'] ?>" <?php echo isset($subject_id) && $subject_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['subject_code'].' - '.$row['description'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="class_id" class="control-label">Class</label>
                    <select name="class_ids[]" id="class_id" class="custom-select custom-select-sm select2" required multiple="multiple">
                        <?php 
                        $class = $conn->query("SELECT cs.*, d.department, CONCAT(co.course, ' ', c.level, '-', c.section) AS class, s.subject_code, s.description FROM class_subjects_faculty cs INNER JOIN class c ON c.id = cs.class_id INNER JOIN subjects s ON s.id = cs.subject_id INNER JOIN department d ON d.id = c.department_id INNER JOIN course co ON co.id = c.course_id WHERE cs.faculty_id = '{$faculty_id}' AND cs.academic_year_id = '{$academic_year_id}' GROUP BY cs.class_id");
                        while($row = $class->fetch_assoc()):
                        ?>
                            <option value="<?php echo $row['class_id'] ?>" <?php echo isset($class_arr) && in_array($row['class_id'], $class_arr) ? 'selected' : '' ?>><?php echo $row['class'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description" class="control-label">Description</label>
                    <textarea name="description" id="description" cols="30" rows="10" class="form-control summernote"><?php echo isset($description) ? $description : ""; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="assignment_files" class="control-label">Assignment Files</label>
                    <input type="file" class="form-control" name="assignment_files[]" multiple accept=".doc,.docx,.pdf,.ppt,.pptx,image/x-png,image/gif,image/jpeg" <?php echo !isset($id) ? "required" : ""; ?>>
                </div>
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="col-md-12">
            <button class="btn btn-flat btn-primary" id="save-assignment">Save</button>
            <a type="button" class="btn btn-flat btn-default" href="./?page=assignment">Cancel</a>
        </div>
    </div>
</div>


<script>
    $(document).ready(function(){
        $('.select2').select2();

        var customMediaButton = function(context) {  
            var ui = $.summernote.ui;  
            var button = ui.button({  
                contents: '<i class="fa fa-photo-video"></i> Media',  
                tooltip: 'List All uploaded media to copy file link or short codes',  
                click: function() {  
                    context.invoke('editor.foreColor', 'red');  
                    uni_modal("Media List", "file_uploads/list_uploads.php", "mid-large")
                }  
            });  
            return button.render();  
        }  

        $('.summernote').summernote({
            height: '50vh',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ol', 'ul', 'paragraph', 'height']],
                ['table', ['table', 'picture', 'video', 'media']],
                ['view', ['undo', 'redo', 'fullscreen', 'codeview', 'help']]
            ],
            buttons: {
                media: customMediaButton
            }
        });

        $('#save-assignment').click(function(e){
            e.preventDefault();
            start_loader();

            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=save_assignment',
                data: new FormData($('#manage-assignment')[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                error: function(err) {
                    console.log(err);
                    alert_toast('An error occurred', 'error');
                    end_loader();
                },
                success: function(resp) {
                    resp = JSON.parse(resp);
                    if (resp.status == 'success') {
                        location.href = "./?page=assignment/view_assignment&id=" + resp.id;
                    } else {
                        alert_toast(resp.message, 'error');
                    }
                    end_loader();
                }
            });
        });
    });
</script>


