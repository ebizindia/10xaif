<style>
.calendar-container {
    padding: 1.25rem;
    max-width: 800px;
    margin: 0 auto;
}

.page-title {
    font-weight: 500;
    color: #333;
    margin-bottom: 1.5rem;
}

.calendar-module {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

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

.calendar-grid {
    width: 100%;
    border-collapse: collapse;
}

.calendar-grid th {
    padding: 0.75rem;
    text-align: center;
    font-weight: 600;
    color: #333;
    /*background-color: #f8f9fa;*/
    background-color: #ffb6a6;
	border: 1px solid #fff;
    border-bottom: 1px solid #9f9f9f !important;
}

.calendar-grid td {
    position: relative;
    height: 45px;
    padding: 0;
    text-align: center;
    border:1px solid #c8c8c8;
    color: #303030;
}

.calendar-grid td.today {
    background: #e8f4ff;
    color: #054890;
    font-weight: bold;
}

.calendar-grid td.has-meeting {
    background: #e6ffe6;
    color: #006600;
}

.calendar-grid td.today.has-meeting {
    background: linear-gradient(135deg, #e8f4ff 50%, #e6ffe6 50%);
}

.meeting-day {
    display: block;
    text-decoration: none;
    color: #666;
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    line-height: 45px;
}

.meeting-day:hover {
    background-color: #006600;
    color: white !important;
    text-decoration: none;
}

/* Remove any text decoration from all links in the calendar */
.calendar-container a {
    text-decoration: none;
}

.calendar-container a:hover {
    text-decoration: none;
}

/* Meetings Table Styles */
.meetings-container {
    margin-top: 2rem;
}

.meetings-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.meetings-table th,
.meetings-table td {
    border: 1px solid #ddd;
    padding: 0.75rem;
    text-align: left;
}

.meetings-table th {
    background-color: #054890;
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
}

.meetings-table tr {
    cursor: pointer;
}

.meetings-table tr:nth-child(even) {
    background-color: #fafafa;
}

.meetings-table tr:hover td {
    background-color: #f9f9f9;
}
.meeting-table tr:hover{
cursor:pointer;
}
/* Mobile Responsive */
@media screen and (max-width: 768px) {
    .calendar-container {
        padding: 0.75rem;
    }
    
    .calendar-header {
        padding: 0.75rem;
        flex-direction: column;
    }
    
    .meetings-table {
        display: block;
        overflow-x: auto;
    }
    
    .meetings-table th,
    .meetings-table td {
        min-width: 120px;
    }
}
</style><div class="row">    <div id='feedback_form_container' class="col-12 mt-3 mb-2">		<div class="card">			<div class="card-body">				<div class="card-header-heading">					<div class="row">						<div class="col"><h4 class="row">Calendar</h4></div>					</div>				</div>				<div class="row">					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
							<div class="calendar-module">
								<div class="calendar-header">
									<div class="month-selector">
										<a href="calendar.php?month=<?php echo $this->body_template_data['calendar']['prev_month']; ?>&year=<?php echo $this->body_template_data['calendar']['prev_year']; ?>" class="nav-arrow">←</a>
										
										<select name="month" id="month-select" onchange="window.location.href='calendar.php?month=' + this.value + '&year=<?php echo $this->body_template_data['calendar']['current_year']; ?>'" class="month_select">
											<?php foreach ($this->body_template_data['calendar']['month_options'] as $num => $name): ?>
												<option value="<?php echo $num; ?>" <?php echo ($num == $this->body_template_data['calendar']['current_month']) ? 'selected' : ''; ?>>
													<?php echo htmlspecialchars($name); ?>
												</option>
											<?php endforeach; ?>
										</select>
										
										<select name="year" id="year-select" onchange="window.location.href='calendar.php?month=<?php echo $this->body_template_data['calendar']['current_month']; ?>&year=' + this.value" class="month_select">
											<?php foreach ($this->body_template_data['calendar']['year_options'] as $year): ?>
												<option value="<?php echo $year; ?>" <?php echo ($year == $this->body_template_data['calendar']['current_year']) ? 'selected' : ''; ?>>
													<?php echo $year; ?>
												</option>
											<?php endforeach; ?>
										</select>
										
										<a href="calendar.php?month=<?php echo $this->body_template_data['calendar']['next_month']; ?>&year=<?php echo $this->body_template_data['calendar']['next_year']; ?>" class="nav-arrow">→</a>
									</div>
								</div>
								
								<table class="calendar-grid">
									<thead>
										<tr>
											<th>M</th>
											<th>T</th>
											<th>W</th>
											<th>T</th>
											<th>F</th>
											<th>S</th>
											<th>S</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($this->body_template_data['calendar']['calendar_grid'] as $week): ?>
											<tr>
												<?php foreach ($week as $day): ?>
													<td <?php 
														$classes = [];
														if ($day == $this->body_template_data['calendar']['today']['day'] && 
															$this->body_template_data['calendar']['current_month'] == $this->body_template_data['calendar']['today']['month'] && 
															$this->body_template_data['calendar']['current_year'] == $this->body_template_data['calendar']['today']['year']) {
															$classes[] = 'today';
														}
														if (!empty($day) && isset($this->body_template_data['calendar']['meetings']['days'][$day])) {
															$classes[] = 'has-meeting';
														}
														if (!empty($classes)) {
															echo 'class="' . implode(' ', $classes) . '"';
														}
													?>>
														<?php 
														if (!empty($day)) {
															if (isset($this->body_template_data['calendar']['meetings']['lookup'][$day])) {
																$meetingId = $this->body_template_data['calendar']['meetings']['lookup'][$day];
																echo '<a href="meetings.php#mode=edit&recid=' . $meetingId . '" class="meeting-day">' . $day . '</a>';
															} else {
																echo $day;
															}
														}
														?>
													</td>
												<?php endforeach; ?>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
							
							<?php if (!empty($this->body_template_data['calendar']['meetings']['list'])): ?>
							<div class="meetings-container">
								<h4 class="page-title">Meetings This Month</h4>
								<div class="panel-body table-responsive"><table class="table table-striped table-bordered table-hover meeting-table">
										<thead>
											<tr>
												<th>Date</th>
												<th>Time</th>
												<th>Venue</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($this->body_template_data['calendar']['meetings']['list'] as $meeting): ?>
												<tr onclick="window.location.href='meetings.php#mode=edit&recid=<?php echo $this->body_template_data['calendar']['meetings']['lookup'][date('j', strtotime($meeting['date']))]; ?>'">
													<td><?php echo htmlspecialchars($meeting['formatted_date']); ?></td>
													<td><?php echo htmlspecialchars($meeting['time']); ?></td>
													<td><?php echo htmlspecialchars($meeting['venue']); ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>							
							</div>
							<?php endif; ?>
						</div>				
					</div>			
				</div>		
			</div>	
		</div>
	</div>						