<div id="addAssignmentForm">
    <h2>Add New Assignment</h2>
    <form action="add_assignment_process.php" method="post" id="assignmentForm" enctype="multipart/form-data" style="max-width: 600px; margin: auto;">
        <label for="title" style="display: block; margin-bottom: 8px;">Title:</label>
        <input type="text" id="title" name="title" required style="width: 100%; padding: 8px; margin-bottom: 20px;">
        
        <label for="description" style="display: block; margin-bottom: 8px;">Description:</label>
        <textarea id="description" name="description" rows="4" required style="width: 100%; padding: 8px; margin-bottom: 20px;"></textarea>
        
        <label for="deadline" style="display: block; margin-bottom: 8px;">Deadline:</label>
        <input type="text" id="deadline" name="deadline" required style="width: 100%; padding: 8px; margin-bottom: 20px;">
        
        <label for="attachments" style="display: block; margin-bottom: 8px;">Attachments:</label>
        <input type="file" id="attachments" name="attachments" style="width: 100%; padding: 8px; margin-bottom: 20px;">
        
        <button type="submit" style="padding: 10px 20px; margin-right: 10px;">Add Assignment</button>
        <button type="button" onclick="hideForm()" style="padding: 10px 20px;">Cancel</button>
    </form>
</div>
