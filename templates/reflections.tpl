<style>
.reflections-container {
    padding: 10px;
}

.months-list {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}


/* Keep active state text white even on hover */
.month-link.active:hover,
.month-link.active:focus,
.month-link.active:active {
    color: white;
    text-decoration: none;
}

.month-link {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
}

/* Add these rules to prevent underline on hover */
 
.month-link:focus, 
.month-link:active {
    text-decoration: none;
    color: #333;
}

.month-link.active {
    background-color: #ff795c;
    color: white;
    border-color: #ff795c;
}
.month-link:hover {    background-color: #ff795c;    color: white;text-decoration: none;    border-color: #ff795c;}

.reflection-card {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
}

.reflection-card th, .reflection-card td {
    border: 1px solid #ddd;
    padding: 10px;
}

.reflection-card th {
    background-color: #f5f5f5;
}

.reflection-textarea {
    width: 100%;
    min-height: 80px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
}

.health-scores {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.score-item {
    flex: 1;
    min-width: 200px;
}



/*For the Month selector*/

	.calendar-header {
	    padding: 1rem;
	    background: #f5f5f5;
	    border-bottom: 1px solid #ddd;
	    display: flex;
	    justify-content: center;
	    align-items: center;
	    gap: 1rem;
	}

	.month-selector {
	    display: flex;
	    align-items: center;
	    gap: 0.5rem;
	}
	.month_select{
	font-size:14px;
	  padding: 5px 10px;
	}
	.nav-arrow {
	    background: none;
	    border: none;
	    font-size: 1.25rem;
	    color: #054890;
	    cursor: pointer;
	    padding: 0.5rem;
	    display: flex;
	    align-items: center;
	    justify-content: center;
	    text-decoration: none;
	}

	.nav-arrow:hover {
	    background: #e9ecef;
	    border-radius: 4px;
	    text-decoration: none;
	}

	select {
	    padding: 0.5rem;
	    border: 1px solid #ddd;
	    border-radius: 4px;
	    font-size: 1rem;
	    background: white;
	}

/************************/

/* Mobile Styles */
@media screen and (max-width: 768px) {
    .reflection-card {
        display: block;
    }
    
    .reflection-card thead {
        display: none;
    }
    
    .reflection-card tbody {
        display: block;
    }
    
    .reflection-card tr {
        display: block;
        margin-bottom: 20px;
        border: 1px solid #ddd;
    }
    
    .reflection-card td {
        display: block;
        border: none;
        border-bottom: 1px solid #eee;
    }
    
    .reflection-card td::before {
        content: attr(data-label);
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }
    
    .reflection-card td:last-child {
        border-bottom: none;
    }
}
</style><div class="row">    <div id='feedback_form_container' class="col-12 mt-3 mb-2">		<div class="card">			<div class="card-body">				<div class="card-header-heading">					<div class="row">						<div class="col"><h4 class="row">Monthly Reflections</h4></div>					</div>				</div>				<div class="row">					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
						<?php if (isset($this->body_template_data['save_result'])): ?>
							<div class="alert <?php echo $this->body_template_data['save_result']['error_code'] == 0 ? 'alert-success' : 'alert-danger'; ?>">
								<?php echo htmlspecialchars($this->body_template_data['save_result']['message']); ?>
							</div>
						<?php endif; ?>
						
						<div class="months-list">

							<div class="calendar-header col-12">
								<div class="month-selector">
									<a id="prev_mnth_getter" href="#"  class="nav-arrow"  >←</a>
									
									<select name="month" id="month_select" class="month_select">
										<?php foreach ($this->body_template_data['month_options'] as $num => $name): ?>
											<option value="<?php echo $num; ?>" <?php echo ($num == $this->body_template_data['sel_m']) ? ' selected ' : ''; ?>>
												<?php echo htmlspecialchars($name); ?>
											</option>
										<?php endforeach; ?>
									</select>
									
									<select name="year" id="year_select" class="month_select">
										<?php foreach ($this->body_template_data['year_options'] as $year): ?>
											<option value="<?php echo $year; ?>" <?php echo ($year == $this->body_template_data['sel_yr']) ? ' selected ' : ''; ?>>
												<?php echo $year; ?>
											</option>
										<?php endforeach; ?>
									</select>
									
									<a id="next_mnth_getter" href="#" class="nav-arrow">→</a>
								</div>
							</div>
							<?php 
								// foreach ($this->body_template_data['months_list'] as $month): 
							?>
								<!-- <a href="reflections.php?month=<?php echo $month; ?>" 
								   class="month-link <?php echo $month === $this->body_template_data['selected_month'] ? 'active' : ''; ?>">
									<?php echo date('F Y', strtotime($month . '-01')); ?>
								</a> -->
							<?php 
								// endforeach; 
							?>
						</div>

						<form action="reflections.php" method="post">
							<input type="hidden" name="mode" value="saveReflection">
							<input type="hidden" name="month_year" value="<?php echo htmlspecialchars($this->body_template_data['selected_month']); ?>">
							
							<div class="health-scores">
								<div class="score-item">
									<label>Physical Health (1-10)</label>
									<input type="number" name="health_scores[physical]" min="1" max="10" class="form-control" 
										   value="<?php echo htmlspecialchars($this->body_template_data['reflection_data']['reflections']['business']['physical_health_score'] ?? ''); ?>">
								</div>
								<div class="score-item">
									<label>Mental Health (1-10)</label>
									<input type="number" name="health_scores[mental]" min="1" max="10" class="form-control"
										   value="<?php echo htmlspecialchars($this->body_template_data['reflection_data']['reflections']['business']['mental_health_score'] ?? ''); ?>">
								</div>
								<div class="score-item">
									<label>Financial Health (1-10)</label>
									<input type="number" name="health_scores[financial]" min="1" max="10" class="form-control"
										   value="<?php echo htmlspecialchars($this->body_template_data['reflection_data']['reflections']['business']['financial_health_score'] ?? ''); ?>">
								</div>
								<div class="score-item">
									<label>Community (1-10)</label>
									<input type="number" name="health_scores[community]" min="1" max="10" class="form-control"
										   value="<?php echo htmlspecialchars($this->body_template_data['reflection_data']['reflections']['business']['community_score'] ?? ''); ?>">
								</div>
							</div>

							<div class="panel-body table-responsive">							<table class="table table-striped table-bordered table-hover">
								<thead>
									<tr>
										<th>Category</th>
										<th>The Situation (10%)</th>
										<th>The Significance & Impact (80%)</th>
										<th>Feelings (10%)</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($this->body_template_data['categories'] as $cat_key => $cat_label): ?>
										<tr>
											<td data-label="Category"><?php echo htmlspecialchars($cat_label); ?></td>
											<td data-label="Situation">
												<textarea name="reflections[<?php echo $cat_key; ?>][situation]" class="reflection-textarea" 
														placeholder="What happened that lead to this being a top or bottom 5% insight?"><?php 
													echo htmlspecialchars($this->body_template_data['reflection_data']['reflections'][$cat_key]['situation'] ?? ''); 
												?></textarea>
											</td>
											<td data-label="Significance">
												<textarea name="reflections[<?php echo $cat_key; ?>][significance]" class="reflection-textarea "
														placeholder="Ask 'so what?' X3 to peel back and dig deep"><?php 
													echo htmlspecialchars($this->body_template_data['reflection_data']['reflections'][$cat_key]['significance'] ?? ''); 
												?></textarea>
											</td>
											<td data-label="Feelings">
												<textarea name="reflections[<?php echo $cat_key; ?>][feelings]" class="reflection-textarea "
														placeholder="List 3-4 feelings"><?php 
													echo htmlspecialchars($this->body_template_data['reflection_data']['reflections'][$cat_key]['feelings'] ?? ''); 
												?></textarea>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
							</div>	
							<div class="card mb-4">
								<div class="card-header">
									<h5 class="mb-0">Looking Forward</h5>
								</div>
								<div class="card-body">
									<textarea name="lookforward" class="reflection-textarea mt-3" 
											placeholder="I am looking forward to the following over the next 30-60 days..."><?php 
										echo htmlspecialchars($this->body_template_data['reflection_data']['lookforward']['lookforward_text'] ?? ''); 
									?></textarea>
								</div>
							</div>

							<div class="card mb-4">
								<div class="card-header">
									<h5 class="mb-0">One Aha Moment or Brilliant Idea</h5>
								</div>
								<div class="card-body">
									<textarea name="insight" class="reflection-textarea mt-3"><?php 
										echo htmlspecialchars($this->body_template_data['reflection_data']['insight']['insight_text'] ?? ''); 
									?></textarea>
								</div>
							</div>


							
							<div class="card mb-4">
								<div class="card-header">
									<h5 class="mb-0">Energy Vampire</h5>
								</div>
								<div class="card-body">
									<textarea name="energy_vampire" class="reflection-textarea mt-3" 
											placeholder="One energy vampire in my life right now (something or someone)..."><?php 
										echo htmlspecialchars($this->body_template_data['reflection_data']['reflections']['business']['energy_vampire'] ?? ''); 
									?></textarea>
								</div>
							</div>
							
							<div class="card mb-4">
								<div class="card-header">
									<h5 class="mb-0">Reluctant Topic</h5>
								</div>
								<div class="card-body">
									<textarea name="reluctant_topic" class="reflection-textarea mt-3" 
											placeholder="Something I'm reluctant to talk about..."><?php 
										echo htmlspecialchars($this->body_template_data['reflection_data']['reflections']['business']['reluctant_topic'] ?? ''); 
									?></textarea>
								</div>
							</div>
							
							<div class="card mb-4">
								<div class="card-header">
									<h5 class="mb-0">Current Issue</h5>
									<small>(everyone answers this last question every month)</small>
								</div>
								<div class="card-body">
									<textarea name="current_issue" class="reflection-textarea mt-3" 
											placeholder="A current issue or question I'd like to explore with the group today..."><?php 
										echo htmlspecialchars($this->body_template_data['reflection_data']['reflections']['business']['current_issue'] ?? ''); 
									?></textarea>
								</div>
							</div>

							<div class="text-center mt-4 mb-4">
								<button type="submit" class="btn btn-success rounded">Save Reflection</button>
							</div>
						</form>
					</div>				</div>			</div>		</div>	</div></div>						

<script>
document.addEventListener('DOMContentLoaded', function() {



    $('#prev_mnth_getter').on('click', e=>{
    	e.preventDefault();
		const curr_selected_month = parseInt($('#month_select').val());
		const curr_selected_year = parseInt($('#year_select').val());
		let month = curr_selected_month-1;
		let year = curr_selected_year;
		if(month<1){
			month = 12;
			year -= 1; 
		}

		if($('#month_select').find(`option[value=${month}]`).length>0 && $('#year_select').find(`option[value=${year}]`).length>0){
			loadMonthPage(month, year);
		}else{
			alert('Sorry, you cannot go back any further in the past.');
		}
	});

	$('#next_mnth_getter').on('click', e=>{
		e.preventDefault();
		const curr_selected_month = parseInt($('#month_select').val());
		const curr_selected_year = parseInt($('#year_select').val());
		let month = curr_selected_month+1;
		let year = curr_selected_year;
		if(month>12){
			month = 1;
			year += 1; 
		}

		if($('#month_select').find(`option[value=${month}]`).length>0 && $('#year_select').find(`option[value=${year}]`).length>0){
			loadMonthPage(month, year);
		}else{
			alert('Sorry, you cannot go back any further in the future.');
		}
	});

	$('#month_select,#year_select').on('change', e=>{
		const month = parseInt($('#month_select').val());
		const year = parseInt($('#year_select').val());
		loadMonthPage(month, year);

	});

	function loadMonthPage(month, year){
		let url = window.location.pathname;
		if(month>=1 && month<=12 && year!=''){
			month = month+'';
			url += '?month='+encodeURIComponent(`${year}-${month.padStart(2,'0')}`);
		}

		window.location.replace(url);
	}


});
</script>