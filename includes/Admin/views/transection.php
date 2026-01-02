<?php wp_head(); ?>
<div class="wrap">
    <div class="eps-header">
        <div class="eps-left">
            <h3><?php _e( 'EPS (Easy Payment System)', 'mc-eps' ); ?>  
            <?php
            $type = get_option('mc_eps_settings'); 
            $type = unserialize($type);
            $mode = $type['mode'] ?? 'sandbox';
            ?><?php  if($mode == 'sandbox') { ?>

                         <small style="color:red;font-size:12px;">Sandbox</small>
                         

                     <?php  } else {?>

                        
                        <small style="color:green;font-size:12px;">Live</small>
                      
                     <?php }  ?></h3>
    
            
        </div>

        <div class="eps-right">
        
            <button id="eps-sync-12" class="btn eps-btn-outline button button-primary" data-range="12_months">Sync with Gateway (12 Months)</button>
            <button id="eps-sync-7" class="btn eps-btn-primary button" data-range="7_days">
                Sync with Gateway (7 days)
            </button>
        </div>
    </div>

        <div class="eps-status-filter">
            <label>Select Status</label> 
           <select id="responseFilter" class="status-dropdown">
                <option value="">All Status</option>
                <option value="Initialize">Initialize</option>
                <option value="Success" selected>Success</option>
                <option value="Failure">Failure</option>
                <option value="Cancel">Cancel</option>
            </select>
           
            
        </div>
    <table id="eps_transections_show" class="table" width="100%" >
    <thead>
        <th>ID</th>
        <th>Transection Id</th>
        <th>Order Id</th>
        <th>Payment Status</th>
        <th>Total Amount</th>
        <th>Financial Entity</th>
        <th>Transaction Date</th>
        <th>Product Status</th>
        
    </thead>
    <tbody></tbody>
</table>

    </div>