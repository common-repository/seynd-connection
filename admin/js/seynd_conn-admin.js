jQuery(document).ready(function () {
    var Nodialog, seyndDialog;
    var PATH = server.seynd_app_path + "/api/redirect-me?action=seynd_free_account&redirect_path=";

    seyndDialog = jQuery("#seyndConnModal").dialog({
	autoOpen: false,
	height: 'auto',
	width: 500,
	closeText: '',
	modal: true,
	fluid: true,
	buttons: {
	    Cancel: {class: 'seynd_cancel', text: 'Cancel', click: function () {
		    seyndDialog.dialog("close");
		}
	    }
	},
	close: function () {
	    jQuery('#seyndConnectionform').show();
	    jQuery('#setSeyndSite').hide();
	}
    });

    Nodialog = jQuery("#seyndConnNoAccountModal").dialog({
	autoOpen: false,
	height: 'auto',
	width: 500,
	closeText: '',
	modal: true,
	fluid: true,
	buttons: {
	    Cancel: {class: 'seynd_cancel', text: 'Cancel', click: function () {
		    Nodialog.dialog("close");
		}
	    }
	},
	close: function () {
	    Nodialog.dialog("close");
	}
    });

    jQuery(document).on("click", "#seynd-change-site", function (event) {
	event.preventDefault();
	jQuery('.seynd-error-msg').empty(); //remove all messages
	seyndDialog.dialog('open');
	jQuery("#seyndConnModal").dialog();
    });

    jQuery(document).on("click", "#seynd-connect", function (event) {
	event.preventDefault();
	jQuery('#seyndEmptyDropdown').empty();
	jQuery('.seynd-error-msg').empty(); //remove all messages
	seyndDialog.dialog('open');
	jQuery("#seyndConnModal").dialog();
    });

    jQuery(document).on("click", "#no-seynd", function (event) {
	event.preventDefault();
	jQuery('.seynd-error-msg').empty(); //remove all messages
	Nodialog.dialog('open');
	jQuery("#seyndConnNoAccountModal").dialog();
    });

    jQuery(document).on("click", "#yes-seynd", function (event) {
	event.preventDefault();
	jQuery('#seyndEmptyDropdown').empty();
	jQuery('.seynd-error-msg').empty(); //remove all messages
	jQuery('.div-connect-option').show();
	jQuery('.div-confirmation').hide();
    });

    jQuery(document).on("click", "#btn-no-seynd", function (event) {
	event.preventDefault();
	var url = window.location.href;
	window.open(PATH + url, "_blank");
    });

    jQuery(document).on("click", "#seynd-connected-sites", function (event) {
	jQuery('.seynd-connected-error-msg').empty();
	jQuery('.seynd-loader-connected-sites').show();
	jQuery('.btn-seyndajax').css('cursor', 'not-allowed');
	var page_type = jQuery("#seynd-connected-websitelist input[name='optpage']:checked").val();

	var selectedPageValues = [];
	if (page_type == 'selected_page') {
	    jQuery('#seynd-connected-websitelist input[name="sel_page"]:checked').each(function () {
		selectedPageValues.push(this.value);
	    });
	    if (selectedPageValues.length === 0) {
		jQuery('.seynd-connected-error-msg').html("Please select pages.");
		jQuery('.seynd-loader-connected-sites').hide();
		jQuery('#seynd-connected-sites').css('cursor', 'pointer');
		return false;
	    }
	}

	var data = {
	    'action': 'edit_code',
	    'page_type': page_type,
	    'selected_page': selectedPageValues
	};
	
	jQuery.post(server.ajax_url, data, function (data) {	    
	    var response = JSON.parse(data);
	    jQuery('.seynd-loader-connected-sites').hide();
	    if (response.status == 1) {
		seyndDialog.dialog('close');				
		alert(response.message);
		location.reload(true);
	    } else {		
		jQuery('.btn-seyndajax').css('cursor', 'pointer');
		alert(response.message);
	    }
	});
    });

    jQuery(document).on("click", "#btnSetSites", function (event) {
	jQuery('.seynd-error-msg').empty();
	jQuery('#seyndEmptyDropdown').val();
	jQuery('.seynd-loader').show();
	jQuery('.btn-seyndajax').css('cursor', 'not-allowed');
	var website_alias = jQuery('#seyndEmptyDropdown').find(":selected").text();
	var subdomain = jQuery('#seyndEmptyDropdown').find(":selected").val();
	var URLdomain = jQuery('#seyndEmptyDropdown').find(":selected").attr("data-url");
	var page_type = jQuery("input[name='optpage']:checked").val();
	var seynd_token = jQuery("#seynd-websitelist #seynd-token").val();
	var sites_list = jQuery("#seynd-websitelist #seynd-sites-list").val();

	var sites = [];
	if (sites_list != '') {
	    sites_list = sites_list.replace(/\\/g, '');
	    sites = JSON.parse(sites_list);
	}
	var selectedPageValues = [];
	if (page_type == 'selected_page') {
	    jQuery('input[name="sel_page"]:checked').each(function () {
		selectedPageValues.push(this.value);
	    });
	    if (selectedPageValues.length === 0) {
		jQuery('.seynd-error-msg').html("Please select pages.");
		jQuery('.seynd-loader').hide();
		jQuery('.btn-seyndajax').css('cursor', 'pointer');
		return false;
	    }
	}

	var data = {
	    'action': 'add_code',
	    'website_alias': website_alias,
	    'subdomain': subdomain,
	    'URLdomain': URLdomain,
	    'page_type': page_type,
	    'selected_page': selectedPageValues,
	    'seynd_token': seynd_token,
	    'sites_list': sites

	};

	// since 2.8 server.ajax_url is always defined in the admin header and points to admin-ajax.php
	jQuery.post(server.ajax_url, data, function (data) {

	    var response = JSON.parse(data);
	    if (response.status == 1) {
		seyndDialog.dialog('close');
		jQuery('.seynd-loader').hide();
		jQuery('.seynd-error-msg').append(response.message);
		//alert('Connect successfully');
		location.reload(true);
	    } else {
		jQuery('.seynd-loader').hide();
		jQuery('.btn-seyndajax').css('cursor', 'pointer');
		alert(response.message);
	    }
	});
    });

    jQuery(document).on("click", "#seynd-btn-disconnect", function (event) {
	var r = confirm("Are you sure?");
	if (r == true) {
	    jQuery('#seynd-btn-disconnect').css('cursor', 'not-allowed');
	    var data = {
		'action': 'seynd_disconnect',
	    };

	    jQuery('.seynd-loader').show();
	    jQuery.post(server.ajax_url, data, function (data) {
		var response = JSON.parse(data);
		if (response.status == 1) {
		    jQuery('.seynd-loader').hide();
		    alert(response.message);
		    location.reload(true);
		} else {
		    alert('Something wrong. Try again later.');
		    jQuery('.seynd-loader').hide();
		    jQuery('#seynd-btn-disconnect').css('cursor', 'pointer');
		}
	    });
	}
    });

    jQuery('#seynd-websitelist input[type=radio][name=optpage]').change(function () {
	if (this.value == 'selected_page') {
	    jQuery(".select_page_list").show();
	} else {
	    jQuery(".select_page_list").hide();
	}
    });

    //Get all pages	
    var data = {
	'action': 'getpages'
    };
    data = jQuery.param(data);
    jQuery.get(server.ajax_url, data, function (datpage) {
	var datpage = JSON.parse(datpage);
	if (datpage.status == 1) {
	    jQuery('#seyndConnectedEmptyPageDropdown').empty();
	    var staticfront = datpage.staticfront;
	    if (datpage.pages) {
		var pages = datpage.pages;
		jQuery.each(pages, function (index, el) {
		    var title = el.post_title;
		    if (staticfront == el.ID) {
			title += " <b>(Home)</b>";
		    }

		    var checked_html = '';
		    if (jQuery.inArray((el.ID).toString(), datpage.seynd_selected_page_array) != -1) {
			checked_html = 'checked';
		    }

		    jQuery('#seyndConnectedEmptyPageDropdown').append('<label><input type="checkbox" name="sel_page" value="' + el.ID + '" ' + checked_html + '> ' + title + '</label>');
		});
	    }
	}
    });

    jQuery('#seynd-connected-websitelist input[type=radio][name=optpage]').change(function () {
	if (this.value == 'selected_page') {
	    jQuery(".seynd-connected-select_page_list").show();
	} else {
	    jQuery(".seynd-connected-select_page_list").hide();
	}
    });
});


