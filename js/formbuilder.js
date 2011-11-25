/**
 * Front-end logic for /form/edit form builder.
 */

$(function () {
	// Initialize the tabs
	$('#tabs').tabs ();

	// Initialize the full/list/preview buttons
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

	/**
	 * Form model.
	 */
	window.Form = Backbone.Model.extend ({
		defaults: function () {
			return {
				title: 'Untitled',
				message: 'Please fill in the following information.',
				fields: [],
				actions: [],
				response_title: 'Thank you',
				response_body: 'Your information has been saved.'
			};
		}
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