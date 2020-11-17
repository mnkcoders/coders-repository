<?php defined ('ABSPATH') or die; ?>
<div id="profile" class="wrap">
    <!-- fill in by javascript -->
    <!--<?php  
    
    print_r($this->list_data);
    
    ?>-->
    <p>
        <?php print $this->label_name ?>
        <?php print $this->input_name ?>
    </p>
    <p>
        <?php print $this->label_email ?>
        <?php print $this->input_email ?>
    </p>
    <p>
        <?php print $this->input_active ?>
    </p>
</div>