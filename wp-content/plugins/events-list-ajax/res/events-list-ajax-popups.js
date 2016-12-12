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

function SetPageScrollEnabled(is_enabled) {
	if (is_enabled) {
		jQuery('body').removeClass('no-scroll');
	} else {
		jQuery('body').addClass('no-scroll');
	}
}

// Статус открыт ли всплывающий диалог
var isPopupOpen = false;

function OpenPopup(post_id, shouldPushState) {
	if (isPopupOpen) {
		return;
	}
	isPopupOpen = true;
	//var url = jQuery( this ).attr( 'href' );
	var loading_indicator = jQuery('#box_loader');
	loading_indicator.show();
	var body = jQuery('body')
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
				var popup = jQuery('<div class="popup-dialog-wrapper"><div class="popup-dialog">' + response + '</div><div class="popup-wrapper-close-button"/></div>');
				body.append(popup);
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
	console.debug(text);
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
			var post_id = jQuery( this ).attr( 'postid' );
			OpenPopup(post_id, true);
		}				
	});

	CheckCurrentPageParameters(false);
});
