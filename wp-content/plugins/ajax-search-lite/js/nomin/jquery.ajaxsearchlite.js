/*! Ajax Search Lite 4.6 js */
(function ($) {

    var methods = {

        init: function (options, elem) {
            var $this = this;

            this.elem = elem;
            this.$elem = $(elem);

            $this.searching = false;
            $this.o = $.extend({}, options);
            $this.n = new Object();
            $this.n.container =  $(this.elem);
            $this.o.rid = $this.n.container.attr('id').match(/^ajaxsearchlite(.*)/)[1];
            $this.o.id = $this.n.container.attr('id').match(/^ajaxsearchlite(.*)/)[1];
            $this.n.probox = $('.probox', $this.n.container);
            $this.n.proinput = $('.proinput', $this.n.container);
            $this.n.text = $('.proinput input.orig', $this.n.container);
            $this.n.textAutocomplete = $('.proinput input.autocomplete', $this.n.container);
            $this.n.loading = $('.proinput .loading', $this.n.container);
            $this.n.proloading = $('.proloading', $this.n.container);
            $this.n.proclose = $('.proclose', $this.n.container);
            $this.n.promagnifier = $('.promagnifier', $this.n.container);
            $this.n.prosettings = $('.prosettings', $this.n.container);
            $this.n.searchsettings = $('#ajaxsearchlitesettings' + $this.o.rid);
            $this.n.resultsDiv = $('#ajaxsearchliteres' + $this.o.rid);
            $this.n.hiddenContainer = $('#asl_hidden_data');
            $this.n.aslItemOverlay = $('.asl_item_overlay', $this.n.hiddenContainer);

            $this.resizeTimeout = null;

            $this.n.showmore = $('.showmore a', $this.n.resultsDiv);
            $this.n.items = $('.item', $this.n.resultsDiv);
            $this.n.results = $('.results', $this.n.resultsDiv);
            $this.n.resdrg = $('.resdrg', $this.n.resultsDiv);

            // Isotopic Layout variables
            $this.il = {
                columns: 3,
                itemsPerPage: 6
            };

            $this.firstClick = true;
            $this.post = null;
            $this.postAuto = null;
            $this.cleanUp();
            $this.n.textAutocomplete.val('');
            $this.o.resultitemheight = parseInt($this.o.resultitemheight);
            $this.scroll = new Object();
            $this.settScroll = null;
            $this.n.resultsAppend = $('#wpdreams_asl_results_' + $this.o.id);
            $this.currentPage = 1;
            $this.isotopic = null;

            $this.animation = "bounceIn";
            switch ($this.o.resultstype) {
                case "vertical":
                    $this.animation = $this.o.vresultanimation;
                    break;
                default:
                    $this.animation = $this.o.hresultanimation;
            }

            $this.filterFns = {
                number: function () {
                    var $parent = $(this).parent();
                    while (!$parent.hasClass('isotopic')) {
                        $parent = $parent.parent();
                    }
                    var number = $(this).attr('data-itemnum');
                    //var currentPage = parseInt($('nav>ul li.asl_active span', $parent).html(), 10);
                    var currentPage = $this.currentPage;
                    //var itemsPerPage = parseInt($parent.data("itemsperpage"));
                    var itemsPerPage = $this.il.itemsPerPage;

                    return (
                        (parseInt(number, 10) < itemsPerPage * currentPage) &&
                            (parseInt(number, 10) >= itemsPerPage * (currentPage - 1))
                        );
                }
            };

            $this.disableMobileScroll = false;
            $this.n.searchsettings.detach().appendTo("body");

            if ($this.o.resultsposition == 'hover') {
                $this.n.resultsDiv.detach().appendTo("body");
            } else if ($this.n.resultsAppend.length > 0) {
                $this.n.resultsDiv.detach().appendTo($this.n.resultsAppend);
            }

            $('fieldset' ,$this.n.searchsettings).each(function(){
                $('.asl_option:not(.hiddend)', this).last().addClass("asl-o-last");
            });

            $this.createVerticalScroll();

            if (detectIE())
                $this.n.container.addClass('asl_msie');

            // Calculates the settings animation attributes
            $this.initSettingsAnimations();

            // Calculates the results animation attributes
            $this.initResultsAnimations();

            $this.initEvents();

            return this;
        },

        duplicateCheck: function() {
            var $this = this;
            var duplicateChk = {};

            $('div[id*=ajaxsearchlite]').each (function () {
                if (duplicateChk.hasOwnProperty(this.id)) {
                    $(this).remove();
                } else {
                    duplicateChk[this.id] = 'true';
                }
            });
        },

        analytics: function(term) {
            var $this = this;
            if ($this.o.analytics && $this.o.analyticsString != '' &&
                typeof ga == "function") {
                ga('send', 'pageview', {
                    'page': '/' + $this.o.analyticsString.replace("{asl_term}", term),
                    'title': 'Ajax Search'
                });
            }
        },

        createVerticalScroll: function () {
            var $this = this;

            $this.scroll = $this.n.results.mCustomScrollbar({
                contentTouchScroll: true,
                scrollButtons: {
                    enable: true
                },
                callbacks: {
                    onScroll: function () {
                        if (isMobile()) return;
                        var top = parseInt($('.mCSB_container', $this.n.results).position().top);
                        var children = $('.mCSB_container .resdrg').children();
                        var overall = 0;
                        var prev = 3000;
                        var diff = 4000;
                        var s_diff = 10000;
                        var s_overall = 10000;
                        var $last = null;
                        children.each(function () {
                            diff = Math.abs((Math.abs(top) - overall));
                            if (diff < prev) {
                                s_diff = diff;
                                s_overall = overall;
                                $last = $(this);
                            }
                            overall += $(this).outerHeight(true);
                            prev = diff;
                        });
                        if ($last.hasClass('group'))
                            s_overall = s_overall + ($last.outerHeight(true) - $last.outerHeight(false));

                        $this.scroll.mCustomScrollbar("scrollTo", $last,{
                            scrollInertia: 200,
                            callbacks: false
                        });
                    }
                }
            });

        },

        createHorizontalScroll: function () {
            var $this = this;

            $this.scroll = $this.n.results.mCustomScrollbar({
                horizontalScroll: true,
                contentTouchScroll: true,
                scrollButtons: {
                    enable: true,
                    scrollType: 'pixels',
                    scrollSpeed: 'auto',
                    scrollAmount: 100
                }
            });

        },

        initEvents: function () {
            var $this = this;

            $($this.n.text.parent()).submit(function (e) {
                e.preventDefault();
                $this.n.text.keyup();
            });
            $this.n.text.click(function () {
                if ($this.firstClick) {
                    $(this).val('');
                    $this.firstClick = false;
                }
            });
            $this.n.resultsDiv.css({
                opacity: 0
            });
            $(document).bind("click touchend", function () {
                $this.hideSettings();
                if ($this.opened == false || $this.o.closeOnDocClick != 1) return;
                $this.hideResults();
            });
            $this.n.proclose.bind("click touchend", function () {
                if ($this.opened == false) return;
                $this.n.text.val("");
                $this.n.textAutocomplete.val("");
                $this.hideResults();
                $this.n.text.focus();
            });
            $($this.elem).bind("click touchend", function (e) {
                e.stopImmediatePropagation();
            });
            $this.n.resultsDiv.bind("click touchend", function (e) {
                e.stopImmediatePropagation();
            });
            $this.n.searchsettings.bind("click touchend", function (e) {
                e.stopImmediatePropagation();
            });

            /*
             * Memorize the focus status
             */
            var focused = false;
            $this.n.text.focus(function(){
                focused = true;
            });

            var trEvent = "touchend";
            if ( $this.n.results.hasClass("mCustomScrollbar") )
                trEvent = "mousedown";
            $this.n.resultsDiv.bind(trEvent, function (e) {
                if (isMobile() && focused) {
                    /*
                     * This will make sure that if the user clicked on the results
                     * then nothing happens.
                     */
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();

                    /**
                     * mcustomScrollbar catches all events and prevents them.
                     * So instead we cancel the first click on the result URL, preventing
                     * accidental clicks or clicks intended to just hide the keyboard.
                     * This is not needed for Isotopic and Polaroid results.
                     */
                    if ( $this.n.results.hasClass("mCustomScrollbar") ) {
                        $this.n.resultsDiv.one("click", "a.asl_res_url", function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            return false;
                        });
                    }

                    // Hinde the mobile keyboard
                    document.activeElement.blur();
                    focused = false;
                    return false;
                }
            });

            $this.n.prosettings.on("click", function () {
                if ($this.n.prosettings.data('opened') == 0) {
                    $this.showSettings();
                } else {
                    $this.hideSettings();
                }
            });

            var resizeTimer;
            $(window).on("resize", function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    $this.resize();
                }, 250);
            });

            var scrollTimer;
            $(window).on("scroll", function () {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(function() {
                    $this.scrolling(false);
                }, 250);
            });

            $this.initNavigationEvent();

            $(window).trigger('resize');
            $(window).trigger('scroll');

            $this.initMagnifierEvent();
            $this.initAutocompleteEvent();
            $this.initFacetEvents();
        },

        initNavigationEvent: function () {
            var $this = this;

            $($this.n.resultsDiv).on('mouseenter', '.item',
                function () {
                    //alert("hover");
                    $('.item', $this.n.resultsDiv).removeClass('hovered');
                    $(this).addClass('hovered');
                }
            );
            $($this.n.resultsDiv).on('mouseleave', '.item',
                function () {
                    //alert("hover");
                    $('.item', $this.n.resultsDiv).removeClass('hovered');
                }
            );

            $(document).keydown(function (e) {

                if (window.event) {
                    var keycode = window.event.keyCode;
                    var ktype = window.event.type;
                } else if (e) {
                    var keycode = e.which;
                    var ktype = e.type;
                }
                //$('.item', $this.n.resultsDiv).hover();

                if ($('.item', $this.n.resultsDiv).length > 0 && $this.n.resultsDiv.css('display') != 'none') {
                    if (keycode == 40) {
                        e.stopPropagation();
                        e.preventDefault();
                        $this.n.text.blur();

                        if ($this.post != null) $this.post.abort();
                        if ($('.item.hovered', $this.n.resultsDiv).length == 0) {
                            $('.item', $this.n.resultsDiv).first().addClass('hovered');
                        } else {
                            $('.item.hovered', $this.n.resultsDiv).removeClass('hovered').next().next('.item').addClass('hovered');
                        }
                        $this.scroll.mCustomScrollbar("scrollTo", ".resdrg .item.hovered",{
                            scrollInertia: 200,
                            callbacks: false
                        });
                    }
                    if (keycode == 38) {
                        e.stopPropagation();
                        e.preventDefault();
                        $this.n.text.blur();

                        if ($this.post != null) $this.post.abort();
                        if ($('.item.hovered', $this.n.resultsDiv).length == 0) {
                            $('.item', $this.n.resultsDiv).last().addClass('hovered');
                        } else {
                            $('.item.hovered', $this.n.resultsDiv).removeClass('hovered').prev().prev('.item').addClass('hovered');
                        }
                        $this.scroll.mCustomScrollbar("scrollTo", ".resdrg .item.hovered",{
                            scrollInertia: 200,
                            callbacks: false
                        });
                    }

                    // Trigger click on return key
                    if ( keycode == 13 && $('.item.hovered', $this.n.resultsDiv).length > 0 ) {
                        e.stopPropagation();
                        e.preventDefault();
                        $('.item.hovered a.asl_res_url', $this.n.resultsDiv).get(0).click();
                    }
                }
            });
        },

        initMagnifierEvent: function () {
           var $this = this;

            var t;
            $this.n.promagnifier.add($this.n.text).bind('click keyup', function (e) {
                if (window.event) {
                    $this.keycode = window.event.keyCode;
                    $this.ktype = window.event.type;
                } else if (e) {
                    $this.keycode = e.which;
                    $this.ktype = e.type;
                }

                var isInput = $(this).hasClass("orig");

                if ($this.n.text.val().length < $this.o.charcount) {
                    $this.n.proloading.css('display', 'none');
                    $this.hideResults();
                    if ($this.post != null) $this.post.abort();
                    clearTimeout(t);
                    return;
                }

                // If redirection is set to the results page, or custom URL
                if (
                    (!isInput && $this.o.redirectonclick == 1 && $this.ktype == 'click' && $this.o.redirectClickTo == 'results_page' ) ||
                    (isInput && $this.o.redirect_on_enter == 1 && $this.ktype == 'keyup' && $this.keycode == 13 && $this.o.redirectEnterTo == 'results_page' )
                ) {
                    var plus = '';
                    var url = '?s=' + $this.n.text.val();

                    if ($this.o.overridewpdefault) {
                        asp_submit_to_url($this.o.homeurl + url + "&asl_active=1", 'post', {
                            p_asl_data: $('form', $this.n.searchsettings).serialize()
                        });
                    } else {
                        asp_submit_to_url($this.o.homeurl + url, 'post', {
                            np_asl_data: $('form', $this.n.searchsettings).serialize()
                        });
                    }

                    $this.n.proloading.css('display', 'none');
                    $this.hideResults();
                    if ($this.post != null) $this.post.abort();
                    clearTimeout(t);
                    return;
                }

                // Ignore arrows
                if ($this.keycode >= 37 && $this.keycode <= 40) return;
                if ($(this).hasClass('orig') && $this.ktype == 'click') return;

                if ($this.o.triggeronclick == 0 && $this.ktype == 'click') return;
                if ($this.o.triggerontype == 0 && $this.ktype == 'keyup') return;
                
                if ($this.post != null) $this.post.abort();
                clearTimeout(t);
                t = setTimeout(function () {
                    $this.search();
                }, 300);
            });
        },

        initFacetEvents: function() {
            var $this = this;
            if ($this.o.trigger_on_facet_change == 1)
                $('input', $this.n.searchsettings).change(function(){
                    if ($this.n.text.val().length < $this.o.charcount) return;
                    if ($this.post != null) $this.post.abort();
                    $this.search();
                });
        },

        destroy: function () {
            return this.each(function () {
                var $this = $.extend({}, this, methods);
                $(window).unbind($this);
            })
        },
        searchfor: function (phrase) {
            $(".proinput input", this).val(phrase).trigger("keyup");
        },

        initAutocompleteEvent: function () {
            var $this = this;
            var tt;

            if ($this.o.autocomplete.enabled == 1 && !isMobile()) {
                $this.n.text.keyup(function (e) {
                    if (window.event) {
                        $this.keycode = window.event.keyCode;
                        $this.ktype = window.event.type;
                    } else if (e) {
                        $this.keycode = e.which;
                        $this.ktype = e.type;
                    }

                    var thekey = 39;
                    // Lets change the keykode if the direction is rtl
                    if ($('body').hasClass('rtl'))
                        thekey = 37;
                    if ($this.keycode == thekey && $this.n.textAutocomplete.val() != "") {
                        e.preventDefault();
                        $this.n.text.val($this.n.textAutocomplete.val());
                        if ($this.post != null) $this.post.abort();
                        $this.search();
                    } else {
                        if ($this.postAuto != null) $this.postAuto.abort();
                        $this.autocompleteGoogleOnly();
                    }
                });
            }
        },

        // If only google source is used, this is much faster..
        autocompleteGoogleOnly: function () {
            var $this = this;

            var val = $this.n.text.val();
            if ($this.n.text.val() == '') {
                $this.n.textAutocomplete.val('');
                return;
            }
            var autocompleteVal = $this.n.textAutocomplete.val();
            if (autocompleteVal != '' && autocompleteVal.indexOf(val) == 0) {
                return;
            } else {
                $this.n.textAutocomplete.val('');
            }

            $.ajax({
                url: 'https://clients1.google.com/complete/search',
                dataType: 'jsonp',
                data: {
                    q: val,
                    hl: $this.o.autocomplete.lang,
                    nolabels: 't',
                    client: 'hp',
                    ds: ''
                },
                success: function(data) {
                    if (data[1].length > 0) {
                        response = data[1][0][0].replace(/(<([^>]+)>)/ig,"");
                        response = $('<textarea />').html(response).text();
                        response = response.substr(val.length);
                        $this.n.textAutocomplete.val(val + response);
                    }
                }
            });
        },

        search: function () {
            var $this = this;

            if ($this.searching && 0) return;
            if ($this.n.text.val().length < $this.o.charcount) return;

            $this.searching = true;
            $this.n.proloading.css({
                display: "block"
            });
            $this.n.proclose.css({
                display: "none"
            });
            //$this.hideSettings();
            //$this.hideResults();
            var data = {
                action: 'ajaxsearchlite_search',
                aslp: $this.n.text.val(),
                asid: $this.o.id,
                options: $('form', $this.n.searchsettings).serialize()
            };
            $this.analytics($this.n.text.val());

            // New method without JSON
            $this.post = $.post(ASL.ajaxurl, data, function (response) {
                response = response.replace(/^\s*[\r\n]/gm, "");
                response = response.match(/!!ASLSTART!!(.*[\s\S]*)!!ASLEND!!/)[1];

                // bye bye JSON

                $this.n.resdrg.html("");
                $this.n.resdrg.html(response);

                $(".asl_keyword", $this.n.resdrg).bind('click', function () {
                    $this.n.text.val($(this).html());
                    $('input.orig', $this.n.container).val($(this).html()).keydown();
                    $('form', $this.n.container).trigger('submit', 'ajax');
                    $this.search();
                });

                $this.n.items = $('.item', $this.n.resultsDiv);

                // Redirect to the first result if enabled :)
                if (
                    $('.asl_res_url', $this.n.resultsDiv).length > 0 &&
                    ($this.o.redirectonclick == 1 && $this.ktype == 'click' && $this.o.redirectClickTo != 'results_page' ) ||
                    ($this.o.redirect_on_enter == 1 && $this.ktype == 'keyup' && $this.keycode == 13 && $this.o.redirectEnterTo != 'results_page' )
                ) {
                    location.href = $( $('.asl_res_url', $this.n.resultsDiv).get(0)).attr('href');
                    return false;
                }

                $this.showResults();
                $this.scrollToResults();

                if ($this.n.items.length == 0) {
                    if ($this.n.showmore != null) {
                        $this.n.showmore.css('display', 'none');
                    }
                } else {
                    if ($this.n.showmore != null) {
                        $this.n.showmore.css('display', 'block');
                        $this.n.showmore.attr('href', $this.o.homeurl + '?s=' + $this.n.text.val());
                    }
                }

            }, "text");
        },

        showLoader: function( ) {
            var $this = this;
            $this.n.proloading.css({
                display: "block"
            });
        },

        hideLoader: function( ) {
            var $this = this;

            $this.n.proloading.css({
                display: "none"
            });
            $this.n.results.css("display", "");
        },

        showResultsBox: function() {
            var $this = this;

            $this.n.resultsDiv.css({
                display: 'block',
                height: 'auto'
            });
            $this.n.items.addClass($this.animationOpacity);

            $this.scrolling(true);
            $this.n.resultsDiv.css($this.resAnim.showCSS);
            $this.n.resultsDiv.removeClass($this.resAnim.hideClass).addClass($this.resAnim.showClass);
        },

        showResults: function( ) {
            var $this = this;
            switch ($this.o.resultstype) {
                case 'vertical':
                    $this.showVerticalResults();
                    break;
                default:
                    $this.showHorizontalResults();
                    break;
            }

            $this.hideLoader();

            $this.n.proclose.css({
                display: "block"
            });

            if ($this.n.showmore != null) {
                if ($this.n.items.length > 0) {
                    $this.n.showmore.css({
                        'display': 'block'
                    });
                } else {
                    $this.n.showmore.css({
                        'display': 'none'
                    });
                }
            }

            /*if (isMobile() && $this.o.mobile.hide_keyboard)
                document.activeElement.blur();*/

            $this.resultsOpened = true;
        },

        hideResults: function( ) {
            var $this = this;

            if ( !$this.resultsOpened ) return false;

            $this.n.resultsDiv.removeClass($this.resAnim.showClass).addClass($this.resAnim.hideClass);
            setTimeout(function(){
                $this.n.resultsDiv.css($this.resAnim.hideCSS);
            }, $this.resAnim.duration);

            $this.n.proclose.css({
                display: "none"
            });
            if ($this.n.showmore != null) {
                $this.n.showmore.css({
                    'display': 'none'
                });
            }

            if (isMobile())
                document.activeElement.blur();

            $this.resultsOpened = false;
        },

        scrollToResults: function( ) {
            $this = this;
            if (this.o.scrollToResults!=1) return;
            if (this.$elem.parent().hasClass("asl_preview_data")) return;
            if ($this.o.resultsposition == "hover")
              var stop = $this.n.probox.offset().top - 20;
            else
              var stop = $this.n.resultsDiv.offset().top - 20;
            if ($("#wpadminbar").length > 0)
                stop -= $("#wpadminbar").height();
            stop = stop < 0 ? 0 : stop;
            $('body, html').animate({
                "scrollTop": stop
            }, {
                duration: 500
            });
        },

        createGroup: function (r) {
            return "<div class='group'>" + r + "</div>";
        },

        showVerticalResults: function () {
            var $this = this;

            $this.showResultsBox();

            if ($this.n.items.length > 0) {
                var count = (($this.n.items.length < $this.o.itemscount) ? $this.n.items.length : $this.o.itemscount);
                var groups = $('.group', $this.n.resultsDiv);

                if ($this.n.items.length <= $this.o.itemscount) {
                    $this.n.results.css({
                        height: 'auto'
                    });
                } else {

                    // Set the height to a fictive value to refresh the scrollbar
                    // .. otherwise the height is not calculated correctly, because of the scrollbar width.
                    $this.n.results.css({
                        height: 30
                    });
                    $this.scroll.mCustomScrollbar('update');
                    $this.resize();

                    // Here now we have the correct item height values with the scrollbar enabled
                    var i = 0;
                    var h = 0;

                    $this.n.items.each(function () {
                        h += $(this).outerHeight(true);
                        i++;
                    });

                    // Count the average height * viewport size
                    i = i < 1 ? 1 : i;
                    h = h / i * count;

                    $this.n.results.css({
                        height: h
                    });
                }

                window.sscroll = $this.scroll;

                // Disable the scrollbar first, to avoid glitches
                $this.scroll.mCustomScrollbar('disable', true);

                // After making the changes trigger an update to re-enable
                $this.scroll.mCustomScrollbar('update');
                // ..then all the other math stuff from the resize event
                $this.resize();
                // .. and finally scroll back to the first item nicely
                $this.scroll.mCustomScrollbar('scrollTo', 0);

                if ($this.o.highlight == 1) {
                    var wholew = (($this.o.highlightwholewords == 1) ? true : false);
                    $("div.item", $this.n.resultsDiv).highlight($this.n.text.val().split(" "), { element: 'span', className: 'highlighted', wordsOnly: wholew });
                }

            }
            $this.resize();
            if ($this.n.items.length == 0) {
                var h = ($('.nores', $this.n.results).outerHeight(true) > ($this.o.resultitemheight) ? ($this.o.resultitemheight) : $('.nores', $this.n.results).outerHeight(true));
                $this.n.results.css({
                    height: 11110
                });
                $this.scroll.mCustomScrollbar('update');
                $this.n.results.css({
                    height: 'auto'
                });
            }
            $this.addAnimation();
            $this.scrolling(true);
            $this.searching = false;


        },

        hideVerticalResults: function () {
            var $this = this;

            $this.disableMobileScroll = false;

            $this.n.resultsDiv
                .animate({
                    opacity: 0,
                    height: 0
                }, {
                    duration: 120,
                    complete: function () {
                        $(this).css({
                            visibility: "hidden",
                            display: "none"
                        });
                    }
                });
        },

        addAnimation: function () {
            var $this = this;
            var i = 0;
            var j = 1;
            $this.n.items.each(function () {
                var x = this;
                setTimeout(function () {
                    $(x).addClass($this.animation);
                }, i);
                i = i + 60;
                j++;
            });
        },

        removeAnimation: function () {
            var $this = this;
            $this.n.items.each(function () {
                var x = this;
                $(x).removeClass($this.animation);
            });
        },

        initSettingsAnimations: function() {
            var $this = this;
            var animDur = 300;

            $this.settAnim = {
                "showClass": "asl_an_fadeInDrop",
                "showCSS": {
                    "visibility": "visible",
                    "display": "block",
                    "opacity": 1,
                    "animation-duration": animDur
                },
                "hideClass": "asl_an_fadeOutDrop",
                "hideCSS": {
                    "visibility": "hidden",
                    "opacity": 0,
                    "display": "none"
                },
                "duration": animDur
            };

            $this.n.searchsettings.css({
                "-webkit-animation-duration": $this.settAnim.duration + "ms",
                "animation-duration": $this.settAnim.duration + "ms"
            });
        },

        initResultsAnimations: function() {
            var $this = this;
            var animDur = 300;

            $this.resAnim = {
                "showClass": "asl_an_fadeInDrop",
                "showCSS": {
                    "visibility": "visible",
                    "display": "block",
                    "opacity": 1,
                    "animation-duration": animDur
                },
                "hideClass": "asl_an_fadeOutDrop",
                "hideCSS": {
                    "visibility": "hidden",
                    "opacity": 0,
                    "display": "none"
                },
                "duration": animDur
            };

            $this.n.resultsDiv.css({
                "-webkit-animation-duration": animDur + "ms",
                "animation-duration": animDur + "ms"
            });
        },

        showSettings: function () {
            var $this = this;

            $this.scrolling(true);
            $this.n.searchsettings.css($this.settAnim.showCSS);
            $this.n.searchsettings.removeClass($this.settAnim.hideClass).addClass($this.settAnim.showClass);

            if ($this.settScroll == null) {
                $this.settScroll = $('.asl_sett_scroll', $this.n.searchsettings).mCustomScrollbar({
                    contentTouchScroll: true
                });
            }
            $this.n.prosettings.data('opened', 1);
        },

        hideSettings: function () {
            var $this = this;

            $this.n.searchsettings.removeClass($this.settAnim.showClass).addClass($this.settAnim.hideClass);
            setTimeout(function(){
                $this.n.searchsettings.css($this.settAnim.hideCSS);
            }, $this.settAnim.duration);

            $this.n.prosettings.data('opened', 0);
        },

        cleanUp: function () {
            var $this = this;

            if ($('.searchsettings', $this.n.container).length > 0) {
                $('body>#ajaxsearchlitesettings' + $this.o.rid).remove();
                $('body>#ajaxsearchliteres' + $this.o.rid).remove();
            }
        },
        resize: function () {
            var $this = this;
            var bodyTop = 0;

            if ( $("body").css("position") != "static" )
                bodyTop = $("body").offset().top;

            if (detectIE() && 0) {
                $this.n.proinput.css({
                    width: ($this.n.probox.width() - 8 - ($this.n.proinput.outerWidth(false) - $this.n.proinput.width()) - $this.n.proloading.outerWidth(true) - $this.n.prosettings.outerWidth(true) - $this.n.promagnifier.outerWidth(true) - 10)
                });
                $this.n.text.css({
                    width: $this.n.proinput.width() - 2 + $this.n.proloading.outerWidth(true),
                    position: 'absolute',
                    zIndex: 2
                });
                $this.n.textAutocomplete.css({
                    width: $this.n.proinput.width() - 2 + $this.n.proloading.outerWidth(true),
                    opacity: 0.25,
                    zIndex: 1
                });
            }

            if ($this.n.prosettings.attr('opened') != 0) {

                if ($this.o.settingsimagepos == 'left') {
                    $this.n.searchsettings.css({
                        display: "block",
                        top: $this.n.prosettings.offset().top + $this.n.prosettings.height() - 2 - bodyTop,
                        left: $this.n.prosettings.offset().left
                    });
                } else {
                    $this.n.searchsettings.css({
                        display: "block",
                        top: $this.n.prosettings.offset().top + $this.n.prosettings.height() - 2 - bodyTop,
                        left: $this.n.prosettings.offset().left + $this.n.prosettings.width() - $this.n.searchsettings.width()
                    });
                }
            }
            if ($this.n.resultsDiv.css('visibility') != 'hidden') {
                if ($this.o.resultsposition != 'block') {
                    var cwidth = $this.n.container.width() - ($this.n.resultsDiv.outerWidth(true) - $this.n.resultsDiv.width());
                    var rwidth = cwidth < 240 ? 240 : cwidth;
                    $this.n.resultsDiv.css({
                        width: rwidth,
                        top: $this.n.container.offset().top + $this.n.container.outerHeight(true) + 10 - bodyTop,
                        left: $this.n.container.offset().left + (cwidth - rwidth)
                    });
                }

            }
        },
        scrolling: function (ignoreVisibility) {
            var $this = this;
            var bodyTop = 0;

            if ( $("body").css("position") != "static" )
                bodyTop = $("body").offset().top;

            if (ignoreVisibility == true || $this.n.searchsettings.css('visibility') == 'visible') {

                if ($this.o.settingsimagepos == 'left') {
                    $this.n.searchsettings.css({
                        display: "block",
                        top: $this.n.prosettings.offset().top + $this.n.prosettings.height() - 2 - bodyTop,
                        left: $this.n.prosettings.offset().left
                    });
                } else {
                    $this.n.searchsettings.css({
                        display: "block",
                        top: $this.n.prosettings.offset().top + $this.n.prosettings.height() - 2 - bodyTop,
                        left: $this.n.prosettings.offset().left + $this.n.prosettings.width() - $this.n.searchsettings.width()
                    });
                }
            }

            if ((ignoreVisibility == true || $this.n.resultsDiv.css('visibility') == 'visible')) {
                var cwidth = $this.n.container.width() - ($this.n.resultsDiv.outerWidth(true) - $this.n.resultsDiv.width());
                var rwidth = cwidth < 240 ? 240 : cwidth;
                if ( ($this.o.resultsposition != 'hover' && $this.n.resultsAppend.length > 0) || $this.n.container.hasClass("hiddend"))
                    rwidth = 'auto';

                $this.n.resultsDiv.css({
                    width: rwidth,
                    top: $this.n.container.offset().top + $this.n.container.outerHeight(true) + 10 - bodyTop,
                    left: $this.n.container.offset().left + (cwidth - rwidth)
                });
            }
        }
    };

    function is_touch_device() {
        return !!("ontouchstart" in window) ? 1 : 0;
    }

    /* Mobile detection - Touch desktop device safe! */
    function isMobile() {
        try{ document.createEvent("TouchEvent"); return true; }
        catch(e){ return false; }
    }

    function asp_submit_to_url(action, method, input) {
        'use strict';
        var form;
        form = $('<form />', {
            action: action,
            method: method,
            style: 'display: none;'
        });
        if (typeof input !== 'undefined' && input !== null) {
            $.each(input, function (name, value) {
                $('<input />', {
                    type: 'hidden',
                    name: name,
                    value: value
                }).appendTo(form);
            });
        }
        form.appendTo('body').submit();
    }

    function detectIE() {
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf('MSIE ');
        var trident = ua.indexOf('Trident/');

        if (msie > 0 || trident > 0)
            return true;

        // other browser
        return false;
    }

    // Object.create support test, and fallback for browsers without it
    if (typeof Object.create !== 'function') {
        Object.create = function (o) {
            function F() {
            }

            F.prototype = o;
            return new F();
        };
    }


    // Create a plugin based on a defined object
    $.plugin = function (name, object) {
        $.fn[name] = function (options) {
            return this.each(function () {
                if (!$.data(this, name)) {
                    $.data(this, name, Object.create(object).init(
                        options, this));
                }
            });
        };
    };

    $.plugin('ajaxsearchlite', methods);
})(jQuery);