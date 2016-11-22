
function GetFilterName(filter) {
	var name = filter.attr( 'name' );
	if (name.substr( name.length - 2, 2 ) == '[]') {
		name = name.substr( 0, name.length - 2 );
	}
	return name;
}

function CollectData( wrapper, ignore_search_id ) {
	var data = {};
	wrapper.find('select').each( function(){
		var select = jQuery( this );
		var name = select.attr( 'name' );
		if( ( name != 'orderby' || select.val() != null ) && select.attr( 'disabled' ) != 'disabled' ){				
			if( select.val() != '' ){
				data[ name ] = select.val() ;
			}
		}
	});
	
	wrapper.find('input').each( function() {
		var input = jQuery( this );
		var name = input.attr( 'name' );
		var is_array = name.substr( name.length - 2, 2 ) == '[]'; 
		
		if( typeof( name ) == 'undefined') {
			return;
		}
		if ( input.attr( 'disabled' ) == true ) {
			return;
		}
		if( input.hasClass( 'sf-date' ) || input.attr( 'type' ) == 'hidden' || !is_array ){
			if( input.val() == '' ) {
				return;
			}
			if( input.attr( 'type' ) == 'radio' && input.prop( 'checked' )  != true) {
				return;
			}
			if( !is_array ){
				if (ignore_search_id && name == 'search-id') {
					return;
				}
				data[ name ] = input.val() ;
			} else {
				var data_name = name.substr( 0, name.length - 2 )
				if( typeof( data[ data_name ] ) == 'undefined' ) {
					data[ data_name ] = [];
				}
				data[ data_name ].push( input.val() );
			}
		} else{
			var n = name.substr( 0, name.length - 2 );
		
			if( input.prop( 'checked' ) ){
				if( typeof data[n] == 'undefined' ) {
					data[n] = [];					
				}
				data[n].push( input.val() );
			}
		}
	});
	return data;
}

function SetUrlHash(key, value, create_history_entry) {
	SetUrlParameter(key, value, create_history_entry, '#');
}

function SetUrlSearch(key, value, create_history_entry) {
	SetUrlParameter(key, value, create_history_entry, '?');
}

function SetUrlParameter(key, value, create_history_entry, type) {
	if (typeof( key ) == 'undefined') {
		console.error("SetUrlParameter: key is undefined");
		return;
	}
	if (typeof( value ) == 'undefined') {
		value = '';
	}
	key = encodeURIComponent(key);
	value = encodeURIComponent(value);
	var parameters = [];
	var hash = type == '#' ? location.hash.substr(1) : location.search.substr(1);
	if (hash.length > 0) {
		parameters = hash.split('&');
	}
	
	var i = 0;
	for (; i <parameters.length; i++) {
		var split = parameters[i].split('=');
		if (split[0] == key) {
			if (value.length > 0) {
				split[1] = value;
				parameters[i] = split.join('=');
			} else {
				parameters.splice(i, 1);
			}
			break;
		}
	}
	
	if (i == parameters.length && value.length > 0) {
		parameters[i] = [key,value].join('=');
	}
	var address = type;
	if (parameters.length > 0) {
		address += parameters.join('&');
	}
	if (create_history_entry) {
		window.history.pushState('forward', null, address);
	} else {
		history.replaceState(undefined, undefined, address);
	}
}

function GetSearchUrlParameter(key) {
	return GetUrlParameter(key, location.search);
}

function GetHashUrlParameter(key) {
	return GetUrlParameter(key, location.hash);
}

function GetUrlParameter(key, url) {
	var result = '';
	if (url.length > 1) {
		var parameters = url.substr(1).split('&');
		for (var i in parameters) {
			var split = parameters[i].split('=');
			if (split[0] == key) {
				if (split.length > 1) {
					result = split[1];
				}
				break;
			}
		}
	}
	return result;
}

// Текущая страница отфильтрованных событий
var current_page = 1;
// Количество доступных страниц результатов
var pages_count = -1;
// Статус открыт ли всплывающий диалог
var isPopupOpen = false;
// Счетчик запросов для использования в качестве айдишника и определения ответа
var current_request_id = 1;
// Айди запроса, с которого начался новый сеанс(новый фильтр), все предыдущие запросы нужно игнорировать
var min_acceptable_request_id = 1;
// Статус выполняется ли запрос на сервер
var isQueryInProgress = false;

