function VkPhotoImport(button) {
	this.limit = 20;
	this.offset = 0;
	this.userId = null;
	this.innerAlbums = [
		{
			aid: 'wall',
			title: 'Стена'
		}
	];
	this.albums = {};
	this.selectedPhotos = [];

	var $this = this;
	$(button).on('click', function () {
		$this.getLoginStatus();
	});
}

/**
 * Обновляет информацию о текущем пользователе
 */
VkPhotoImport.prototype.getLoginStatus = function () {
	var $this = this;

	VK.Auth.getLoginStatus(function (response) {
		if (response.session) {
			$this.userId = response.session.mid;
			$this.getAlbums();
		} else {
			$this.login();
		}
	});
};

/**
 * Авторизует текущего пользователя
 */
VkPhotoImport.prototype.login = function () {
	var $this = this;
	VK.Auth.login(function (response) {
		if (response.session) {
			$this.userId = response.session.mid;
			$this.getAlbums();
		} else {
			/* Пользователь нажал кнопку Отмена в окне авторизации */
		}
	}, VK.access.PHOTOS);
};


/**
 * Получает список альбомов пользователя
 * @returns {boolean}
 */
VkPhotoImport.prototype.getAlbums = function () {
	if (this.userId == null)
		return false;
	var $this = this;
	VK.Api.call('photos.getAlbums', {owner_id: this.userId}, function (result) {
		if (typeof(result.error) == 'undefined') {
			$this.albums = result.response;

			for (var n in $this.innerAlbums) {
				$this.albums.unshift($this.innerAlbums[n]);
			}

			$this.renderAlbumsList();
		} else if (result.error && result.error.error_msg == 'Access denied') {
			VK.Auth.logout();
			$this.login();
		}
	});

	return true;
};

VkPhotoImport.prototype.renderAlbumsList = function () {

	$("#overlay").show();
	$("#popup").html($('#vkAlbumsList').tmpl({
		albums: this.albums
	}));
	showPopup('vk-albums-modal', true);
};

/**
 * Получает список фотографий в альбоме
 */
VkPhotoImport.prototype.loadPhotos = function (link) {
	var $this = this;
	var photos = [];
	var albumId = $(link).data('id');

	VK.Api.call('photos.get', {owner_id: this.userId, album_id: albumId}, function (result) {
		$this.renderPhotosList(result.response);
	});
	return false;
};

/**
 * Отрисовывает фотографии в модальном окне
 * @param data
 */
VkPhotoImport.prototype.renderPhotosList = function (data) {
	this.selectedPhotos = [];
	$("#overlay").show();
	$("#popup").html($('#vkPhotosList').tmpl({
		photos: data
	}));
	showPopup('vk-photos-modal', true);
};

/**
 * Выбор фотографии при клике
 * @param elem
 */
VkPhotoImport.prototype.selectPhoto = function (elem) {
	if (!$(elem).hasClass('selected')) {
		this.selectedPhotos.push($(elem).data('id'));
	} else {
		for (var i = 0; i <= this.selectedPhotos.length; i++) {
			if ($(elem).data('id') == this.selectedPhotos[i]) {
				Array.remove(this.selectedPhotos, i);
			}
		}
	}

	$(elem).toggleClass('selected', !$(elem).hasClass('selected'));

	$(elem).closest('.popup-app_cont').find('.button').toggleClass('button-load-photo_vk__no', this.selectedPhotos.length == 0);
};

/**
 * Сохранение выбранных фотографий
 * @returns {boolean}
 */
VkPhotoImport.prototype.savePhotos = function () {
	if (this.selectedPhotos.length == 0)
		return false;

	var ids = [];
	for (var i = 0; i <= this.selectedPhotos.length; i++) {
		ids.push(this.userId + '_' + this.selectedPhotos[i]);
	}

	var $this = this;
	VK.Api.call('photos.getById', {photos: ids.join(',')}, function (result) {
		$this.renderSavedPhotos(result.response);
	});

	return true;
};

/**
 * Отрисовка выбранных фотографий
 * @param photos
 */
var currentVkNumber = 100;
VkPhotoImport.prototype.renderSavedPhotos = function (photos) {
	$('#isRemoteUpload').val(1);

	$('#popup, #overlay').hide();
	for (i = 0; i < photos.length; i++) {
        $.get(homeUrl + 'lk/uploadWork', {remote:1, qqfile:photos[i]['src_big']}, function(data) {
            currentVkNumber++;
            $('.works-uploaded').append($('#workTmpl').tmpl({
                id: currentVkNumber,
                imagePath: data.path,
                imageName: data.name
            }));
            $(".prof-btn_next").show();
        }, 'json');
	}
};