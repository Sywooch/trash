
var DataTableWrapper = function(table, formFilters, config) {
	this.$table = $(table);
	this.$formFilters = $(formFilters);
	this.$notify = null;
	this.editor = null;
	this.dataTable = null;
	this.currentNode = null;

	this.dtDom = 'CT<"clear">lfrtip';
	this.url = null;
	this.urlEdit = null;
	this.isInlineEdit = true;
	this.rowsData = undefined;
	this.actions = [];
	this.fields = {};
	this.columns = [];
	this.order = 0;
	this.orderDirection = 'desc';
	this.values = {};
	this.trackingChanges = null;

	var wrapper = this;

	this.$table.data('DataTableWrapper', this);

	// Установка настроек
	this.setParams(config);

	// Создание объекта dataTable.Editor
	this.editor = new $.fn.dataTable.Editor({
		ajax: this.urlEdit,
		table: this.$table,
		idSrc: 'id',
		fields: this.getEditorFields(),
		formOptions: {
			bubble: {
				buttons: {
					label: 'Сохранить',
					fn: function () { this.submit(); }
				}
			},
			inline: {
				buttons: {
					label: '&gt;',
					fn: function () { this.submit(); }
				}
			}
		}
	});

	// Создание объекта dataTable
	this.dataTable = this.$table.DataTable({
		dom: this.dtDom,
		processing: true,
		displayLength: 50,
		data: this.rowsData,
		columns: this.getTableColumns(),
		order: [
			typeof this.order === 'number' ? this.order : this.columns.indexOf(this.order),
			this.orderDirection
		],
		language: {
			processing: 'Загрузка данных ...',
			search: 'Поиск:',
			lengthMenu: 'Показывать по _MENU_ записей',
			info: 'Показано от _START_ до _END_, всего _TOTAL_',
			infoEmpty: 'Ничего не найдено',
			infoFiltered: '(отфильтрованно из _MAX_ записей)',
			emptyTable: 'Ничего не найдено',
			paginate: {
				first: 'Первая',
				previous: 'Предыдущая',
				next: 'Следующая',
				last: 'Последняя'
			}
		},
		colVis: {
			buttonText: 'Показать / скрыть столбцы'
		},
		tableTools: {
			sSwfPath: '/js/datatables-tabletools/swf/copy_csv_xls_pdf.swf',
			aButtons: [
				{
					"sExtends": "csv",
					"sButtonText": "Экспорт в Excel"
				},
				{
					"sExtends": "print",
					"sButtonText": "Распечатать"
				}
			]
		}
	});

	// Обновление данных с учетом фильтров
	this.dataTable.on('preXhr.td', function(e, settings, data) {
		$.each(wrapper.$formFilters.serializeArray(), function (i, field) {
			data[field.name] = field.value;
		});
		delete(data['lastId']);

		$('tbody', wrapper.$table).hide();
	});

	this.dataTable.on('xhr.td', function(e, settings, json) {
		$('tbody', wrapper.$table).show();

		// Отслеживание новых записей
		if (wrapper.trackingChanges) {
			$('input[name=lastId]', wrapper.$formFilters).val(json.lastId);
			$('.notifyjs-corner .notifyjs-wrapper').remove();
		}
	});

	// Обработка ответа от сервера
	this.editor.on('submitComplete', function(e, json, data) {
		if (!json.success && json.errorMsg) {
			alert(json.errorMsg);
		}
	});
};

DataTableWrapper.prototype.getTableColumns = function() {
	var wrapper = this;
	var columns = [];

	$.each(this.columns, function(index, name) {
		var params = wrapper.fields[name];
		if (params) {
			var columnData = null;
			if (!params.emptyData) {
				columnData = { '_': name };
				if (params.display) {
					columnData['display'] = params.display;
					columnData['filter'] = params.display;
				} else {
					columnData['display'] = name;
				}
				if (params.sort) {
					columnData['sort'] = params.sort;
				}
			}
			var column = {
				name: name,
				orderable: params.orderable,
				defaultContent: params.defaultContent,
				render: params.render,
				data: columnData
			};

			if (wrapper.isInlineEdit && params.editable) column.class = 'editable';

			columns.push(column);
		}
	});

	return columns;
};

