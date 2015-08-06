<div class="js-popup popup" data-popup-id="js-popup-speclist">
    <h2 class="popup_title ui-border_b">Выберите исследование</h2>
    <ul class="spec_list columns_3">
        <?php $perColumn = count($diagnostics)/3; $i = 0; $diagnosticsArray = array();?>
        <?php foreach ($diagnostics as $diagnostic):?>
            <?php if ($diagnostic->parent_id == 0):?>
            <?php echo $i === 0 ? '<li class="column">' : '';?>
                <ul class="column_group">
                    <li class="spec_list_head spec_list_item js-specselect" data-spec-id="<?php echo $diagnostic->id;?>" data-related-form="search_form"><a class="spec_list_link" href=""><?php echo $diagnostic->name?></a></li>
                <?php $i++; $diagnosticsArray[] = array('id' => $diagnostic->id, 'name' => $diagnostic->name);?>
                <?php foreach ($diagnostics as $diagnosticChild): ?>
                    <?php if ($diagnosticChild->parent_id == $diagnostic->id): ?>
                    <li class="spec_list_item js-specselect" data-spec-id="<?php echo $diagnosticChild->id; ?>" data-related-form="search_form"><a class="spec_list_link" href=""><?php echo $diagnosticChild->name ?></a></li>       
                    <?php $i++; $diagnosticsArray[] = array('id' => $diagnosticChild->id, 'name' => $diagnostic->name .' '.$diagnosticChild->name); ?>
                    <?php endif;?>
                <?php endforeach;?>
                </ul>
            <?php if( $i+($perColumn*0.3) >= $perColumn ) { echo '</li>'; $i = 0; }; //($perColumn*0.3) - для выравнивания списков ?> 
            <?php endif;?>
        <?php endforeach;?>
    </ul>
 </div>
 <!-- For autocomplete -->
 <div class="xml-data-speclist s-hidden"><?php echo CJSON::encode($diagnosticsArray);?></div>