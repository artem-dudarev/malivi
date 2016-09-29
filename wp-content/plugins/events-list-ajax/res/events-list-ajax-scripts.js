
function CollectData( wrapper ){
	var data = {};
	wrapper.find('select').each( function(){
		if( ( jQuery( this ).attr( 'name' ) != 'orderby' || jQuery( this ).val() != null ) && jQuery( this ).attr( 'disabled' ) != 'disabled' ){				
			if( jQuery( this ).val() != '' ){
				data[ jQuery( this ).attr( 'name' ) ] = jQuery( this ).val() ;
			}
		}
	});
	
	wrapper.find('input').each( function(){
		if( typeof( jQuery( this ).attr( 'name' ) ) != 'undefined' && ( typeof jQuery( this ).attr( 'disabled' ) == 'undefined' || jQuery( this ).attr( 'disabled' ) == false ) ){
			if( jQuery( this ).hasClass( 'sf-date' ) || jQuery( this ).attr( 'type' ) == 'hidden' || jQuery( this ).attr( 'name' ).substr( jQuery( this ).attr( 'name' ).length - 2, 2 ) != '[]' ){
				if( jQuery( this ).val() != '' ){
					if( jQuery( this ).attr( 'type' ) != 'radio' || jQuery( this ).prop( 'checked' ) ){
						if( jQuery( this ).attr( 'name' ).substr( jQuery( this ).attr( 'name' ).length - 2, 2 ) != '[]' ){
							data[ jQuery( this ).attr( 'name' ) ] = jQuery( this ).val() ;
						} else {
							var data_name = jQuery( this ).attr( 'name' ).substr( 0, jQuery( this ).attr( 'name' ).length - 2 )
							if( typeof( data[ data_name ] ) == 'undefined' )
								data[ data_name ] = [];
							data[ data_name ].push( jQuery( this ).val() );
						}
					}
				}
			} else{
				var n = jQuery( this ).attr( 'name' ).substr( 0, jQuery( this ).attr( 'name' ).length - 2 );
			
				if( jQuery( this ).prop( 'checked' ) ){
					if( typeof data[n] == 'undefined' )
						data[n] = [];					
					data[n].push( jQuery( this ).val() );
				}
			}
		}
	});
	return data;
}

function GetFiltersDataWithoutId(data) {
	var result = {};
	for (var key in data) {
		if (key === "search-id") {
			continue;
		}
		result[key] = data[key];
	}
	return result;
}

function SetUrlParameter(key, value, create_history_entry) {
	//key = encodeURI(key);
	//value = encodeURI(value);
	var parameters = [];
	var hash = location.hash.substr(1);
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
	var address = '/';
	if (parameters.length > 0) {
		address = '#' + parameters.join('&');
	}
	if (create_history_entry) {
		window.history.pushState('forward', null, address);
	} else {
		//location.href = '#filter-' + JSON.stringify( CollectData( wrapper));
		history.replaceState(undefined, undefined, address);
	}
}