DataTableWrapper.prototype.getEditorFields = function() {
	var wrapper = this;
	var fields = [];

	$.each(this.fields, function(name, params) {
		if (params.type) {
			var field = {
				name: name,
				type: params.type,
				label: params.label,
				ipOpts: params.ipOpts,
				className: params.className,
				params: params
			};

			if (params.values) field.ipOpts = wrapper.values[params.values];

			fields.push(field);
		}
	});

	return fields;
};

DataTableWrapper.prototype.getColumnNameByCell = function(cell) {
	return this.columns[this.dataTable.cell(cell).index().column];
};

DataTableWrapper.prototype.setParams = function(params) {
	if (params) {
		if (params.url !== undefined) this.url = params.url;
		if (params.urlEdit !== undefined) this.urlEdit = params.urlEdit;
		if (params.isInlineEdit !== undefined) this.isInlineEdit = params.isInlineEdit;
		if (params.dtDom !== undefined) this.dtDom = params.dtDom;
		if (params.rowsData !== undefined) this.rowsData = params.rowsData;
		if (params.actions !== undefined) this.actions = params.actions;
		if (params.fields !== undefined) this.fields = params.fields;
		if (params.columns !== undefined) this.columns = params.columns;
		if (params.order !== undefined) this.order = params.order;
		if (params.orderDirection !== undefined) this.orderDirection = params.orderDirection;
		if (params.values !== undefined) this.values = params.values;
		if (params.trackingChanges !== undefined) this.trackingChanges = params.trackingChanges;
	}

	return this;
};

DataTableWrapper.prototype.init = function() {
	var wrapper = this;

	if (this.url) {
		this.dataTable.ajax.url(this.url);
		if (this.rowsData === undefined) {
			this.dataTable.ajax.reload();
		}
	}

	if (this.trackingChanges) {
		this.runTrackingChanges(this.trackingChanges);
	}

	// Inline-редактирование записей
	this.$table.on('click', 'td.editable', function() {
		wrapper.inlineEdit(wrapper.getColumnNameByCell(this), this);
	});

	// Обработка действий
	this.$table.on('click', 'button.action', function() {
		wrapper.action($(this).data('actionType'), this.parentNode);
	});

	// Обработка изменений значений фильтров
	this.$formFilters.submit(function() { return false; });

	$('input.dt_filter,select.dt_filter', this.$formFilters).change(function () { wrapper.reload(); });

	$('.dt_filter_link', this.$formFilters).click(function() { wrapper.reload(); });

	this.$table.on('mouseenter mouseleave', 'tbody tr', function() {
		$(this).toggleClass('active');
	});
};

DataTableWrapper.prototype.reload = function() {
	this.dataTable.ajax.reload();
};

DataTableWrapper.prototype.action = function(type, node) {
	var action = this.actions[type];
	var params = {
		title: action.title,
		buttons: []
	};

	params.buttons.push({
		label: 'Отмена',
		fn: function() { this.close(); }
	});
	params.buttons.push({
		label: 'Сохранить',
		fn: function() { this.submit(); }
	});

	if (action.type === 'create')
		this.editor.create(params);
	else if (action.type === 'edit')
		this.editor.edit(node, true, params);
	else
		this.editor.bubble(node, action.fields, params);

	if (this.fields['action_type']) {
		this.editor.set('action_type', type);
	}

	this.currentNode = node;

	this.inlineDatePickers($('.DateTimePicker input'));
	this.inlineDatePickers($('.DatePicker input'), {timepicker:false, format: 'd.m.Y', closeOnDateSelect: true});
};

DataTableWrapper.prototype.inlineDatePickers = function(inputs, custom) {
	$(inputs).each(function() {
		var params = {
			format: 'd.m.Y H:i',
			closeOnDateSelect: false,
			onShow: function(ct, input) {
				var div = input.closest('.DTE_Bubble_Liner');
				if (div.length > 0) {
					var dp = this;
					var offset = div.offset();

					var close = function() {
						$(dp).trigger('close.xdsoft');
						$([document.body, window]).off('mousedown.xdsoft');
					};

					$(this).css({
						left: offset.left - $(this).width() - 10,
						top: offset.top + 2,
						position: 'absolute'
					}).show();

					$([document.body, window]).on('mousedown.xdsoft', close);
					$('.xdsoft_time', dp).click(close);
					return false;
				}
			},
			lang: 'ru'
		};

		if (custom != undefined) {
			for (var each in custom) {
				params[each] = custom[each];
			}
		}
		$(this).datetimepicker(params);
	});
};

