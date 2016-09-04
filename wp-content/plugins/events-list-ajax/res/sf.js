
 function collect_data( wrapper ){

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

var current_page = 1;
var pages_count = 0;
function get_filter_results( is_on_load, is_append ) {
	var wrapper = jQuery( '.sf-wrapper' );
	var data = {
		action	:	'sf-search',
		data	:	collect_data( wrapper )
	};
	
	if( typeof is_on_load == 'undefined' ){
		location.href = '#filter-' + JSON.stringify( data.data ); 
	}

	
	if ( typeof is_append != 'undefined' ) {
		if (current_page == pages_count) {
			return;
		}
		current_page++;
		data.data.page = current_page;
	}

	$article = jQuery('.entry-content');
	if(typeof is_append == 'undefined') {
		$article.css({opacity:.7});
	}
	//wrapper.css({opacity:.1});
	
	search_data = data.data;
	jQuery.post(
		sf_ajax_root,
		data,
		function( response ){
			response = JSON.parse( response );
			if( JSON.stringify( search_data ) != JSON.stringify( response.post ) ) {
				//return;
			}
			//wrapper.css({opacity:1});
			$article.css({opacity:1});
			if( typeof is_append == 'undefined' ) {
				pages_count = response.pages_count;
				$article.html(response.html);
			} else {
				$article.append(response.html);
			}
			
			jQuery( '.events-list-row' ).click( function( event ){
				if(event.which == 1) {
					event.preventDefault();
					var post_id = jQuery( this ).attr( 'postid' );
					open_popup(post_id);
				}				
			});
		}
	);
}

function open_popup(post_id) {
	//var url = jQuery( this ).attr( 'href' );
	
	var shadow = '<div class="popup-dialog-shadow"></div>';
	jQuery( shadow ).appendTo( 'body' );
	window.history.pushState('forward', null, '#show=' + post_id);
	//jQuery('.popup-dialog').load('event/kaliningrad-siti-dzhaz1');
	//var page = jQuery.get(url);
	//jQuery('.popup-dialog').html(page);
	// Получить данные страницы через ajax и создать из них по шаблону страницу
	
	var settings = {
		action	:	'get-post-page',
		post_id	: post_id	
	};
	
	jQuery.post(
		sf_ajax_root,
		settings,
		function( response ){
			var popup = '<div class="popup-dialog">' + response + '</div>';
			jQuery(popup).appendTo('body');
			
			jQuery(window).on('popstate', function(event) {
				close_popup();
			});
		}
	);
	
	//document.location = url;
	jQuery(document).keydown(function(e) {
		if (e.keyCode == 27) { // escape key maps to keycode `27`
			event.preventDefault();
			window.history.back();
		}
	});

	jQuery('.popup-dialog-shadow').click(function(e) {
		event.preventDefault();
		window.history.back();
	});
}

function close_popup() {
	jQuery( '.popup-dialog, .popup-dialog-shadow' ).remove();
}


// Парсит строку адреса и выставляет фильтрам указанные в строке данные
function parse_location_data(filter_string) {
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
		//$article = jQuery('.entry-content').prepend('<div>' + win.scrollTop() + '</div>');
		// End of the document reached?
		if (jQuery(document).height() - win.height()*5/4 <= win.scrollTop()) {
			//$('#loading').show();

			get_filter_results(false, true);
		}
	});
		
	// Отслеживаем изменения фильтров и вызываем поиск после каждого изменения
	jQuery( document ).on( 'change', '.sf-filter input, .sf-filter select', function(){
		var possible_cond_key = jQuery( this ).closest( '.sf-element' ).attr( 'data-id' );
		var possible_cond_val = jQuery( this ).val();
		if( ( jQuery( this ).attr('type') == 'checkbox' || jQuery( this ).attr('type') == 'radio' ) && !jQuery( this ).prop( 'checked' ) )
			possible_cond_val = -2;
		jQuery( '.sf-element-hide' ).each( function(){
			if( jQuery( this ).attr( 'data-condkey' ) == possible_cond_key ){
				if( possible_cond_val == jQuery( this ).attr( 'data-condval' ) ){
					jQuery( this ).fadeIn();
					jQuery( this ).addClass( 'sf-element' );
					jQuery( this ).find( 'input, select' ).attr( 'disabled', false );
				}else{
					jQuery( this ).hide();
					jQuery( this ).removeClass( 'sf-element' );
					jQuery( this ).find( 'input, select' ).attr( 'disabled', true );
				}
			}
		});
		jQuery( '.sf-wrapper' ).find( 'input[name="page"]' ).remove();
		if( jQuery( '.sf-wrapper' ).find( '.sf-button-btnsearch' ).length == 0 )
			get_filter_results();
	});
	
	// Переключение страниц
	jQuery( document ).on( 'click','.sf-nav-click', function( event ){
		event.preventDefault();
		jQuery( '.sf-wrapper' ).find( 'input[name="page"]' ).remove();
		var txt = '<input type="hidden" name="page" value="' + jQuery( this ).attr( 'data-href' ) + '" />';
		jQuery( txt ).appendTo( '.sf-wrapper' );
		get_filter_results();
		jQuery('html, body').animate({ scrollTop: ( jQuery('.sf-wrapper').offset().top - 25 )}, 'slow');
	});
	
	// Если загрузилась страница с указанием фильтров, применим эти фильтры
	if( location.hash.substr( 0, 8 ) == '#filter-' ) {
		parse_location_data(location.hash.substr( 8 ));
		
	}

	// После загрузки страницы - вызываем поиск
	get_filter_results( true );

	// Если загрузилась страница с указанием фильтров, применим эти фильтры
	if( location.hash.substr( 0, 6 ) == '#show=' ) {
		var post_id = parseInt(location.hash.substr( 6 ), 10);
		open_popup(post_id);
	}
	
});
