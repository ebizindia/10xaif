<style>
    #add_form_field_msgbody{
        min-height: 270px;
    }
    /*.cash_refund_card {
        display:table;
        border-collapse:collapse;
    }
    .cash_refund_card td{                                               
        padding:5px;
    }
    .cash_refund_card td:first-child{
        width:60% !important;
    }
    .cash_refund_card td:last-child{
        text-align:right;;
    }
    .bg-member{
        background:#d5ffdf;
    }
    .bg-grey-bold{
        background:#eaeaea;
        font-weight:600;
    }*/

    @-webkit-keyframes highlight-success {
        0% {
            background-color: #d4edda;
            opacity:1;
        }
        22% {
            background-color: #d8efde;
        }
        77% {

            background-color: #e3efe6;
        }
        100% {
            background-color: none;
        }
    }
        
    .highlight-success {
        -webkit-animation-name: highlight-success;
        -webkit-animation-duration: 1000ms;
        -webkit-animation-iteration-count: 1;
        -webkit-animation-timing-function: ease-in-out;
    } 

    @-webkit-keyframes highlight-error {
        0% {
            background-color: #f8d7da;
            opacity:1;
        }
        22% {
            background-color: #f8d7da;
        }
        77% {
            background-color: #f9e9ea;
        }
        100% {
            background-color: none;
        }
    }

    .highlight-error {
        -webkit-animation-name: highlight-error;
        -webkit-animation-duration: 1000ms;
        -webkit-animation-iteration-count: 1;
        -webkit-animation-timing-function: ease-in-out;
    } 

    
</style>
<div class="row">
    <div id='tkt_scan_form_container' class="col-12 mt-3 mb-2">
		<div class="card">
            <div class="card-body" style="height: 100vh;"  >
                
                <div class="card-header-heading">
                    <div class="row">
                        <div class="col-12">
                            <h4 id="panel-heading-text" class="pull-left row">Scan Tickets&nbsp;</h4>
                        </div>
                    </div>
                </div>

                <?php 
                    
                        require_once CONST_THEMES_TEMPLATE_INCLUDE_PATH.'scan-etickets-add.tpl';   
                    
                ?>
            </div>
        </div>    
    </div>

</div>
