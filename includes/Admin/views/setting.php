
<div class="wrap">

    <h1><?php _e( 'EPS Settings', 'mc-eps' ); ?></h1>

    <?php if ( isset($this->errors['error_message' ] ) ) { ?>  <p class="description error" style ="color:#CA0B00"><?php echo $this->errors['error_message' ]; ?></p> <?php } ?>   

    <?php if ( isset($this->successes['success_message' ] ) ) { ?>  <p class="description" style ="color:#22bb33"><?php echo $this->successes['success_message' ]; ?></p> <?php } ?>   

    <form action="" method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="module_val"><?php _e( 'User Name', 'mc-eps' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="module_val" required = "true" id="module_val" class="regular-text" value="<?php echo  esc_attr($values['module_val']); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="password"><?php _e( 'Password', 'mc-eps' ); ?></label>
                    </th>
                    <td>
                        <input type="password" name="password" required = "true" id="password" class="regular-text" value="<?php echo  esc_attr($values['password']); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="merchent_code"><?php _e( 'Hash Key', 'mc-eps' ); ?></label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" required = "true" name="merchent_code" id="merchent_code" value="<?php echo  esc_attr($values['merchent_code']); ?>">
                    </td>
                </tr>

               <tr >
                    <th scope="row">
                        <label for="api_base_url"><?php _e( 'Merchant Id', 'mc-eps' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="api_base_url"  id="api_base_url" class="regular-text" value="<?php echo  esc_attr($values['api_base_url']); ?>">
                    </td>
                </tr>

                 <tr>
                    <th scope="row">
                        <label for="redirect_url"><?php _e( 'Store Id', 'mc-eps' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="redirect_url" id="redirect_url" class="regular-text" value="<?php echo  esc_attr($values['redirect_url']); ?>">
                    </td>
                </tr>

                 <tr >
                    <th scope="row">
                        <label for="mode"><?php _e( 'Mode', 'mc-eps' ); ?></label>
                    </th>
                    <td>
                    <?php  if(esc_attr($values['mode']) == 'sandbox') { ?>

                         <input type="radio" name="mode" value="sandbox" checked > Sandbox&nbsp;&nbsp;
                         <input type="radio" name="mode" value="production" > Production

                     <?php  } else {?>

                        <input type="radio" name="mode" value="sandbox" > Sandbox&nbsp;&nbsp;
                         <input type="radio" name="mode" value="production" checked> Production
                      
                     <?php }  ?>


                    </td>
                </tr>
            </tbody>
        </table>

        <?php wp_nonce_field( 's_mc_eps_setting' ); ?>
        <?php 
        $other_attributes = array('style' =>"display : inline-block; margin-left :13% ");
        submit_button( __( 'Save', 'mc-eps' ), 'primary', 'submit_mc_eps_setting', true, $other_attributes ); ?>
    </form>
</div>