jQuery(document).on('submit', 'form#seyndConnectionform', function (event) {
    // code
    event.preventDefault();
    jQuery('.seynd-error-msg').empty();
    jQuery('.seynd-loader').show();
    jQuery('.btn-seyndajax').css('cursor', 'not-allowed');
    var website_url = window.location.origin;
    var username = jQuery("#seyndConnectionform input[name=username]").val();
    var password = jQuery("#seyndConnectionform input[name=password]").val();
    var seynd_url = server.seynd_app_path + "/api/authorize";

    jQuery.post(seynd_url, "website_url="+ website_url +"&username=" + username + "&password=" + password, function (dat) {
	if (dat.status == 1) {

	    //Get all pages	
	    var data = {
		'action': 'getpages'
	    };
	    data = jQuery.param(data);
	    jQuery.get(server.ajax_url, data, function (datpage) {
		var datpage = JSON.parse(datpage);
		if (datpage.status == 1) {
		    jQuery('#seyndEmptyPageDropdown').empty();
		    var staticfront = datpage.staticfront;
		    if (datpage.pages) {
			var pages = datpage.pages;
			jQuery.each(pages, function (index, el) {
			    var title = (el.post_title).replace(/\\/g, '');
			    if (staticfront == el.ID) {
				title += " <b>(Home)</b>";
			    }
			    jQuery('#seyndEmptyPageDropdown').append('<label><input type="checkbox" name="sel_page" value="' + el.ID + '"> ' + title + '</label>');
			});
		    }
		}
	    });

	    jQuery("#seyndConnectionform input[name=username]").val('');
	    jQuery("#seyndConnectionform input[name=password]").val('');
	    jQuery('#seynd-websitelist').get(0).reset();
	    jQuery('#seyndEmptyDropdown').empty();

	    if (dat.sites) {
		var sites = JSON.parse(dat.sites);
		console.log(sites);
		jQuery.each(sites, function (index, el) {
		    if (el.sitedomain) {
			jQuery('#seyndEmptyDropdown').append('<option value="' + el.sitedomain.name + '" data-url="' + el.url + '">' + el.name + '</option>');
		    }
		});
		jQuery("#seynd-websitelist #seynd-token").val(dat.token);
		jQuery("#seynd-websitelist #seynd-sites-list").val(dat.sites);
		jQuery('#seyndConnectionform').hide();
		jQuery('#setSeyndSite').show();
		jQuery('.seynd-loader').hide();
		jQuery('.btn-seyndajax').css('cursor', 'pointer');
		if(dat.site_is_added==0){
			jQuery('.seynd-error-msg').html('( '+website_url +' ) website is not added.Please add this website in seynd account.');
		}
	    }
	} else {
	    jQuery('.seynd-error-msg').html(dat.message);
	    jQuery('.seynd-loader').hide();
	    jQuery('.btn-seyndajax').css('cursor', 'pointer');
	}

    });
});



