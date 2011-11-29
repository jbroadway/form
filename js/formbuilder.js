/**
 * This is the form model which contains the front-end logic
 * for the /form/edit form builder. It's populated in the view
 * and connected to the UI elements via knockout's data binding.
 */
var form = {
	/**
	 * This is the data structure of the form itself.
	 */
	data: {},

	/**
	 * Initialize the form data and apply the bindings.
	 */
	init: function (data) {
		form.data = data;

		/**
		 * Define an observable for the email action.
		 */
		form.actions_email = ko.dependentObservable ({
			read: function () {
				for (var i = 0; i < form.data.actions.length; i++) {
					if (form.data.actions[i].type == 'email') {
						return form.data.actions[i].to;
					}
				}
				return '';
			},
			write: function (value) {
				for (var i = 0; i < form.data.actions.length; i++) {
					if (form.data.actions[i].type == 'email') {
						if (value == '') {
							form.data.actions.splice (i, 1);
						} else {
							form.data.actions[i].to = value;
						}
						return;
					}
				}
				form.data.actions.push ({type: 'email', to: value});
			},
			owner: form
		});

		/**
		 * Define an observable for the redirect action.
		 */
		form.actions_redirect = ko.dependentObservable ({
			read: function () {
				for (var i = 0; i < form.data.actions.length; i++) {
					if (form.data.actions[i].type == 'redirect') {
						return form.data.actions[i].url;
					}
				}
				return '';
			},
			write: function (value) {
				for (var i = 0; i < form.data.actions.length; i++) {
					if (form.data.actions[i].type == 'redirect') {
						if (value == '') {
							form.data.actions.splice (i, 1);
						} else {
							form.data.actions[i].url = value;
						}
						return;
					}
				}
				form.data.actions.push ({type: 'redirect', url: value});
			},
			owner: form
		});

		// Bind the form model to the view elements.
		ko.applyBindings (form);
	},

	/**
	 * Save the core form fields on blur of main fields.
	 */
	update_form: function () {
		var data = {
			title: form.data.title,
			message: form.data.message,
			response_title: form.data.response_title,
			response_body: form.data.response_body
		};

		form.show_saving ();
		$.post ('/form/api/update/' + form.data.id, data, function (res) {
			if (res.success) {
				form.done_saving ();
				return;
			}
			$.add_notification (res.error);
			form.done_saving ();
		});
	},

	/**
	 * Save the actions to the server.
	 */
	update_actions: function () {
		form.show_saving ();
		$.post ('/form/api/actions/' + form.data.id, {actions: form.data.actions}, function (res) {
			if (res.success) {
				form.done_saving ();
				return;
			}
			$.add_notification (res.error);
			form.done_saving ();
		});
	},

	/**
	 * Delete a field from the form.
	 */
	delete_field: function () {
		return false;
	},

	/**
	 * Add a text field to the form.
	 */
	add_text: function () {
		return false;
	},

	/**
	 * Add a textarea field to the form.
	 */
	add_textarea: function () {
		return false;
	},

	/**
	 * Add a select field to the form.
	 */
	add_select: function () {
		return false;
	},

	/**
	 * Add a checkbox field to the form.
	 */
	add_checkbox: function () {
		return false;
	},

	/**
	 * Add a radio field to the form.
	 */
	add_radio: function () {
		return false;
	},

	/**
	 * Add a date field to the form.
	 */
	add_date: function () {
		return false;
	},

	/**
	 * Add a slider field to the form.
	 */
	add_slider: function () {
		return false;
	},

	/**
	 * Disable "Done Editing" link and show "Saving..." message.
	 */
	show_saving: function () {
		$('#saving').fadeIn ('slow');
		$('#done-editing').addClass ('disabled').on ('click', function (e) {
			e.preventDefault ();
			if ($(this).hasClass ('disabled')) {
				return false;
			}
			window.location.href = $(this).attr ('href');
		});
	},

	/**
	 * Re-enable "Done Editing" link and hide "Saving..." message.
	 */
	done_saving: function () {
		$('#done-editing').removeClass ('disabled');
		$('#saving').fadeOut ('slow');
	},

	/**
	 * Show the preview tab.
	 */
	show_preview: function () {
		$('#toggle-full').removeClass ('active');
		$('#toggle-list').removeClass ('active');
		$('#toggle-preview').addClass ('active');
		$('#field-list-full').hide ();
		$('#field-list-list').hide ();
		$('#field-list-preview').show ();

		$('#field-list-preview').html ('Loading...');

		// Load preview of form
		$.get ('/form/preview/' + form.data.id, function (res) {
			$('#field-list-preview').html (res);
			$(":range").rangeinput({progress:true});
			$(":date").dateinput({format:"yyyy-mm-dd"});
		});

		return false;
	},

	/**
	 * Show the list tab.
	 */
	show_list: function () {
		$('#toggle-full').removeClass ('active');
		$('#toggle-list').addClass ('active');
		$('#toggle-preview').removeClass ('active');
		$('#field-list-full').hide ();
		$('#field-list-list').show ();
		$('#field-list-preview').hide ();
		return false;
	},

	/**
	 * Show the full tab.
	 */
	show_full: function () {
		$('#toggle-full').addClass ('active');
		$('#toggle-list').removeClass ('active');
		$('#toggle-preview').removeClass ('active');
		$('#field-list-full').show ();
		$('#field-list-list').hide ();
		$('#field-list-preview').hide ();
		return false;
	}
};
