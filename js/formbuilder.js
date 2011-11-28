/**
 * Front-end logic for /form/edit form builder.
 */

$(function () {
	/**
	 * Initialize the properties/fields/actions tabs.
	 */
	$('#tabs').tabs ();

	/**
	 * Initialize the full/list/preview buttons.
	 */
	$('#toggle-full').on ('click', function () {
		$('#toggle-full').addClass ('active');
		$('#toggle-list').removeClass ('active');
		$('#toggle-preview').removeClass ('active');
		$('#field-list-full').show ();
		$('#field-list-list').hide ();
		$('#field-list-preview').hide ();
		return false;
	});
	$('#toggle-list').on ('click', function () {
		$('#toggle-full').removeClass ('active');
		$('#toggle-list').addClass ('active');
		$('#toggle-preview').removeClass ('active');
		$('#field-list-full').hide ();
		$('#field-list-list').show ();
		$('#field-list-preview').hide ();
		return false;
	});
	$('#toggle-preview').on ('click', function () {
		$('#toggle-full').removeClass ('active');
		$('#toggle-list').removeClass ('active');
		$('#toggle-preview').addClass ('active');
		$('#field-list-full').hide ();
		$('#field-list-list').hide ();
		$('#field-list-preview').show ();

		$('#field-list-preview').html ('Loading...');

		// Load preview of form
		$.get ('/form/preview/' + form_id, function (res) {
			$('#field-list-preview').html (res);
			$(":range").rangeinput({progress:true});
			$(":date").dateinput({format:"yyyy-mm-dd"});
		});

		return false;
	});

	/*
	 * Disable "Done Editing" link and show "Saving..." message.
	 */
	function show_saving () {
		$('#saving').fadeIn ('slow');
		$('#done-editing').addClass ('disabled').on ('click', function (e) {
			e.preventDefault ();
			if ($(this).hasClass ('disabled')) {
				return false;
			}
			window.location.href = $(this).attr ('href');
		});
	}

	/*
	 * Re-enable "Done Editing" link and hide "Saving..." message.
	 */
	function done_saving () {
		$('#done-editing').removeClass ('disabled');
		$('#saving').fadeOut ('slow');
	}

	/**
	 * Save form basics on blur of main fields.
	 */
	$('#form-title, #form-message, #form-response-title, #form-response-body').on ('blur', function () {
		var data = {
			title: $('#form-title').val (),
			message: $('#form-message').val (),
			response_title: $('#form-response-title').val (),
			response_body: $('#form-response-body').val ()
		};

		show_saving ();
		$.post ('/form/api/update/' + form_id, data, function (res) {
			if (res.success) {
				done_saving ();
				return;
			}
			$.add_notification (res.error);
			done_saving ();
		});
	});

	/**
	 * Field model.
	 */
	window.Field = Backbone.Model.extend ({
		defaults: function () {
			return {
				id: 'field',
				label: 'New field'
			};
		}
	});

	/**
	 * Action model.
	 */
	window.Action = Backbone.Model.extend ({
		defaults: function () {
			return {
				type: 'email'
			};
		}
	});

	/**
	 * Collection of fields.
	 */
	window.FieldList = Backbone.Collection.extend ({
		model: Field,
		url: '/form/api/field'
	});

	/**
	 * Collection of actions.
	 */
	window.ActionList = Backbone.Collection.extend ({
		model: Action,
		url: '/form/api/action'
	});

	Backbone.emulateHTTP = true;

	/**
	 * Define our views.
	 */
	window.FormView = Backbone.View.extend ({
	});

	/**
	 * The application.
	 */
	window.AppView = Backbone.View.extend ({
		el: $('#properties'),
		
		events: {
			'click a.add-field': 'addField',
			'click .field-delete a': 'deleteField',
			'click a.add-action': 'addAction',
			'click .action-delete a': 'deleteAction'
		},
		
		initialize: function () {
		},
		
		render: function () {
		},
		
		addField: function (e) {
		},
		
		deleteField: function (e) {
		},
		
		addAction: function (e) {
		},
		
		deleteAction: function (e) {
		}
	});

	window.App = new AppView;
});