jQuery(document).on('submit', 'form#seyndUpdateform', function (event) {
    event.preventDefault();
    jQuery('#seyndEmptyDropdown').val();
    jQuery('.seynd-loader').show();
    jQuery('.btn-seyndajax').css('cursor', 'not-allowed');
    var website_alias = jQuery('#seyndUpdateSitesDropdown').find(":selected").text();
    var subdomain = jQuery('#seyndUpdateSitesDropdown').find(":selected").val();
    var URLdomain = jQuery('#seyndUpdateSitesDropdown').find(":selected").attr("data-url");

    var data = {
	'action': 'update_site',
	'website_alias': website_alias,
	'URLdomain': URLdomain,
	'subdomain': subdomain
    };

    jQuery.post(server.ajax_url, data, function (data) {

	var response = JSON.parse(data);
	if (response.status == 1) {	    
	    jQuery('.seynd-loader').hide();
	    alert(response.message);
	    location.reload(true);
	}
	else{
	    alert(response.message);
	}
    });
});

jQuery(document).on("click", "#seynd_btnRefresh_site", function (event) {
    // code
    event.preventDefault();
    jQuery('.seynd-error-msg').empty();
    jQuery("#seynd_btnRefresh_site").html('Sycing...');
    jQuery('#seynd_btnRefresh_site').css('cursor', 'not-allowed');
    var website_url = window.location.origin;
    var token = jQuery("#seyndUpdateform input[name=seynd_token]").val();
    var seynd_url = server.seynd_app_path + "/api/authorize";
    jQuery.post(seynd_url,"website_url=" +website_url+ "&token=" + token, function (dat) {
	if (dat.status == 1) {
	    jQuery('#seyndUpdateSitesDropdown').empty();
	    if (dat.sites) {
		var sites_list = (dat.sites).replace(/\\/g, '');
		var sites = JSON.parse(sites_list);
		jQuery.each(sites, function (index, el) {
		    if (el.sitedomain) {
			var name = (el.name).replace(/\\/g, '');
			jQuery('#seyndUpdateSitesDropdown').append('<option value="' + el.sitedomain.name + '" data-url="' + el.url + '">' + name + '</option>');
		    }
		});

		var data = {
		    'action': 'update_site_list',
		    'sites_list': sites
		};
		jQuery.post(server.ajax_url, data, function (data) {
		    var response = JSON.parse(data);		    
		    jQuery("#seynd_btnRefresh_site").html('Syc Site');
		    jQuery('#seynd_btnRefresh_site').css('cursor', 'pointer');
		    if(response.status == 0){
			alert(response.message);
		    }
		});
	    }
	    if(dat.site_is_added==0){
			jQuery('.seynd-error-msg').html('( '+website_url +' ) website is not added.Please add this website in seynd account.');
		}
	} else {
	    jQuery('.seynd-error-msg').html(dat.message);
	    jQuery('.seynd-loader').hide();
	    jQuery('.btn-seyndajax').css('cursor', 'pointer');
	}

    });
});