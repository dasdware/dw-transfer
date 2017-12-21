<?php

/**
 * Admin GUI displaying export and import metaboxes.
 *
 * @link       http://www.dasd.co.uk
 * @since      1.0.0
 *
 * @package    DW_WP_Transfer
 * @subpackage DW_WP_Transfer/admin/partials
 */
?>

<div class="wrap">
    <h1><?=__('DW Transfer')?></h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="postbox-container-2" class="postbox-container">

                <div class="postbox">
                    <h2><?=__('Export')?></h2>
                    <div class="inside">    
                        <form method="post">
                            <?php wp_nonce_field('dw-transfer-export'); ?>
                            <div class="form-heading">
                                <label for="transfer_type"><?=__('Transfer type')?></label>
                            </div>
                            <div class="form-field">
                               <select name="transfer_type">
<?php                          foreach (DW_Transfer_Descriptors::get_instance()->descriptors as $descr) { ?>
                                   <option value="<?=$descr->name?>"><?=$descr->caption?></option>
<?php                          } ?>
                               </select>
                            </div>
                            <input name="dw-transfer-export" class="button button-primary button-large" value="<?=__('Start export')?>" type="submit"/>
                        </form>
                    </div>
                </div>

                <div class="postbox">
                    <h2><?=__('Import')?></h2>
                    <div class="inside">    
                        <form method="post" enctype='multipart/form-data'>
                            <?php wp_nonce_field('dw-transfer-import'); ?>
                            <div class="form-heading">
                                <label for="transfer_type"><?=__('Transfer type')?></label>
                            </div>
                            <div class="form-field">
                               <select name="transfer_type">
<?php                          foreach (DW_Transfer_Descriptors::get_instance()->descriptors as $descr) { ?>
                                 <option value="<?=$descr->name?>"><?=$descr->caption?></option>
<?php                          } ?>
                               </select>
                            </div>
                            <div class="form-heading">
                                <label for="transfer_file"><?=__('File upload')?></label>
                            </div>
                            <div class="form-field">
                                <input type='file' name='transfer_file'/>
                            </div>
                            <input name="dw-transfer-import" class="button button-primary button-large" value="<?=__('Start import')?>" type="submit"/>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
