<div class="wrap">
        <div id="icon-themes" class="icon32"><br /></div>
        <h2><?php _e('Theme Options', 'framework') ?></h2>
        <?php
            if ( isset($_REQUEST['saved']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('settings saved','framework').'.</strong></p></div>';
            if ( isset($_REQUEST['reset']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('settings reset','framework').'.</strong></p></div>';
            if ( isset($_REQUEST['resetFactoryDefaultsConfirm']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('settings have been reset to factory settings','framework').'.</strong></p></div>';
        ?>
        <div id="doc-block">
                <p><?php printf(__('For help, please read our %1$stheme documentation%2$s. In case you still experience problems please raise a %3$sticket%4$s.','framework'), '<a href="http://www.wppricecomparison.com/documentation/compare/">', '</a>', '<a href="http://www.wppricecomparison.com/support/">', '</a>'); ?></p>
        </div>

        <form method="post" id="theme-options-form" action="<?php echo $_SERVER['SCRIPT_NAME'] . "?page=theme-options.php"; ?>" enctype="multipart/form-data" style="overflow:hidden;" >
        <input type="hidden" name="selectedtab" id="selectedtab" value="0"/>	

        <div id="tabs" class="metabox-holder clearfix">
            
            <ul id="tab-items">
                <li><a href="#tabs-1"><?php _e('General Settings', 'framework') ?></a></li>
                <li><a href="#tabs-2"><?php _e('Price Comparison', 'framework') ?></a></li>
                <li><a href="#tabs-3"><?php _e('Color settings', 'framework') ?></a></li>
                <li><a href="#tabs-4"><?php _e('Miscellaneous', 'framework') ?></a></li>
                <li><a href="#tabs-5"><?php _e('Social', 'framework') ?></a></li>
            </ul>
            <div class="clear"></div>            

            <div class="postbox-container">

            <?php 
            $tab = 1;
            foreach ($options as $value) { 
                switch ( $value['type'] ) {

                    case "opentab": ?>
                        <div id="tabs-<?php echo $tab;?>">
                        <?php 
                        $tab++;
                        break;

                    case "closetab": ?>
                        </div><!-- #tabs- -->
                        <?php
                        break;

                    case "open": // style the opening
                        ?>
                        <!-- div id="tabs-<?php echo $tab;?>" -->
                        <div class="postbox">
                        <?php 
                        break;

                    case "note": // style the notes
                        ?>
                        <?php if(isset($value['desc'])): ?>
                        <div class="notes"><p><?php echo $value['desc']; ?></p></div>
                        <?php endif; ?>
                        <?php 
                        break;

                    case "title": // style the titles
                        ?>
                        <h3 class="hndle"><span><?php echo $value['name']; ?></span></h3>
                        <div class="inside">
                        <?php break;

                    case "hidden": ?>
                        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="hidden" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(htmlspecialchars(get_option( $value['id'] ), ENT_QUOTES)); } else { echo $value['std']; } ?>" />
                        <?php break;

                    case "text": // style the text boxes
                        ?>
                        <div class="textcont <?php echo (isset($value['class'])) ? $value['class'] : ""?>">
                                <div class="value">
                                        <?php echo $value['name']; ?>:
                                </div>
                                <div class="input">
                                        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(htmlspecialchars(get_option( $value['id'] ), ENT_QUOTES)); } else { echo $value['std']; } ?>" />
                                        <?php if(isset($value['desc'])): ?>
                                        <p><?php echo stripslashes($value['desc']); ?></p>
                                        <?php endif; ?>
                                </div>
                                <div class="clear"></div>
                        </div>


                    <?php break;

                    case "file": // style the upload boxes
                        $option_value = get_option($value['id']);
                        ?>
                        <div class="textcont">
                                <div class="value">
                                        <?php echo $value['name']; ?>:
                                </div>
                                <div class="input">
                                        <table class="form-table">
                                                <tr valign="top">
                                                        <th scope="row">File:</th>
                                                        <td><input type="file" name="<?php echo $value['id']; ?>" class="tz-upload" size="40" border="0" /></td>
                                                </tr>
                                        </table>
                                        
                                        <?php if( isset($value['subtype']) && $value['subtype'] == "image" && $option_value != ""):?>
                                        <p style="width: 200px;" id="ImageContainerr">
                                            <img style="max-width: 100%;" id="image_<?php echo $value['id']; ?>" src="<?php echo esc_url($option_value); ?>" />                                            
                                        </p>
                                        <?php else: ?>
                                            <p><?php _e('Current file:', 'framework') ?> <?php echo $option_value; ?></p>
                                        <?php endif; ?>
                                </div>
                                <div class="clear"></div>
                        </div>
                    <?php break;

                    case "textarea": // style the textareas
                        ?>

                        <div class="textcont">
                        <div class="value"><?php echo $value['name']; ?>:</div>
                        <div class="input">
                        <textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>"><?php if ( get_option( $value['id'] ) != "") { echo stripslashes(htmlspecialchars(get_option( $value['id'] ), ENT_QUOTES)); } else { echo $value['std']; } ?></textarea>
                        <?php if(isset($value['desc'])): ?>
                        <p><?php echo stripslashes(htmlspecialchars($value['desc'])); ?></p>
                        <?php endif; ?>
                        </div>
                        <div class="clear"></div>
                        </div>


                        <?php break;

                    case "checkbox": // style the checkboxes
                        ?>

                        <div class="textcont">
                        <div class="value check-value"><?php echo $value['name']; ?>:</div>
                        <div class="input">
                        <?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
                        <p><input class="theme-options-checkbox" type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
                        <?php if(isset($value['desc'])): ?>
                        <?php echo stripslashes(htmlspecialchars($value['desc'])); ?></p>
                        <?php endif; ?>
                        </div>
                        <div class="clear"></div>
                        </div>


                        <?php break;

                    case "select": // style the select
                        ?>

                        <div class="textcont">
                        <div class="value"><?php echo $value['name']; ?>:</div>
                        <div class="input">
                        <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php foreach ($value['options'] as $key => $val) { ?><option value="<?php echo $key; ?>"<?php if ( get_option( $value['id'] ) == $key) { echo ' selected="selected"'; } ?>><?php echo $val; ?></option><?php } ?></select>
                        <?php if(isset($value['desc'])): ?>
                        <p><?php echo stripslashes($value['desc']); ?></p>
                        <?php endif; ?>
                        </div>
                        <div class="clear"></div>
                        </div>


                        <?php break;

                    case "close": // style the closing
                        ?>

                                </div><!-- inside -->
                        </div><!-- post box -->

                        <p class="submit">
                                        <input name="save" type="submit" class="button"  value="<?php _e('Save Settings','framework'); ?>" />    
                                        <input type="hidden" name="action" value="save" />
                        </p>

                        <?php break;
                } 
            }
            ?>
           
        </div><!-- postbox container -->
    </div><!-- metabox holder -->
    </form>   
        
    <form method="post" action="">
        <p class="submit">
            <input name="clearOrphanedProductDescriptions" type="submit" class="button" value="<?php _e('Clear Old Product Descriptions','framework'); ?>" />
            <input type="hidden" name="action" value="clearOrphanedProductDescriptions" />
            <span><?php _e('Caution: this will remove all old orphaned product descriptions.', 'framework') ?></span>
        </p>
    </form>

    <form method="post" action="">
        <p class="submit">
            <input name="reset" type="submit" class="button" value="<?php _e('Reset','framework'); ?>" />
            <input type="hidden" name="action" value="reset" />
            <span><?php _e('Caution: will restore theme defaults. It is highly recommended that you backup your database first.', 'framework') ?></span>
        </p>
    </form>

    <form method="post" action="">
        <p class="submit">
            <input name="resetFactoryDefaults" type="submit" class="button" value="<?php _e('Reset To Factory Defaults','framework'); ?>" />
            <input type="hidden" name="action" value="resetFactoryDefaults" />
            <span><?php _e('Caution: This will restore the theme to factory defaults (It will also restore the Compare+ plugin to factory defaults if it is installed and activated), it is highly recommended that you backup your database first.', 'framework') ?></span>
        </p>
    </form>
        
</div>
<script type="text/javascript">
if(jQuery('#tz_hide_related_products').is(':checked')){ 
        jQuery('.number_related_products').css("display", "none");
}


jQuery(document).ready ( function() {
    
        jQuery('textarea').keydown(function(e) {			
            if (e.keyCode == 13 || e.which == 13) {
                  e.preventDefault();
                  var s = jQuery(this).val();
                  jQuery(this).val(s+"\n");
                }
        });
});
</script>