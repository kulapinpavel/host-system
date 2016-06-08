$(function() {
	console.log("hello, world!");
	$('.folder-view > ul').tree({
		expanded: 'li:first'
	});
	$('.folder-view a').on('click', function (e) {
		e.preventDefault();
		var obj = $(this);
		var modal = $('.bs-example-modal-lg');
		var url = 'view-file';

		if(obj.hasClass('file')) {
			console.log(obj.data("file"));
			$.ajax({
		        type: "POST",
		        data: {
		        	'file':obj.data("file")
		        },
		        processData: true,
		        url: url,
		        dataType: "html",
		        success: function (response) {
		        	modal.find(".modal-body").html(response);
		            modal.modal();
		        },
		        beforeSend: function () {
		        },
		        complete: function () {
		        }
		    });
		}
	});
	$('.host-list__switcher').on('click', function (e) {
		e.preventDefault();
		var obj = $(this);
		var list = obj.closest('.host').find('.host-list');

		if(list.is(':visible')) {
			list.stop().slideUp(500);
			obj.text = "&#9650;";
		}
		else {
			list.stop().slideDown(500);
			obj.text = "&#9660;";
		}
	});
	$('.directory-switcher').on('change',function() {
		var obj = $(this);
		var input = obj.closest(".input-group").find('input[name="Hosts[home_dir]"]');

		if(input.prop('readonly') == true) {
			input.prop("readonly", false);
		}
		else {
			input.prop("readonly", true);
		}
	});
	$('form.host-create').on('input','input[name="Hosts[name]"]',function() {
		var obj = $(this);
		var input = obj.closest('form.host-create').find('input[name="Hosts[home_dir]"]');

		if(input.prop('readonly') == true) {
			input.val(input.data('homedir') + "/" + obj.val());
		}
	});
});