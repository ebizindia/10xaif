<div class="row">
    <div id='rec_list_container' class="col-12 mt-3 mb-2">
        <div class="card">
        <div class="card-body">
            <div class="card-header-heading">
            <div class="row">
                <div class="col-8">
                    <h4 class="row pg_heading_line_ht"><?php echo \eBizIndia\_esc($this->body_template_data['page_title']); ?></h4>



                </div>
                <div class="col-4 text-right">
                    <div class="row" style="float:right;">
                        <div style="text-align:right;width: 100%;">
                        <!-- a class="btn btn-primary toggle-search" href="javascript:void(0);">
                            <img src="images/search-plus.png" class="custom-button fa-search-plus" alt="Search"><img src="images/search-minus.png" class="custom-button fa-search-minus" alt="Search">
                        </a -->
                        <?php 
                            if($this->body_template_data['can_add']===true){
                        ?>      
                                <a href="settings.php#mode=addrec" class="btn btn-success record-add-button rounded"  id="add-record-button"><!--<i class="fa fa-plus"></i>--><img src="images/plus.png" class="custom-button-small" alt="Plus"> <span class="hide_in_mobile"  >Add Settings</span> </a>
                        
                        <?php       
                            }
                        ?>  

                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="responsive-block-table for-cat">
        <p class="card-description"><?php echo \eBizIndia\_esc($this->body_template_data['page_description']); ?></p>    
        <div class="panel-body table-responsive">
            <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Setting Name</th>
                                    <th>Value</th>
                                    <th>Description</th>
                                    <th>Last Updated</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->body_template_data['settings'] as $setting): ?>
                                <tr>
                                    <td class="text-center">

                                        <div class="">
                                            <a href="settings.php#mode=<?php echo $action_mode; ?>&recid=<?php echo \eBizIndia\_esc($setting['setting_id']); ?>" class="btn btn-xs btn-success user-edit-action record-edit-button" data-in-mode="list-mode" data-recid="<?php echo \eBizIndia\_esc($setting['setting_id']); ?>" data-rel='tooltip' title="Edit details">
                                                <img src="images/edit-white.png" class="custom-button-small" alt="Edit">
                                            </a>
                                        </div>



                                        <!--button type="button" class="btn btn-sm btn-primary edit-setting" 
                                                data-id="<?php echo \eBizIndia\_esc($setting['id']); ?>"
                                                data-value="<?php echo \eBizIndia\_esc($setting['setting_value']); ?>">
                                            Edit
                                        </button -->
                                    </td>
                                    <td><?php echo \eBizIndia\_esc($setting['setting_name']); ?></td>
                                    <td>
                                        <span class="setting-value" data-id="<?php echo \eBizIndia\_esc($setting['id']); ?>">
                                            <?php echo \eBizIndia\_esc($setting['setting_value']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo \eBizIndia\_esc($setting['setting_description']); ?></td>
                                    <td>
                                        <?php echo \eBizIndia\_esc($setting['last_updated_on']); ?><br>
                                        <small>By: <?php echo \eBizIndia\_esc($setting['last_updated_by']); ?></small>
                                    </td>
                                    
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














<!-- div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?php echo \eBizIndia\_esc($this->body_template_data['page_title']); ?></h4>
                    <p class="card-description"><?php echo \eBizIndia\_esc($this->body_template_data['page_description']); ?></p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Setting Name</th>
                                    <th>Value</th>
                                    <th>Description</th>
                                    <th>Last Updated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->body_template_data['settings'] as $setting): ?>
                                <tr>
                                    <td><?php echo \eBizIndia\_esc($setting['setting_name']); ?></td>
                                    <td>
                                        <span class="setting-value" data-id="<?php echo \eBizIndia\_esc($setting['id']); ?>">
                                            <?php echo \eBizIndia\_esc($setting['setting_value']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo \eBizIndia\_esc($setting['setting_description']); ?></td>
                                    <td>
                                        <?php echo \eBizIndia\_esc($setting['last_updated_on']); ?><br>
                                        <small>By: <?php echo \eBizIndia\_esc($setting['last_updated_by']); ?></small>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary edit-setting" 
                                                data-id="<?php echo \eBizIndia\_esc($setting['setting_id']); ?>"
                                                data-value="<?php echo \eBizIndia\_esc($setting['setting_value']); ?>">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div -->

<!-- Edit Setting Modal -->
<div class="modal fade" id="editSettingModal" tabindex="-1" role="dialog" aria-labelledby="editSettingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSettingModalLabel">Edit Setting</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editSettingForm">
                    <input type="hidden" id="setting_id" name="setting_id">
                    <div class="form-group">
                        <label for="setting_value">Value</label>
                        <input type="text" class="form-control" id="setting_value" name="setting_value" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSettingBtn">Save changes</button>
            </div>
        </div>
    </div>
</div>