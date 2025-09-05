<style>
.constitution-container {
    margin: 0 10px;
    font-family: sans-serif;
}

.constitution-container h2 {
    margin-top: 20px;
}

.document-frame {
    width: 100%;
    min-height: 600px;
    border: 1px solid #ddd;
    margin: 20px 0;
}

.upload-form {
    margin: 20px 0;
    padding: 10px;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.file-input {
    margin: 10px 0;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 4px;
    background: white;
}

.file-input input[type="file"] {
    display: block;
    width: 100%;
	position: static;
	opacity: 1;
}

@media screen and (max-width: 768px) {
    .document-frame {
        min-height: 400px;
    }
}
</style><div class="row">    <div id='feedback_form_container' class="col-12 mt-3 mb-2">		<div class="card">			<div class="card-body">				<div class="card-header-heading">					<div class="row">						<div class="col"><h4 class="row">Constitution Document</h4></div>					</div>				</div>				<div class="row">					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
						<?php if (isset($this->body_template_data['upload_result'])): ?>
							<div class="alert <?php echo $this->body_template_data['upload_result']['error_code'] == 0 ? 'alert-success' : 'alert-danger'; ?>">
								<?php echo htmlspecialchars($this->body_template_data['upload_result']['message']); ?>
							</div>
						<?php endif; ?>
						
						<?php if (!empty($this->body_template_data['current_document'])): ?>
							<div>
								<strong>Current Document:</strong> 
								<?php echo htmlspecialchars($this->body_template_data['current_document']['filename']); ?>
								(uploaded <?php echo date('d-M-Y', strtotime($this->body_template_data['current_document']['upload_date'])); ?>)
								<iframe src="constitution.php?download=1" class="document-frame"></iframe>
							</div>
						<?php else: ?>
							<p>No document has been uploaded yet.</p>
						<?php endif; ?>
						
						<div class="upload-form">
							<h4>Upload New Document</h4>
							<form action="constitution.php" method="post" enctype="multipart/form-data">
								<input type="hidden" name="mode" value="upload">
								<div class="file-input">
								<label for="document-upload">Select Document:</label>
								<input type="file" id="document-upload" name="document" accept=".doc,.docx,.pdf" required style="margin-top:5px;">
								</div>
								<div class="text-center" style="margin-top: 20px;">
									<button type="submit" class="rounded btn btn-success">Upload Document</button>
								</div>
							</form>
						</div>					</div>				</div>			</div>		</div>	</div></div>