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
    transition: all 0.3s ease;
}

.year-link:hover {
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transform: translateY(-2px);
    text-decoration: none;
    color: #333;
}

.year-link.active {
    background-color: #054890;
    color: white;
    border-color: #054890;
    text-decoration: none;
}

.goal-card {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.goal-card th, .goal-card td {
    border: 1px solid #ddd;
    padding: 10px;
    vertical-align: top;
}

.goal-card th {
    background-color: #f5f5f5;
}

.users-list {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 20px;
    background: #f9f9f9;
}

.users-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
}

.user-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
}

.user-card:hover {
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transform: translateY(-2px);
    text-decoration: none;
    color: #333;
}

.user-card.active {
    border-color: #054890;
    background-color: #f0f5ff;
    text-decoration: none;
    color: #333;
}

.user-name {
    font-weight: bold;
    font-size: 0.95em;
    text-align: center;
}

.user-details {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.goal-content {
    white-space: pre-wrap;
    line-height: 1.4;
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

    .users-grid {
        grid-template-columns: 1fr;
    }
}
</style><div class="row">    <div id='feedback_form_container' class="col-12 mt-3 mb-2">		<div class="card">			<div class="card-body">				<div class="card-header-heading">					<div class="row">						<div class="col"><h4 class="row">All Goal Cards</h4></div>					</div>				</div>				<div class="row">					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">												<div class="users-list">							<h4>Select Member</h4>							<div class="users-grid">								<?php foreach ($this->body_template_data['users'] as $user): ?>									<a href="all-goals.php?user_id=<?php echo $user['id']; ?>&year=<?php echo $this->body_template_data['selected_year']; ?>" 									   class="user-card <?php echo $user['id'] == $this->body_template_data['selected_user_id'] ? 'active' : ''; ?>">										<div class="user-name"><?php echo htmlspecialchars($user['name']); ?></div>									</a>								<?php endforeach; ?>							</div>						</div>
												<?php if ($this->body_template_data['selected_user_id']): ?>        <?php if ($this->body_template_data['user_details']): ?>            <div class="user-details">                <h3><?php echo htmlspecialchars($this->body_template_data['user_details']['name']); ?></h3>            </div>                        <div class="years-list">                <?php foreach ($this->body_template_data['years_list'] as $year): ?>                    <a href="all-goals.php?user_id=<?php echo $this->body_template_data['selected_user_id']; ?>&year=<?php echo $year; ?>"                        class="year-link <?php echo $year === $this->body_template_data['selected_year'] ? 'active' : ''; ?>">                        <?php echo htmlspecialchars($year); ?>                    </a>                <?php endforeach; ?>            </div>		<div class="panel-body table-responsive">            <table class="table table-striped table-bordered table-hover">                <thead>                    <tr>                        <th>Category</th>                        <th>Goal</th>                        <th>Significance</th>                        <th>Action Planned</th>                        <th>Mid Term Review</th>                        <th>Final Review</th>                    </tr>                </thead>                <tbody>                    <?php foreach ($this->body_template_data['categories'] as $cat_key => $cat_label): ?>                        <tr>                            <td data-label="Category"><?php echo htmlspecialchars($cat_label); ?></td>                            <td data-label="Goal">                                <div class="goal-content"><?php                                     echo htmlspecialchars($this->body_template_data['goal_card'][$cat_key]['goal'] ?? '');                                 ?></div>                            </td>                            <td data-label="Significance">                                <div class="goal-content"><?php                                     echo htmlspecialchars($this->body_template_data['goal_card'][$cat_key]['significance'] ?? '');                                 ?></div>                            </td>                            <td data-label="Action Planned">                                <div class="goal-content"><?php                                     echo htmlspecialchars($this->body_template_data['goal_card'][$cat_key]['action_planned'] ?? '');                                 ?></div>                            </td>                            <td data-label="Mid Term Review">                                <div class="goal-content"><?php                                     echo htmlspecialchars($this->body_template_data['goal_card'][$cat_key]['mid_review'] ?? '');                                 ?></div>                            </td>                            <td data-label="Final Review">                                <div class="goal-content"><?php                                     echo htmlspecialchars($this->body_template_data['goal_card'][$cat_key]['final_review'] ?? '');                                 ?></div>                            </td>                        </tr>                    <?php endforeach; ?>                </tbody>            </table>		</div>        <?php endif; ?>    <?php else: ?>        <div class="alert alert-info">            Please select a member to view their goal cards.        </div>    <?php endif; ?>																	</div>				</div>			</div>		</div>	</div></div>

    
    
    

    
