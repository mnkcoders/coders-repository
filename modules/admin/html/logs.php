<?php defined ('ABSPATH') or die; ?>
<h1 class="wp-heading-inline"><?php print get_admin_page_title() ?></h1>
<p>Logs view</p>
<?php var_dump(\CodersRepo::listModules()) ?>