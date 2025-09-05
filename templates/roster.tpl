<div class="card">
    <div class="card-body">
        <div class="card-header-heading">
            <div class="row">
                <div class="col-8">
                    <h4 class="row pg_heading_line_ht">Roster</h4>
                </div>
            </div>
        </div>
        
        <?php if (isset($this->body_template_data['save_result'])): ?>
            <div class="alert <?php echo $this->body_template_data['save_result']['error_code'] == 0 ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo htmlspecialchars($this->body_template_data['save_result']['message']); ?>
            </div>
        <?php endif; ?>

        <form class="form-horizontal" role="form" name='rosterForm' id="rosterForm" action='roster.php' method='post'>
            <input type="hidden" name="mode" value="saveRoster">

            <!-- Year Selection -->
            <div class="form-group row">
                <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="Year">Year <span class="required">*</span></label>
                <div class="col-xs-12 col-sm-6 col-lg-4">
                    <select class="form-control" name="year" onchange="window.location='roster.php?year='+this.value;">
                        <?php foreach ($this->body_template_data['years_list'] as $year): ?>
                            <option value="<?php echo htmlspecialchars($year); ?>" 
                                    <?php echo $year === $this->body_template_data['selected_year'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($year); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Position Assignments -->
            <?php foreach ($this->body_template_data['roster_positions'] as $position_key => $position_label): ?>
                <div class="form-group row">
                    <label class="control-label col-xs-12 col-sm-6 col-lg-2" for="<?php echo $position_key; ?>">
                        <?php echo htmlspecialchars($position_label); ?>
                    </label>
                    <div class="col-xs-12 col-sm-6 col-lg-4">
                        <select class="form-control" name="roster[<?php echo $position_key; ?>]">
                            <option value="">Select Member</option>
                            <?php foreach ($this->body_template_data['members_list'] as $member): ?>
                                <option value="<?php echo $member['id']; ?>" 
                                        <?php echo isset($this->body_template_data['current_roster'][$position_key]) && 
                                                  $this->body_template_data['current_roster'][$position_key]['user_id'] == $member['id'] 
                                                  ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($member['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Submit Button -->
            <div class="form-actions form-group">
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                    <center>
                        <button class="btn btn-success rounded" type="submit" id="record-save-button">
                            <img src="images/check.png" class="check-button" alt="Check"> 
                            <span>Save Roster</span>
                        </button>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>