var lastDate = -1;

function GetFilterResults( is_append ) {
	var wrapper = jQuery( '.sf-wrapper' );
	var filters_data = CollectData( wrapper, false );
	var events_list_container = jQuery('.events-list-table');
	if (is_append) {
		if (pages_count == -1) {
			pages_count = events_list_container.attr('pages-count');
		}
		if (current_page == pages_count) {
			return;
		}
		if (lastDate == -1) {
			lastDate = events_list_container.attr('last-date');
		}
		current_page++;
	} else {
		current_page = 1;
		lastDate = -1;
		min_acceptable_request_id = current_request_id;
	}
 	isQueryInProgress = true;
	
	var loading_indicator = jQuery('#list_loader');
	loading_indicator.show();
	var request_data = {
		action		:	'sf-search',
		data		:	filters_data,
		request_id	: 	current_request_id,
		page		: 	current_page,
		last_date	:	lastDate
	};

	var request_id = current_request_id;
	current_request_id++;
	
	if (!is_append) {
		events_list_container.html('');
	}
	
	jQuery.post(sf_ajax_root, request_data).
		done( function (response_data) {
			loading_indicator.hide();
			var response = JSON.parse(response_data);
			
			console.debug("response_id: " + response.request_id + ", local_id=" + request_id);
			if ( response.request_id < min_acceptable_request_id) {
				console.debug("response is outdated");
				isQueryInProgress = false;
				return;
			}
			if( response.request_id != request_id) {
				console.error("invalid response received");
				isQueryInProgress = false;
				return;
			}
			lastDate = response.last_date;
			if(is_append) {
				events_list_container.append(response.html);
			} else {
				pages_count = response.pages_count;
				events_list_container.html(response.html);
			}
			isQueryInProgress = false;
		}).fail( function() {
			loading_indicator.hide();
			if(is_append) {
				current_page--;
				ShowConnectionError();
			}
			ShowConnectionError();
			isQueryInProgress = false;
		});
}

function OpenPopup(post_id, shouldPushState) {
	if (isPopupOpen) {
		return;
	}
	isPopupOpen = true;
	//var url = jQuery( this ).attr( 'href' );
	var loading_indicator = jQuery('#box_loader');
	loading_indicator.show();
	SetPageScrollEnabled(false);
	if (shouldPushState) {
		SetUrlHash('show', post_id, true);
		//window.history.pushState('forward', null, '#show=' + post_id);
		 //document.location.search = 'show=' + post_id;
	}
	// Получить данные страницы через ajax и создать из них по шаблону страницу
	var settings = {
		action	:	'get-post-page',
		post_id	: post_id	
	};
	jQuery.post(sf_ajax_root,settings)
		.done(function( response ) {
			loading_indicator.hide();
			if (isPopupOpen) {
				var popup = '<div class="popup-dialog-wrapper"><div class="popup-dialog">' + response + '</div><div class="popup-wrapper-close-button"/></div>';
				jQuery('body').append(jQuery(popup));
				jQuery('.popup-dialog-wrapper').click(function(e) {
					event.preventDefault();
					SetUrlHash('show', '', true);
					ClosePopup();
				});

				jQuery('.popup-dialog').click(function(e) {
					e.stopPropagation();
				});
			}
		}).fail(function() {
			loading_indicator.hide();
			ShowConnectionError();
		});
}

function ShowConnectionError(text) {
	ShowMessageBox('Не удалось подключиться к серверу, проверьте соединение с интернетом или обновите страницу.');
}

function ShowMessageBox(text) {
	
}

function ClosePopup() {
	if (!isPopupOpen) {
		return;
	}
	isPopupOpen = false;
	jQuery( '.popup-dialog-wrapper' ).remove();
	SetPageScrollEnabled(true);
}