DataTableWrapper.prototype.inlineEdit = function(field, node) {
	if (this.fields[field].editBubble) {
		this.editor.bubble(node, [ field ]);
	} else {
		this.editor.inline(node, [ field ], {
			buttons: {
				label: '&gt;',
				fn: function () { this.submit(); }
			}
		});
	}
};

DataTableWrapper.prototype.ajaxError = function(xhr, textStatus, error) {
	if (xhr.status == 403 || xhr.status == 401) {
		window.location.replace(window.location.href);
	}
};

// Проверка на наличие новых заявок
DataTableWrapper.prototype.runTrackingChanges = function(interval) {
	var wrapper = this;

	// Всплывающее окошко в котором показывается, что есть новые заявки
	this.$notify = $(
		'<div><div class="notifyjs-bootstrap-base notifyjs-bootstrap-info"><div class="clearfix">' +
		'<div class="title">Появилась новая заявка</div>' +
		'<div class="buttons"><button class="refresh">Обновить</button></div>' +
		'</div></div></div>'
	);

	$.notify.addStyle('new_request', { html: this.$notify });

	this.trackingTimer = setInterval(function () {
		$.ajax({
			url: wrapper.url,
			data: wrapper.$formFilters.serialize(),
			dataType: 'json'
		})
			.done(function(json) {
				var len = json.data.length;
				if (len > 0) {
					$('.notifyjs-corner .notifyjs-wrapper').remove();

					$.notify('Hello', {
						autoHide: false,
						className: 'info',
						style: 'new_request'
					});

					$('div.title', wrapper.$notify).html(declension(len, [
						'Появилась ' + len + ' новая заявка',
						'Появилось ' + len + ' новых заявки',
						'Появились ' + len + ' новых заявок'
					]));

					$('button.refresh', wrapper.$notify).on('click', function () { wrapper.reload(); });
				}
			}).
			error(function(xhr, textStatus, error) {
				wrapper.ajaxError(xhr, textStatus, error);
			});
	}, interval);
};


// Тип поля для datatalbe мультиселект
$.fn.dataTable.Editor.fieldTypes.chosen = $.extend(true, {}, $.fn.dataTable.Editor.models.fieldType, {
	create: function(conf) {
		var html = '<div>';
		html += '<select multiple="">';
		$.each(conf.ipOpts, function(id, value) {
			html += '<option value="' + id + '">' + value + '</option>';
		});
		html +=	'</select>';
		html += '</div>';

		conf._enabled = true;
		conf._input = $(html)[0];

		$('select', conf._input).chosen({
			width: '380px',
			max_selected_options: conf.params.max_selected_options
		});

		return conf._input;
	},

	get: function(conf) {
		return $('select', conf._input).val();
	},

	set: function(conf, val) {
		$('select', conf._input).val(val);
		$('select', conf._input).trigger('chosen:updated');
	},

	enable: function(conf) {
		conf._enabled = true;
		$(conf._input).removeClass('disabled');
	},

	disable: function(conf) {
		conf._enabled = false;
		$(conf._input).addClass('disabled');
	}
});

// Тип поля для datatalbe изображение
$.fn.dataTable.Editor.fieldTypes.image = $.extend(true, {}, $.fn.dataTable.Editor.models.fieldType, {
	create: function(conf) {
		var html = '<div class="DTE_Field_Input"><img src=""/></div>';

		conf._enabled = true;
		conf._input = $(html)[0];

		if (conf.params && conf.params.height) {
			$('img', conf._input).attr('height', conf.params.height);
		}

		return conf._input;
	},

	get: function(conf) {
		return $('img', conf._input).attr('src');
	},

	set: function(conf, val) {
		$('img', conf._input).attr('src', val);
	}
});


// Склонение окончаний
function declension(num, expressions) {
	var count = num % 100;
	if (count >= 5 && count <= 20) return expressions['2'];
	count = count % 10;
	return expressions[(count == 1) ? '0' : (count >= 2 && count <= 4 ? '1' : '2')];
}
