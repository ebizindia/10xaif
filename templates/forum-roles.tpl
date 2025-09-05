<style>
.forum-roles-container {
    padding: 10px;
}

.years-list {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.year-link {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
}.year-link:hover {text-decoration: none;background-color: #ff795c;color:#fff;}

.year-link.active {
    background-color: #ff795c;
    color: white;
    border-color: #ff795c;
}

.roles-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.roles-table th, .roles-table td {
    border: 1px solid #ddd;
    padding: 10px;
}

.roles-table th {
    background-color: #f5f5f5;
}

/* Mobile Styles */
@media screen and (max-width: 768px) {
    .roles-table {
        display: block;
    }
    
    .roles-table thead {
        display: none;
    }
    
    .roles-table tbody {
        display: block;
    }
    
    .roles-table tr {
        display: block;
        margin-bottom: 20px;
        border: 1px solid #ddd;
    }
    
    .roles-table td {
        display: block;
        border: none;
        border-bottom: 1px solid #eee;
    }
    
    .roles-table td::before {
        content: attr(data-label);
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }
    
    .roles-table td:last-child {
        border-bottom: none;
    }
}
</style>

<div class="row">    <div id='feedback_form_container' class="col-12 mt-3 mb-2">		<div class="card">			<div class="card-body">				<div class="card-header-heading">					<div class="row">						<div class="col"><h4 class="row">Forum Roles</h4></div>					</div>				</div>				<div class="row">					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
						<?php if (isset($this->body_template_data['save_result'])): ?>
							<div class="alert <?php echo $this->body_template_data['save_result']['error_code'] == 0 ? 'alert-success' : 'alert-danger'; ?>">
								<?php echo htmlspecialchars($this->body_template_data['save_result']['message']); ?>
							</div>
						<?php endif; ?>
						
						<div class="years-list">
							<?php foreach ($this->body_template_data['years_list'] as $year): ?>
								<a href="forum-roles.php?year=<?php echo $year; ?>" 
								   class="year-link <?php echo $year === $this->body_template_data['selected_year'] ? 'active' : ''; ?>">
									<?php echo htmlspecialchars($year); ?>
								</a>
							<?php endforeach; ?>
						</div>
						<form action="forum-roles.php" method="post">
							<input type="hidden" name="mode" value="saveForumRoles">
							<input type="hidden" name="year" value="<?php echo htmlspecialchars($this->body_template_data['selected_year']); ?>">
													<div class="panel-body table-responsive">
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Position</th>
										<th>Member</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($this->body_template_data['forum_positions'] as $position_key => $position_label): ?>
										<tr>
											<td data-label="Position"><?php echo htmlspecialchars($position_label); ?></td>
											<td data-label="Member">
												<select class="form-control" name="roles[<?php echo $position_key; ?>]">
													<option value="">Select Member</option>
													<?php foreach ($this->body_template_data['members_list'] as $member): ?>
														<option value="<?php echo $member['id']; ?>" 
																<?php echo isset($this->body_template_data['current_roles'][$position_key]) && 
																		  $this->body_template_data['current_roles'][$position_key]['member_id'] == $member['id'] 
																		  ? 'selected' : ''; ?>>
															<?php echo htmlspecialchars($member['name']); ?>
														</option>
													<?php endforeach; ?>
												</select>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>	
							<div style="margin-top: 20px; text-align: center;">
								<button type="submit" class="btn btn-success rounded">
									<img src="images/check.png" class="check-button" alt="Check"> 
									<span>Save Forum Roles</span>
								</button>
							</div>
						</form>					</div>				</div>			</div>		</div>	</div></div>
