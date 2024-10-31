/*notifier-and-ip-blocker*/
jQuery(function($){
	var hash = window.location.hash;
	if(hash == '') hash = $('.nav-tab-wrapper a:first').attr('href');
    if(hash != ''){
        $('.nav-tab-wrapper').children().removeClass('nav-tab-active');
        $('.nav-tab-wrapper a[href="'+hash+'"]').addClass('nav-tab-active');
        $('.tabs-content').children().addClass('hidden');
        $('.tabs-content div'+hash).removeClass('hidden');
    }

    $('.nav-tab-wrapper a').click(function(){    	
        $(this).parent().children().removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.tabs-content').children().addClass('hidden');
        $('.tabs-content div'+$(this).attr('href')).removeClass('hidden');        
    });
    
    $('a.actionip').click(function(){
    	var el = $(this);
    	var r = confirm(el.attr('data-confirm')+' '+el.attr('data-ip')+'?');
		if(r == true){
		    $.post(
		    	naipb_ajax_object.ajax_url,
		    	{
		    		method: el.attr('data-method'),
		    		ip: el.attr('data-ip'),
		    		auto: el.attr('data-auto'),
		    		security: naipb_ajax_object.security,
		    	},
		    	function(response){
					if(response == 1) el.parents('tr').remove();
				}
	    	);
		}    	
    	return false;
    });
});