function GetUrlParameter(key) {
	var result = '';
	var hash = location.hash.substr(1);
	if (hash.length > 0) {
		var parameters = hash.split('&');
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

var current_page = 1;
var pages_count = 0;
var isPopupOpen = false;
var current_request_id = 1;
// Айди запроса, с которого начался новый сеанс(новый фильтр), все предыдущие запросы нужно игнорировать
var min_acceptable_request_id = 1;

function GetFilterResults( is_append ) {
	var wrapper = jQuery( '.sf-wrapper' );
	var filters_data = CollectData( wrapper );
	
	if (is_append) {
		if (current_page == pages_count) {
			return;
		}
		current_page++;
	} else {
		current_page = 1;
		min_acceptable_request_id = current_request_id;
	}
	var request_data = {
		action		:	'sf-search',
		data		:	filters_data,
		request_id	: current_request_id,
		page		: current_page
	};

	var request_id = current_request_id;
	current_request_id++;
	var events_list_container = jQuery('.events-list-table');
	if (!is_append) {
		events_list_container.animate({opacity:.7}, 200);
	}
	//wrapper.css({opacity:.1});
	
	
	jQuery.post(
		sf_ajax_root,
		request_data,
		function (response_data) {
			var response = JSON.parse(response_data);
			console.debug("response_id: " + response.request_id + ", local_id=" + request_id);
			if ( response.request_id < min_acceptable_request_id) {
				console.debug("response is outdated");
				return;
			}
			if( response.request_id != request_id) {
				console.debug("invalid response received");
				return;
			}
			//wrapper.css({opacity:1});
			events_list_container.finish();
			events_list_container.css({opacity:1});
			if(is_append) {
				events_list_container.append(response.html);
			} else {
				pages_count = response.pages_count;
				events_list_container.html(response.html);
			}
			
			jQuery( '.events-list-row' ).click( function( event ){
				if(event.which == 1) {
					event.preventDefault();
					var post_id = jQuery( this ).attr( 'postid' );
					OpenPopup(post_id);
				}				
			});
		}
	);
}

function OpenPopup(post_id, shouldPushState = true) {
	if (isPopupOpen) {
		return;
	}
	isPopupOpen = true;
	//var url = jQuery( this ).attr( 'href' );
	
	var body = jQuery('body')
	body.addClass('no-scroll');
	var shadow = jQuery('<div class="popup-dialog-shadow"></div>');
	body.append(shadow);
	if (shouldPushState) {
		SetUrlParameter('show', post_id, true);
		//window.history.pushState('forward', null, '#show=' + post_id);
		 //document.location.search = 'show=' + post_id;
	}
	// Получить данные страницы через ajax и создать из них по шаблону страницу
	
	var settings = {
		action	:	'get-post-page',
		post_id	: post_id	
	};
	
	jQuery.post(
		sf_ajax_root,
		settings,
		function( response ) {
			if (isPopupOpen) {
				var popup = jQuery('<div class="popup-dialog-wrapper"><div class="popup-dialog">' + response + '</div></div>');
				body.append(popup);
				jQuery('.popup-dialog-wrapper').click(function(e) {
					event.preventDefault();
					SetUrlParameter('show', '', true);
					ClosePopup();
				});

				jQuery('.popup-dialog').click(function(e) {
					e.stopPropagation();
				});
			}
		}
	);
	
	//document.location = url;
	//jQuery('#page').addClass('no-scroll');
}

function ClosePopup() {
	if (!isPopupOpen) {
		return;
	}
	isPopupOpen = false;
	jQuery( '.popup-dialog-wrapper, .popup-dialog-shadow' ).remove();
	jQuery('body').removeClass('no-scroll');
	//jQuery('#page').removeClass('no-scroll');
}

function CheckCurrentPageParameters() {
	console.debug("check_current_page_parameters");
	// Если загрузилась страница с указанием фильтров, применим эти фильтры
	var post_id_string = GetUrlParameter('show');
	if( post_id_string.length > 0) {
		var post_id = parseInt(post_id_string, 10);
		OpenPopup(post_id, false);
		//return;
	}
	// Если загрузилась страница с указанием фильтров, применим эти фильтры
	var filters_string = GetUrlParameter('filter');
	if( filters_string.length > 0 ) {
		ParseFilters(filters_string);
	}
	// После загрузки страницы - вызываем поиск
	GetFilterResults(false);
}


// Парсит строку адреса и выставляет фильтрам указанные в строке данные
function ParseFilters(filter_string) {
	//console.debug("apply filters: " + filter_string);
	var range_max = '';
	var range_min = '';
	var	hash = JSON.parse( filter_string );
	jQuery('fieldset .sf-element input').each( function() {
		var fieldset_id = jQuery(this).attr('name');
		fieldset_id = fieldset_id.substr(0, fieldset_id.length-2);
		var fieldset_parameters = hash[fieldset_id];
		var field_id = jQuery(this).attr("value")
		var checked = fieldset_parameters != undefined && fieldset_parameters.includes(field_id);
		//console.debug("fieldset="+fieldset_id + ", field_id="+field_id + ", checked=" + checked);
		jQuery(this).prop('checked', checked);
	});
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

jQuery( document ).ready( function() {
	var wrapper = jQuery('.sf-wrapper');
	var anchor = jQuery('#events-list-filters-anchor');
	// Активируем логику, только если на странице есть есть наши фильтры
	if (wrapper.length == 0) {
		// Если фильтров нет, то удалим placeholder в боковом меню
		if (anchor.length > 0) {
			anchor.parent().parent().hide();
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

	// Добавим автоподгрузку на скрол
	var win = jQuery(window);
	// Each time the user scrolls
	win.scroll(function() {
		//$events_list_container = jQuery('.entry-content').prepend('<div>' + win.scrollTop() + '</div>');
		// End of the document reached?
		if (jQuery(document).height() - win.height()*5/4 <= win.scrollTop()) {
			//$('#loading').show();

			//GetFilterResults(true);
		}
	});
	win.keydown(function(e) {
		if (isPopupOpen && e.keyCode == 27) { // escape key maps to keycode `27`
			event.preventDefault();
			SetUrlParameter('show', '', true);
			ClosePopup();
		}
	});
	win.on('popstate', function(event) {
		if (isPopupOpen) {
			ClosePopup();
		}
		CheckCurrentPageParameters();
	});
		
	// Отслеживаем изменения фильтров и вызываем поиск после каждого изменения
	jQuery( document ).on( 'change', '.sf-filter input, .sf-filter select', function() {
		//console.debug("on_filters_change");
		var possible_cond_key = jQuery( this ).closest( '.sf-element' ).attr( 'data-id' );
		var possible_cond_val = jQuery( this ).val();
		if( ( jQuery( this ).attr('type') == 'checkbox' || jQuery( this ).attr('type') == 'radio' ) && !jQuery( this ).prop( 'checked' ) ) {
			possible_cond_val = -2;
		}
		jQuery( '.sf-element-hide' ).each( function() {
			if( jQuery( this ).attr( 'data-condkey' ) == possible_cond_key ) {
				if( possible_cond_val == jQuery( this ).attr( 'data-condval' ) ) {
					jQuery( this ).fadeIn();
					jQuery( this ).addClass( 'sf-element' );
					jQuery( this ).find( 'input, select' ).attr( 'disabled', false );
				} else {
					jQuery( this ).hide();
					jQuery( this ).removeClass( 'sf-element' );
					jQuery( this ).find( 'input, select' ).attr( 'disabled', true );
				}
			}
		});
		var wrapper = jQuery( '.sf-wrapper' );
		var wrapper = jQuery( '.sf-wrapper' );
		var filters_data = CollectData( wrapper );
		var filters_string_data = GetFiltersDataWithoutId(filters_data);
		var parameters_string = JSON.stringify( filters_string_data );
		
		if (parameters_string.length > 2) {
			SetUrlParameter('filter', parameters_string, false);
		} else {
			SetUrlParameter('filter', '', false);
		}
		GetFilterResults(false);
	});

	CheckCurrentPageParameters();
});
