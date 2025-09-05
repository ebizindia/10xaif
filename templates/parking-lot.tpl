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

.meetings-list {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.meeting-input {
    flex: 1;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.remove-row {
    padding: 8px 12px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;    height:40px;
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
</style>
<div class="row">    
    <div id='feedback_form_container' class="col-12 mt-3 mb-2"> 
        <div class="card">          
            <div class="card-body">             
                <div class="card-header-heading">                   
                    <div class="row">                       
                        <div class="col"><h4 class="row">Parking Lot</h4></div>                 
                    </div>              
                </div>      
                <div class="row">                   
                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <?php if (!empty($this->body_template_data['result'])): 
         if ($this->body_template_data['result']['error_code']>0): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($this->body_template_data['result']['message']) ?></div>
         <?php else: ?>
            <div class="alert alert-success"><?= htmlspecialchars($this->body_template_data['result']['message']) ?></div>
    <?php endif; endif;  ?>

            <div class="alert alert-success d-none" id="delMsg" ></div>

    <form action="parking-lot.php" method="post">
        <input type="hidden" name="mode" value="saveParkingLot">
        <div class="panel-body table-responsive">                           
            <table class="table table-striped table-bordered table-hover" id="parking-lot-table">
       
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Date</th>
                    <th>Notes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                
                <?php 
                   

                    if (!empty($this->body_template_data['parking_entries'])): ?>
                    <?php foreach ($this->body_template_data['parking_entries'] as $entry): ?>
                        <tr>
                            <td style="vertical-align: top;">
                                
                                <select name="members[]" class="form-control" required>
                                    <option value="">Select Member</option>
                                    <?php foreach ($this->body_template_data['members_list'] as $member): ?>
                                    <option value="<?php echo $member['id']; ?>"  <?php if($entry['name']==$member['id']): ?> selected <?php endif; ?>  >
                                    <?php echo htmlspecialchars($member['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                
                            </td>
                            <td style="vertical-align: top;">
                                <input type="date" name="date[]" class="form-control meeting-input" value="<?= htmlspecialchars($entry['date']) ?>" required>
                            </td>
                            
                            <td style="vertical-align: top;">
                               <textarea name="notes[]" class="form-control reflection-textarea" rows="5" ><?php echo str_replace(" | ","\n", $entry['description']); ?></textarea>

                            </td>
                            <td><input type="hidden" name="rec[]" value="<?php echo $entry['id']; ?>"><button type="button" class="btn btn-danger remove-row" data-id="<?php echo $entry['id']; ?>">X</button></td> <!-- First row has no remove button -->
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                         <td style="vertical-align: top;">
                            <select name="members[]" class="form-control" required>
                                    <option value="">Select Member</option>
                                    <?php foreach ($this->body_template_data['members_list'] as $member): ?>
                                    <option value="<?php echo $member['id']; ?>" >
                                    <?php echo htmlspecialchars($member['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                        </td>
                        <td style="vertical-align: top;"><input type="date" name="date[]" class="form-control meeting-input" required></td>
                       
                        <td style="vertical-align: top;"><textarea name="notes[]" class="form-control reflection-textarea" rows="5" ></textarea></td>
                        <td><input type="hidden" name="rec[]" value=""></td> <!-- First row has no remove button -->
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
   <div class="col-md-12" style="padding-left: 2px;text-align:left;">
        <button type="button" class="btn btn-primary" id="add-row"><img src="images/plus.png" class="check-button" alt="add row"> Add Row</button>
    </div>
    <div class="col-md-12 mt-3 mb-2" style="clear: both;text-align:center;">    
        <button type="submit" class="btn btn-success" style=""><img src="images/check.png" class="check-button" alt="Check"> <span>Save Note(s)</span></button>
    </div>    
    </div>
    </form>
    </div>
</div>
</div>
  </div>
  </div>  
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Add new row
    $("#add-row").click(function () {
            var newRow = `<tr>
                <td style="vertical-align: top;">
                    <select name="members[]" class="form-control" required>
                                    <option value="">Select Member</option>
                                    <?php foreach ($this->body_template_data['members_list'] as $member): ?>
                                    <option value="<?php echo $member['id']; ?>" >
                                    <?php echo htmlspecialchars($member['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                </td>
                <td style="vertical-align: top;"><input type="date" name="date[]" class="form-control meeting-input" required></td>
                
                <td style="vertical-align: top;"><textarea name="notes[]" class="form-control reflection-textarea" rows="5" ></textarea></td>
                <td><input type="hidden" name="rec[]" value=""><button type="button" class="btn btn-danger remove-row" data-id="<?php echo $entry['id']; ?>">X</button></td>
            </tr>`;
            $("#parking-lot-table tbody").append(newRow);
        });

    // Remove row via AJAX
    $(document).on("click", ".remove-row", function () {
        var row = $(this).closest("tr");
        var id = $(this).data("id");

        if (id) {
            $.ajax({
                type: "POST",
                url: "parking-lot.php",
                data: { mode: "deleteParkingLot", id: id },
                dataType: "json",
                success: function (response) {
                    if (response.error_code === 0) {
                        row.remove();
                        $("#delMsg").text(response.message).removeClass("d-none alert-danger").addClass("alert-success");
                    } else {
                        //alert(response.message);
                        $("#delMsg").text(response.message).removeClass("d-none alert-success").addClass("alert-danger");
                    }
                }
            });
        } else {
            row.remove();
        }
    });
});
</script>

<!-- script>
    $(document).ready(function () {
        // Add new row
        $("#add-row").click(function () {
            var newRow = `<tr>
                <td style="vertical-align: top;">
                    <select name="members[]" class="form-control" required>
                                    <option value="">Select Member</option>
                                    <?php foreach ($this->body_template_data['members_list'] as $member): ?>
                                    <option value="<?php echo $member['id']; ?>" >
                                    <?php echo htmlspecialchars($member['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                </td>
                <td style="vertical-align: top;"><input type="date" name="date[]" class="form-control meeting-input" required></td>
                
                <td style="vertical-align: top;"><textarea name="notes[]" class="form-control reflection-textarea" rows="5" ></textarea></td>
                <td><input type="hidden" name="rec[]" value=""><button type="button" class="btn btn-danger remove-row" data-id="<?php echo $entry['id']; ?>>X</button></td>
            </tr>`;
            $("#parking-lot-table tbody").append(newRow);
        });

        // Remove row (only if more than one row exists)
        $(document).on("click", ".remove-row", function () {
            if ($("#parking-lot-table tbody tr").length > 1) {
                $(this).closest("tr").remove();
            }
        });
    });
</script -->
