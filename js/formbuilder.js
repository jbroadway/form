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
	 * Whether the form has finished being initialized.
	 * We won't call any save functions until the initialization
	 * has been completed, so we're not issuing false saves
	 * on first load.
	 */
	initialized: false,

	/**
	 * Whether the fields are currently being updated. Prevents
	 * calling update_actions() incorrectly.
	 */
	updating_fields: false,

	/**
	 * Initialize the form data and apply the bindings.
	 */
	init: function (data) {
		form.data = data;

		form.data.fields = form.make_fields_observable (form.data.fields);

		/**
		 * Custom binding for sorting fields in list view.
		 */
		ko.bindingHandlers.sortableList = {
			init: function (element, valueAccessor) {
				var list = valueAccessor ();
				$(element).sortable ({
					update: function (event, ui) {
						// get the data item
						var item = ui.item.tmplItem ().data;

						// figure out its new position
						var position = ko.utils.arrayIndexOf (ui.item.parent ().children (), ui.item[0]);

						// start updating fields list
						form.updating_fields = true;

						// remove the item and add it back in the right spot
						if (position >= 0) {
							list.remove (item);
							list.splice (position, 0, item);
						}

						// done updating fields
						form.updating_fields = false;

						// save changes to the server
						form.update_fields ();
					}
				});
			}
		};

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

		/**
		 * Define an observable for the cc_recipient name_field.
		 */
		form.actions_cc_name_field = ko.dependentObservable ({
			read: function () {
				return form.read_cc_recipient ('name_field');
			},
			write: function (value) {
				if (typeof value == 'undefined') {
					return;
				}
				return form.update_cc_recipient ('name_field', value);
			},
			owner: form
		});

		/**
		 * Define an observable for the cc_recipient email_field.
		 */
		form.actions_cc_email_field = ko.dependentObservable ({
			read: function () {
				return form.read_cc_recipient ('email_field');
			},
			write: function (value) {
				if (typeof value == 'undefined') {
					return;
				}
				return form.update_cc_recipient ('email_field', value);
			},
			owner: form
		});

		/**
		 * Define an observable for the cc_recipient reply_from field.
		 */
		form.actions_cc_reply_from = ko.dependentObservable ({
			read: function () {
				return form.read_cc_recipient ('reply_from');
			},
			write: function (value) {
				return form.update_cc_recipient ('reply_from', value);
			},
			owner: form
		});

		/**
		 * Define an observable for the cc_recipient subject field.
		 */
		form.actions_cc_subject = ko.dependentObservable ({
			read: function () {
				return form.read_cc_recipient ('subject');
			},
			write: function (value) {
				return form.update_cc_recipient ('subject', value);
			},
			owner: form
		});

		/**
		 * Define an observable for the cc_recipient body_intro field.
		 */
		form.actions_cc_body_intro = ko.dependentObservable ({
			read: function () {
				return form.read_cc_recipient ('body_intro');
			},
			write: function (value) {
				return form.update_cc_recipient ('body_intro', value);
			},
			owner: form
		});

		/**
		 * Define an observable for the cc_recipient body_sig field.
		 */
		form.actions_cc_body_sig = ko.dependentObservable ({
			read: function () {
				return form.read_cc_recipient ('body_sig');
			},
			write: function (value) {
				return form.update_cc_recipient ('body_sig', value);
			},
			owner: form
		});

		// Set the initial include_data checked status.
		// Doing this field the backwards way because of issues
		// with checked handling in Knockout.
		for (var i = 0; i < form.data.actions.length; i++) {
			if (form.data.actions[i].type == 'cc_recipient' && form.data.actions[i].hasOwnProperty ('include_data')) {
				if (form.data.actions[i].include_data == 'yes') {
					$('#include-data').attr ('checked', true);
				} else {
					$('#include-data').attr ('checked', false);
				}
				break;
			}
		}

		// Bind the form model to the view elements.
		ko.applyBindings (form);

		// Now initialize
		form.initialized = true;
	},

	/**
	 * Turn the fields into an observableArray of items whose properties are also observables.
	 */
	make_fields_observable: function (fields) {
		var list = ko.observableArray ([]);
		for (var i = 0; i < fields.length; i++) {
			var field = {
				type: fields[i].type,
				id: fields[i].id,
				label: ko.observable (fields[i].label),
				default_value: ko.observable (fields[i].default_value),
				rules: ko.observable (form.transform_rules (fields[i].rules)),
				message: ko.observable (fields[i].message)
			};
			field.rules.subscribe (function (value) {
				form.update_fields ();
			});
			if (fields[i].hasOwnProperty ('placeholder')) {
				field.placeholder = ko.observable (fields[i].placeholder);
			}
			if (fields[i].hasOwnProperty ('size')) {
				field.size = ko.observable (fields[i].size);
			}
			if (fields[i].hasOwnProperty ('cols')) {
				field.cols = ko.observable (fields[i].cols);
			}
			if (fields[i].hasOwnProperty ('rows')) {
				field.rows = ko.observable (fields[i].rows);
			}
			if (fields[i].hasOwnProperty ('size')) {
				field.size = ko.observable (fields[i].size);
			}
			if (fields[i].hasOwnProperty ('min')) {
				field.min = ko.observable (fields[i].min);
			}
			if (fields[i].hasOwnProperty ('max')) {
				field.max = ko.observable (fields[i].max);
			}
			if (fields[i].hasOwnProperty ('values')) {
				field.values = ko.observable (fields[i].values.join ("\n"));
			}
			list.push (field);
		}
		return list;
	},

	/**
	 * Define an observable for the cc_recipient include_data.
	 */
	actions_cc_include_data: function (el) {
		for (var i = 0; i < form.data.actions.length; i++) {
			if (form.data.actions[i].type == 'cc_recipient') {
				form.data.actions[i].include_data = $(el).is (':checked') ? 'yes' : 'no';
				form.update_actions ();
				return;
			}
		}
	},

	/**
	 * Read the selected field from cc_recipient action.
	 */
	read_cc_recipient: function (name) {
		for (var i = 0; i < form.data.actions.length; i++) {
			if (form.data.actions[i].type == 'cc_recipient' && form.data.actions[i].hasOwnProperty (name)) {
				return form.data.actions[i][name];
			}
		}
		return '';
	},

	/**
	 * Update the cc_recipient action.
	 */
	update_cc_recipient: function (name, value) {
		for (var i = 0; i < form.data.actions.length; i++) {
			if (form.data.actions[i].type == 'cc_recipient') {
				form.data.actions[i][name] = value;
				return;
			}
		}
		// Create a new cc_recipient action since one was not found.
		form.data.actions.push ({
			type: 'cc_recipient',
			name_field: form.read_cc_recipient ('name_field'),
			email_field: form.read_cc_recipient ('email_field'),
			reply_from: form.read_cc_recipient ('reply_from'),
			subject: form.read_cc_recipient ('subject'),
			body_intro: form.read_cc_recipient ('body_intro'),
			body_sig: form.read_cc_recipient ('body_sig'),
			include_data: 'no'
		});
	},

	/**
	 * Clear the cc_recipient action.
	 */
	clear_cc_recipient: function () {
		for (var i = 0; i < form.data.actions.length; i++) {
			if (form.data.actions[i].type == 'cc_recipient') {
				form.data.actions.splice (i, 1);
				return false;
			}
		}
		return false;
	},

	/**
	 * Transform rules from stored format into the format used
	 * in the form builder UI.
	 */
	transform_rules: function (rules) {
		for (var i in rules) {
			if (i === 'not empty') {
				return 'yes';
			} else if (i === 'email') {
				return 'email';
			} else if (i === 'url') {
				return 'url';
			} else if (i === 'type') {
				return 'numeric';
			} else if (i === 'regex') {
				if (rules[i] === '/^[a-zA-Z]+$/') {
					return 'alpha';
				}
				return 'alphanumeric';
			}
			// no rules
			return false;
		}
	},

	/**
	 * Save the core form fields on blur of main fields.
	 */
	update_form: function () {
		if (! form.initialized) {
			return;
		}

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
	 * Save changes to the fields to the server.
	 */
	update_fields: function () {
		if (! form.initialized) {
			return;
		}

		form.show_saving ();
		$.post ('/form/api/fields/' + form.data.id, {fields: form.data.fields ()}, function (res) {
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
		if (! form.initialized) {
			return;
		}

		if (form.updating_fields) {
			return;
		}

		form.show_saving ();
		$.post ('/form/api/actions/' + form.data.id, {actions: form.data.actions}, function (res) {
			if (res.success) {
				form.done_saving ();
				return;
			}
			$.add_notification (res.error);
			form.done_saving ();
		});
		return true;
	},

	/**
	 * Delete a field from the form.
	 */
	delete_field: function (item) {
		if (confirm ('Are you sure you want to remove this field?')) {
			form.data.fields.remove (item);
			return form.update_fields ();
		}
		return false;
	},

	/**
	 * Put the last field into focus.
	 */
	focus_last_field: function () {
		// make sure the full field list is showing
		form.show_full ();

		// get the last element in the field list
		var last_section = $('#field-list-full .section:last');

		// scroll to the last element
		$('html, body').animate ({
			scrollTop: last_section.offset ().top
		}, 500);

		// put first input into focus
		last_section.find ('input:first').focus ();
	},

	/**
	 * Add a text field to the form.
	 */
	add_text_field: function () {
		var f = {
			type: 'text',
			id: '',
			label: ko.observable (''),
			default_value: ko.observable (''),
			placeholder: ko.observable (''),
			size: ko.observable ('30'),
			rules: ko.observable (''),
			message: ko.observable ('')
		};
		form.data.fields.push (f);
		form.focus_last_field ();
		return false;
	},

	/**
	 * Add a textarea field to the form.
	 */
	add_textarea_field: function () {
		var f = {
			type: 'textarea',
			id: '',
			label: ko.observable (''),
			default_value: ko.observable (''),
			placeholder: ko.observable (''),
			cols: ko.observable ('50'),
			rows: ko.observable ('4'),
			rules: ko.observable (''),
			message: ko.observable ('')
		};
		f.rules.subscribe (function () {
			form.update_fields ();
		});
		form.data.fields.push (f);
		form.focus_last_field ();
		return false;
	},

	/**
	 * Add a select field to the form.
	 */
	add_select_field: function () {
		var f = {
			type: 'select',
			id: '',
			label: ko.observable (''),
			default_value: ko.observable (''),
			values: ko.observable (''),
			rules: ko.observable (''),
			message: ko.observable ('')
		};
		f.rules.subscribe (function () {
			form.update_fields ();
		});
		form.data.fields.push (f);
		form.focus_last_field ();
		return false;
	},

	/**
	 * Add a checkbox field to the form.
	 */
	add_checkbox_field: function () {
		var f = {
			type: 'checkbox',
			id: '',
			label: ko.observable (''),
			default_value: ko.observable (''),
			values: ko.observable (''),
			rules: ko.observable (''),
			message: ko.observable ('')
		};
		f.rules.subscribe (function () {
			form.update_fields ();
		});
		form.data.fields.push (f);
		form.focus_last_field ();
		return false;
	},

	/**
	 * Add a radio field to the form.
	 */
	add_radio_field: function () {
		f = {
			type: 'radio',
			id: '',
			label: ko.observable (''),
			default_value: ko.observable (''),
			values: ko.observable (''),
			rules: ko.observable (''),
			message: ko.observable ('')
		};
		f.rules.subscribe (function () {
			form.update_fields ();
		});
		form.data.fields.push (f);
		form.focus_last_field ();
		return false;
	},

	/**
	 * Add a date field to the form.
	 */
	add_date_field: function () {
		var f = {
			type: 'date',
			id: '',
			label: ko.observable (''),
			default_value: ko.observable ('today'),
			rules: ko.observable (''),
			message: ko.observable ('')
		};
		f.rules.subscribe (function () {
			form.update_fields ();
		});
		form.data.fields.push (f);
		form.focus_last_field ();
		return false;
	},

	/**
	 * Add a range field to the form.
	 */
	add_range_field: function () {
		var f = {
			type: 'range',
			id: '',
			label: ko.observable (''),
			default_value: ko.observable ('5'),
			min: ko.observable ('0'),
			max: ko.observable ('10'),
			rules: ko.observable (''),
			message: ko.observable ('')
		};
		f.rules.subscribe (function () {
			form.update_fields ();
		});
		form.data.fields.push (f);
		form.focus_last_field ();
		return false;
	},

	/**
	 * Determine template to use to render a given field type.
	 */
	determine_template: function (field) {
		return 'field-' + field.type;
	},

	/**
	 * Set the current drag item.
	 */
	select_field: function (field) {
		form.selected_field (field);
	},

	/**
	 * The current drag item.
	 */
	selected_field: ko.observable (),

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
