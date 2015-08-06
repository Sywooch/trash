<div id="dia-specs">
	<script>
		$(document).ready(function(){
			$(".met-dia span").click(function(){
				$(".met-dia span").not(this).addClass("plus").next().css("display", "none");
				$(".met-dia span").find("i").html("[+]");
				$(this).prev().attr("checked","checked");
				if ($(this).hasClass("plus"))
					{
						$(this).removeClass("plus").next().toggle();
						$(this).find("i").html("[&ndash;]");	
					}
				else 
					{
						$(this).addClass("plus").next().toggle();
						$(this).find("i").html("[+]")
					}
			});


			$(".met-dia .parent").click(function(){
				$(".met-dia span").not(this).addClass("plus").next().css("display", "none");
				$(".met-dia span").find("i").html("[+]");
				$(this).attr("checked","checked");
				if ($(this).next().hasClass("plus"))
					{
						$(this).next().removeClass("plus").next().toggle();
						$(this).next().find("i").html("[&ndash;]");	
					}
				else 
					{
						$(this).next().addClass("plus").next().toggle();
						$(this).next().find("i").html("[+]");
					}
			});
/*
			$(".met-dia input[name='met-dia-choise1']").click(function(){
				
			});
*/
			$(".met-dia li").click(function(){
				$(this).find("input").attr("checked","checked");
			});
		});
	</script>
	<div class="head">Выберите метод диагностики</div>
	<div class="spec-wrap">
	<?php 
		$plusShow = array();
		foreach($diagnostics as $diagnostic){
			$plusShow[$diagnostic->id] = 0;
			if($diagnostic->parent_id == 0){
				foreach($diagnostics as $diagnosticChild){
					if($diagnosticChild->parent_id == $diagnostic->id)
						$plusShow[$diagnostic->id]++;
				}
			}
		}
	?>
	<?php 
		foreach($diagnostics as $diagnostic){
			if($diagnostic->parent_id == 0){ ?>
				<div class="met-dia">
                <?php if($diagnostic->id == $selDiag) { ?>
                	<input type="radio" class="parent" data-diag="<?php echo $diagnostic->name; ?>" value="<?php echo $diagnostic->id; ?>" name="met-dia-choise" checked="checked" />
                    <span class="plus"><?php echo $diagnostic->name; ?> 
                    <?php  if($plusShow[$diagnostic->id] > 0) {?>
                    <i>[-]</i>
                    <?php  } ?>
                    </span>
                <?php } else { ?>
                	<input type="radio" class="parent" data-diag="<?php echo $diagnostic->name; ?>" value="<?php echo $diagnostic->id; ?>" name="met-dia-choise" />
                    <span class="plus"><?php echo $diagnostic->name; ?> 
                    <?php  if($plusShow[$diagnostic->id] > 0) {?>
                    <i>[+]</i>
                    <?php  } ?>
                    </span>
                <?php } ?>
                
                <?php if($diagnostic->id == $selDiagParent) { ?>
				<ul style="display:block;">	
                <?php } else { ?>
                <ul>
                <?php } ?>
                
				<?php foreach($diagnostics as $diagnosticChild){
					if($diagnosticChild->parent_id == $diagnostic->id){ ?>
                    	<li>
						<?php if($diagnosticChild->id == $selDiag) { ?>
						<input type="radio" data-diag="<?php echo $diagnostic->reduction_name . ' ' . $diagnosticChild->name; ?>" value="<?php echo $diagnosticChild->id; ?>" name="met-dia-choise" checked="checked" />
                        <?php } else { ?>
                        <input type="radio" data-diag="<?php echo $diagnostic->reduction_name . ' ' . $diagnosticChild->name; ?>" value="<?php echo $diagnosticChild->id; ?>" name="met-dia-choise" />
                        <?php } ?>
                        <?php echo $diagnosticChild->name; ?></li>
					<?php } ?>
				<?php } ?>
				</ul>
				</div>
			<?php } ?>
		<?php } ?>
		<br>
		<div class="met-dia">
        	<?php if($selDiag == 0) {?>
			<input class="parent" type="radio" name="met-dia-choise" value="0" data-diag="все диагностики" checked="checked">
            <?php } else { ?>
            <input class="parent" type="radio" name="met-dia-choise" value="0" data-diag="все диагностики">
            <?php } ?>
			<span class="plus">Все диагностики </span>
		</div>
	
	</div>
	<div id="dia-spec-submit" class="button but-specs but-dia-spec">
		<span>Применить</span>	
	</div>
</div>