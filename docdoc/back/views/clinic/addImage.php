
<div id="UploadPhotos">

	<div class="row uploadActions">
		<div class="form addPhoto" style="width:100px; display: inline; float: left;">Добавить</div>
		<div class="form startUpload" style="width:100px; margin-left: 10px; float: right;">Загрузить всё</div>
		<div class="form cancelUpload" style="width:100px; float: right;">Отменить всё</div>
		<div class="clear"></div>
	</div>

	<div class="table table-striped files" id="previews">
		<p class="note">Перенесите сюда фотографии, которые хотите добавить, или нажмите на кнопку добавить</p>
	</div>

	<div class="file-row template">
		<div class="col" style="width: 80px;">
			<span class="preview"><img data-dz-thumbnail /></span>
		</div>
		<div class="col" style="width: 170px;">
			<p class="name" data-dz-name></p>
			<p class="size" data-dz-size></p>
			<strong class="error text-danger" data-dz-errormessage></strong>
		</div>
		<div class="col" style="width: 150px;">
			<button class="btn btn-primary start">
				<i class="glyphicon glyphicon-upload"></i>
				<span>Загрузить</span>
			</button>
			<button data-dz-remove class="btn btn-warning cancel">
				<i class="glyphicon glyphicon-ban-circle"></i>
				<span>Отмена</span>
			</button>
		</div>
	</div>

</div>
