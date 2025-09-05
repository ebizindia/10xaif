
<div class="card">
    <div class="card-body">
        <div class="card-header-heading">
            <div class="row">
                <div class="col-6"><h4 id="panel-heading-text" class="pull-left row panel-heading"><?php \eBizIndia\_esc(empty($this->body_template_data['panel_heading'])?'Page Not Found':$this->body_template_data['panel_heading']); ?></h4></div>

                <div class="col-6 text-right">
    <a href="event-registrations.php#" class="btn btn-danger record-list-show-button back-to-list-button row" id="back-to-list-button">
    <img src="images/left-arrow.png" class="custom-button" alt="Left"> Back To Events List </a>

    <a href="event-registrations.php#" class="btn btn-danger record-list-show-button back-to-list-button row mobile-bck-to-list"  id="back-to-list-button"><img src="images/left-arrow.png" class="custom-button" alt="Left"> </a>

                </div>
            </div>
        </div>

        <div class="row" id="booking_success_msg"  >
             <div class="col <?php echo $this->body_template_data['event_reg_msg_class']; ?>" >
                    <!-- <h4>Thank You!</h4>   -->
                    <span class="msg-text"  ><?php echo (empty($this->body_template_data['panel_heading'])?'You have visited an invalid URL.':$this->body_template_data['event_reg_msg']); ?></span><br><br>
                   
             </div>   
        </div>
        
    </div>
</div>



