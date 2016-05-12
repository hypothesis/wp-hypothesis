<div class="wrap">
<form method="post" action="options.php">
      <?php echo settings_fields( "my_option_group" ); ?>
      <?php echo do_settings_sections( "hypothesis-setting-admin" ); ?>
      <?php echo submit_button(); ?>
</form>
</div>
