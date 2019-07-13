Perch.Apps.PerchGallery = function() {
	var init = function() {
		init_upload_form();
		init_image_list();
		init_album_list();
	};
	
	var init_upload_form = function() {
		var form = $('#imageupload:not(.basic)');
		if (form.length) {
			
			var albumID=$('#albumID').val();
			var success_url=$('#success').val();
			
			form.addClass('inner');

			form.pluploadQueue({
				runtimes : 'html5,flash,silverlight,html4',
				url : '../../upload.php',
				max_file_size : '10mb',
				chunk_size : '1mb',
				unique_names : false,
				filters : [
					{title : "Image files", extensions : "jpg,gif,png"},
					{title : "Zip files", extensions : "zip"}
				],
				multipart_params: {
					'albumID':albumID
				},
				flash_swf_url : '../../js/plupload.flash.swf',
				silverlight_xap_url : '../../js/plupload.silverlight.xap',
				
				init : {
					StateChanged: function(up) {
						if (up.state==plupload.STOPPED) {
							setTimeout(function(){window.location = success_url}, 5000);
						}
					}
				}
				
			});
			
			
		}
	};
	
	var init_image_list = function() {
		var list = $('ul.image-list');
		if (list.length) {
			list.sortable({
				update: function() {
					var order = [];
					list.find('li').each(function(i,o){
						order.push($(o).attr('data-id'));
					});
					$.post(Perch.path+'/addons/apps/perch_gallery/images/?id='+list.attr('data-albumid'), {'_perch_ajax':1, 'formaction':'sort', 'token':Perch.token, 'order':order.join(',')}, function(new_token){
						Perch.token=new_token;
						list.parents('form').find('input[name=token]').val(new_token);
					});
				}
			});
		}
	};

	var init_album_list = function() {
		var list = $('ol.album-list');
		if (list.length) {
			list.sortable({
	            stop: function(){
	                list.find('input').each(function(i,o) {
	                    $(o).val(i+1);
	                });
	            }
	        });
	        list.find('input').hide()
	    }
	}

	return {
		init: init
	};
}();