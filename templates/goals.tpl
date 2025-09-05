<style>
.goals-container {
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
}.year-link:hover {text-decoration: none;background-color: #ff795c;    color: white;    border-color: #ff795c;}

.year-link.active {
    background-color: #ff795c;
    color: white;
    border-color: #ff795c;
}

.goal-card {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.goal-card th, .goal-card td {
    border: 1px solid #ddd;
    padding: 10px;
}

.goal-card th {
    background-color: #f5f5f5;
}

.goal-textarea {
    min-height: 80px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
}

/* Mobile Styles */
@media screen and (max-width: 768px) {
    .goal-card {
        display: block;
    }
    
    .goal-card thead {
        display: none;
    }
    
    .goal-card tbody {
        display: block;
    }
    
    .goal-card tr {
        display: block;
        margin-bottom: 20px;
        border: 1px solid #ddd;
    }
    
    .goal-card td {
        display: block;
        border: none;
        border-bottom: 1px solid #eee;
    }
    
    .goal-card td::before {
        content: attr(data-label);
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }
    
    .goal-card td:last-child {
        border-bottom: none;
    }
}
</style><div class="row">    <div id='feedback_form_container' class="col-12 mt-3 mb-2">		<div class="card">			<div class="card-body">				<div class="card-header-heading">					<div class="row">						<div class="col"><h4 class="row">Goal Cards</h4></div>					</div>				</div>				<div class="row">					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
						<?php if (isset($this->body_template_data['save_result'])): ?>
							<div class="alert <?php echo $this->body_template_data['save_result']['error_code'] == 0 ? 'alert-success' : 'alert-danger'; ?>">
								<?php echo htmlspecialchars($this->body_template_data['save_result']['message']); ?>
							</div>
						<?php endif; ?>
						
						<div class="years-list">
							<?php foreach ($this->body_template_data['years_list'] as $year): ?>
								<a href="goals.php?year=<?php echo $year; ?>" 
								   class="year-link <?php echo $year === $this->body_template_data['selected_year'] ? 'active' : ''; ?>">
									<?php echo htmlspecialchars($year); ?>
								</a>
							<?php endforeach; ?>
						</div>

						<form action="goals.php" method="post">
							<input type="hidden" name="mode" value="saveGoals">
							<input type="hidden" name="year" value="<?php echo htmlspecialchars($this->body_template_data['selected_year']); ?>">
							<div class="panel-body table-responsive">							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Category</th>
										<th style="width:120px;">Goal</th>
										<th>Significance</th>
										<th>Action Planned</th>
										<th>Mid Term Review</th>
										<th>Final Review</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($this->body_template_data['categories'] as $cat_key=> $cat_label): ?>
										<tr>
											<td data-label="Category"><?php echo htmlspecialchars($cat_label); ?></td>
											<td data-label="Goal" style="width:120px;">
												<textarea name="goals[<?php echo $cat_key; ?>][goal]" class="goal-textarea"><?php 
													echo htmlspecialchars($this->body_template_data['goal_card'][$cat_key]['goal'] ?? ''); 
												?></textarea>
											</td>
											<td data-label="Significance">
												<textarea name="goals[<?php echo $cat_key; ?>][significance]" class="goal-textarea"><?php 
													echo htmlspecialchars($this->body_template_data['goal_card'][$cat_key]['significance'] ?? ''); 
												?></textarea>
											</td>
											<td data-label="Action Planned">
												<textarea name="goals[<?php echo $cat_key; ?>][action_planned]" class="goal-textarea"><?php 
													echo htmlspecialchars($this->body_template_data['goal_card'][$cat_key]['action_planned'] ?? ''); 
												?></textarea>
											</td>
											<td data-label="Mid Term Review">
												<textarea name="goals[<?php echo $cat_key; ?>][mid_review]" class="goal-textarea"><?php 
													echo htmlspecialchars($this->body_template_data['goal_card'][$cat_key]['mid_review'] ?? ''); 
												?></textarea>
											</td>
											<td data-label="Final Review">
												<textarea name="goals[<?php echo $cat_key; ?>][final_review]" class="goal-textarea"><?php 
													echo htmlspecialchars($this->body_template_data['goal_card'][$cat_key]['final_review'] ?? ''); 
												?></textarea>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>							</div>
							
							<div style="margin-top: 20px; text-align: center;">
								<button type="submit" class="btn btn-success rounded">Save Goal Cards</button>
							</div>
						</form>
					</div>				</div>			</div>		</div>	</div></div>					