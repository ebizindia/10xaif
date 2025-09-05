<style>
.view-forum-roles-container {
    padding: 1.25rem;
    max-width: 1400px;
    margin: 0 auto;
}

.view-roles-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1.25rem;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.view-roles-table th, 
.view-roles-table td {
    border: 1px solid #ddd;
    padding: 0.75rem;
    text-align: left;
}

.view-roles-table th {
    background-color: #f5f5f5;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
}

.view-roles-table thead th {
    background-color: #054890;
    color: white;
}

.view-roles-table tr:hover {
    background-color: #f9f9f9;
}

.view-roles-table tr:nth-child(even) {
    background-color: #fafafa;
}

.role-position {
    font-weight: 600;
    color: #333;
}

.member-name {
    color: #666;
}

.table-wrapper {
    max-height: 800px;
    overflow-x: auto;
    margin-top: 1.25rem;
    border: 1px solid #ddd;
    border-radius: 0.25rem;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    color: #054890;
    text-decoration: none;
    font-weight: 500;
}

.back-link:hover {
    text-decoration: underline;
}

/* Mobile Styles */
@media screen and (max-width: 768px) {
    .view-forum-roles-container {
        padding: 1rem;
    }

    .view-roles-table {
        display: block;
    }
    
    .view-roles-table th {
        position: relative;
    }

    .table-wrapper {
        max-height: none;
        border: none;
    }

    .view-roles-table td, 
    .view-roles-table th {
        min-width: 160px;
    }
}
</style>
<div class="row">
    <div id='feedback_form_container' class="col-12 mt-3 mb-2">
		<div class="card">
			<div class="card-body">
				<div class="card-header-heading">
					<div class="row">
						<div class="col"><h4 class="row">View Forum Roles</h4></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
						<div class="panel-body table-responsive">
							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Position</th>
										<?php foreach ($this->body_template_data['years_list'] as $year): ?>
											<th><?php echo htmlspecialchars($year); ?></th>
										<?php endforeach; ?>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($this->body_template_data['forum_positions'] as $position_key => $position_label): ?>
										<tr>
											<td class="role-position"><?php echo htmlspecialchars($position_label); ?></td>
											<?php foreach ($this->body_template_data['years_list'] as $year): ?>
												<td class="member-name">
													<?php 
														if (isset($this->body_template_data['all_roles'][$year][$position_key])) {
															echo htmlspecialchars($this->body_template_data['all_roles'][$year][$position_key]['name']);
														} else {
															echo '—';
														}
													?>
												</td>
											<?php endforeach; ?>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>