function CheckCurrentPageParameters(do_request_data) {
	// Если загрузилась страница с указанием фильтров, применим эти фильтры
	var post_id_string = GetHashUrlParameter('show');
	if( post_id_string.length > 0) {
		var post_id = parseInt(post_id_string, 10);
		OpenPopup(post_id, false);
		//return;
	}
	// Если загрузилась страница с указанием фильтров, применим эти фильтры
	ParseFilters(do_request_data);
}


// Парсит строку адреса и выставляет фильтрам указанные в строке данные
function ParseFilters(do_request_data) {
	//console.debug("apply filters: " + filter_string);
	var was_changes = false;
	jQuery('fieldset .sf-element input').each( function() {
		var fieldset_id = jQuery(this).attr('name');
		fieldset_id = fieldset_id.substr(0, fieldset_id.length-2);
		var fieldset_parameters_string = GetSearchUrlParameter('f' + fieldset_id);
		if (fieldset_parameters_string.length = 0) {
			return;
		}
		var fieldset_parameters = decodeURIComponent(fieldset_parameters_string).split(',');
		var field_id = jQuery(this).attr('value')
		var checked = fieldset_parameters != undefined && fieldset_parameters.includes(field_id);
		//console.debug("fieldset="+fieldset_id + ", field_id="+field_id + ", checked=" + checked);
		if (jQuery(this).prop('checked') != checked) {
			was_changes = true;
		} 
		jQuery(this).prop('checked', checked);
	});
	// После загрузки страницы - не вызываем поиск, начальные данные должны были прийти с сервера
	if (do_request_data && was_changes) {
		GetFilterResults(false);
	}
}

function ParseFiltersBak(filter_string) {
	var range_max = '';
	var range_min = '';
	var	hash = JSON.parse( filter_string );
	for ( property in hash ) {
		jQuery( '.sf-element-hide[data-condkey="'+property+'"]' ).each( function(){
			if( jQuery( this ).attr( 'data-condval' ) == hash[property] ){
				jQuery( this ).show();
				jQuery( this ).addClass( 'sf-element' );
			}
		});
			
		if( jQuery( '.sf-filter *[name="' + property + '"]' ).attr( 'type' ) != 'radio' )
			jQuery( '.sf-filter *[name="' + property + '"]' ).val( hash[property] );
		jQuery( '.sf-filter input[name="' + property + '[]"]' ).each( function(){
			if( jQuery( this ).attr( 'type' ) == 'checkbox' ){
				for( var i = 0; i < hash[property].length; i++ )
					if( jQuery( this ).val() == hash[property][i] )
						jQuery( this ).prop( 'checked', true );
			}
		});
		
		var date_index = 0;
		jQuery( '.sf-filter input.sf-date[name="' + property + '[]"]' ).each( function(){
			jQuery( this ).val( hash[property][ date_index ] );
			date_index++;
		});
		
		jQuery( '.sf-filter input[type="radio"][name="' + property + '"][value="' + hash[property] +'"]' ).prop('checked',true);
		if( jQuery( '.sf-filter *[name="' + property + '"]' ).parent().hasClass( 'sf-range-wrapper' ) ){
			var arrange_slider = true;
			jQuery( '.sf-filter *[name="' + property + '"]' ).parent().find( 'input[type="hidden"]' ).each( function(){
				if( jQuery( this ).val() != hash[ jQuery( this ).attr( 'name') ] )
					arrange_slider = false;
			});
			if( arrange_slider ){
				var parent = jQuery( '.sf-filter *[name="' + property + '"]' ).parent()
				parent.find( 'input[type="hidden"]' ).each( function(){						
					if( jQuery( this ).attr( 'name' ).match(/max/i) )
						range_max = parseInt( jQuery( this ).val() );
					else
						range_min = parseInt( jQuery( this ).val() );
				});
				parent.find( '.sf-range' ).slider( "option", "values", [range_min,range_max] );	
				if( parent.attr( 'data-unitfront' ) == 1 )
					var pricetxt = parent.attr( 'data-unit' ) + range_min + ' - ' + parent.attr( 'data-unit' ) + range_max;
				else
					var pricetxt = range_min + parent.attr( 'data-unit' ) + ' - ' + range_max + parent.attr( 'data-unit' );
				parent.find( '.sf-write' ).text( pricetxt );
			}
		}
	}
}

