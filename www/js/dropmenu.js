
(function($) {
$.fn.dropmenu = function(user_options) {

    var timer_id = null;
    var opened = null;

    var options = {        
        v_offset : 0,
        h_offset : 0,
        timeout : 2000,
        fade_speed : 300,
        corner_radius : 5
    };


    function build_dropmenu(container) {

        var list = $(container).children('ul.dropmenu');
        if (list.length == 0) return null;
        
        list.css({
        	visibility: 'hidden',
        	display: 'block'
        });
        
        max_link_width = 0;
        $(list).children('li').each(function(){        	
        	max_link_width = Math.max($(this).children('a').width(), max_link_width);
        });
        
        list.find('a').css({
        	width: Math.max(max_link_width, $(container).children('a').width())        	
        });
        
        list.find('li:last').addClass('last');
        
        list.remove();
        
        new_menu = $('<div />').addClass('_dropmenu').css({
        	display: 'block',
        	visibility: 'none'        	
        }).append(list);

        $(container).append(new_menu);
        
        background = $('<div />').addClass('background').css({
            position: 'absolute',
            width: list.outerWidth(),
            height: list.outerHeight(),
            zIndex: 0
        });

        new_menu.css({
        	visibility: 'visible',
        	display: 'none'
        }).prepend(background);

        list.css({
        	visibility: 'visible',
        	zIndex: 100,
        	position: 'absolute'
        });
        
        return new_menu;        
    }

    function show_dropmenu(container) {
        cancel_timer();

        var dropmenu = $(container).children('div._dropmenu');
        if (dropmenu.length==0) dropmenu = build_dropmenu(container);
        
        if (!dropmenu) {
            if (opened) hide_dropmenu(opened.parent('li'));
            return;
        }
        
        if (dropmenu.css('display') == 'block') return;

        if (opened) hide_dropmenu(opened.parent('li'));

        var c_height = $(container).find('a').outerHeight();
        var c_position = $(container).find('a').position();
        
        dropmenu.css({
            left: c_position.left + options.h_offset,
            top: c_position.top + c_height + options.v_offset
        });

        dropmenu.fadeIn(options.fade_speed);
        opened = dropmenu;
        $(container).addClass('highlighted');
    }

    function hide_dropmenu(container) {
        $(container).children('div._dropmenu').hide().fadeOut(options.fade_speed);
        opened = null; 
        $(container).removeClass('highlighted');
    }

    function cancel_timer() {
        if (timer_id != null) clearTimeout(timer_id);
        timer_id=null;
    }

    function shedule_hide(container) {
        cancel_timer();        
        timer_id = setTimeout(function(){hide_dropmenu(container)}, options.timeout);
    }


    function init_li(li) {
        $(li).mouseover(function(){
            show_dropmenu(this);
        }).mouseout(function(){
            shedule_hide(this);
        });
    }


    return this.each(function() {
        if (options) $.extend(options, user_options);

        $(this).children('li').each(function(){
            init_li(this);
        });
    });

}

})(jQuery);