function HandleFiltersChange() {
	var changed = jQuery(this);
	var possible_cond_key = changed.closest( '.sf-element' ).attr( 'data-id' );
	var possible_cond_val = changed.val();
	if( ( changed.attr('type') == 'checkbox' || changed.attr('type') == 'radio' ) && !changed.prop( 'checked' ) ) {
		possible_cond_val = -2;
	}
	jQuery( '.sf-element-hide' ).each( function() {
		if( changed.attr( 'data-condkey' ) == possible_cond_key ) {
			if( possible_cond_val == changed.attr( 'data-condval' ) ) {
				changed.fadeIn();
				changed.addClass( 'sf-element' );
				changed.find( 'input, select' ).attr( 'disabled', false );
			} else {
				changed.hide();
				changed.removeClass( 'sf-element' );
				changed.find( 'input, select' ).attr( 'disabled', true );
			}
		}
	});
	var wrapper = jQuery( '.sf-wrapper' );
	var filters_data = CollectData( wrapper, true );
	//var parameters_string = JSON.stringify( filters_data );
	/*for(var key in filters_data) { 
		var value = filters_data[key];
		SetUrlSearch(key, value); 
	}*/
	var name = GetFilterName(changed);
	SetUrlSearch('f' + name, filters_data[name]);
	GetFilterResults(false);
}

jQuery( document ).ready( function() {
	var win = jQuery(window);
	// Всплывающий бокс загрузки
	var loader_box = '';
	loader_box += '<div id="box_loader" style="display: none">';
	loader_box += 	'<div class="back">';
	loader_box += 		'<div class="loader_pr">';
	loader_box += 			'<div class="pr_bt"></div>';
	loader_box += 			'<div class="pr_bt"></div>';
	loader_box += 			'<div class="pr_bt"></div>';
	loader_box += 		'</div>';
	loader_box += 	'</div>';
	loader_box += '</div>';
	jQuery('body').append(jQuery(loader_box));
	// Закрытие попапа на Esc
	win.keydown(function(e) {
		if (isPopupOpen && e.keyCode == 27) { // escape key maps to keycode `27`
			event.preventDefault();
			SetUrlHash('show', '', true);
			ClosePopup();
		}
	});
	// Закрытие попапа при нажатии "назад"
	win.on('popstate', function(event) {
		if (isPopupOpen) {
			ClosePopup();
		}
		CheckCurrentPageParameters(true);
	});

	jQuery( document ).on('click', '.events-list-row', function( event ){
		if(event.which == 1) {
			event.preventDefault();
			ClosePopup();
			var post_id = jQuery( this ).attr( 'postid' );
			OpenPopup(post_id, true);
		}				
	});

	var wrapper = jQuery('.sf-wrapper');
	var anchor = jQuery('#events-list-filters-anchor');
	// Активируем логику, только если на странице есть есть наши фильтры
	if (wrapper.length == 0) {
		// Если фильтров нет, то удалим placeholder в боковом меню
		if (anchor.length > 0) {
			//anchor.hide();
		}
		return;
	}
	
	// Переместим фильтры в боковое меню
	wrapper.show();
	if (anchor.length == 0) {
		wrapper.html('Not found anchor with id: #events-list-filters-anchor');
		return;
	}
	wrapper.prependTo(anchor);
	jQuery('#secondary').trigger('resize');

	// Добавим автоподгрузку на скрол
	// Each time the user scrolls
	jQuery(document).scroll(function() {
		// End of the document reached?
		var content_height = jQuery('.site-content').height();
		var container_height = jQuery('body').height();
		var required_scroll = content_height - container_height*3/2;
		//jQuery('.site-description').html('required_scroll='+required_scroll+' current='+jQuery(this).scrollTop());
		if (!isQueryInProgress && jQuery(this).scrollTop() > required_scroll) {
			//$('#loading').show();
			GetFilterResults(true);
		}
	});
	
		
	// Отслеживаем изменения фильтров и вызываем поиск после каждого изменения
	jQuery( document ).on( 'change', '.sf-filter input, .sf-filter select', HandleFiltersChange);

	CheckCurrentPageParameters(false);
});
