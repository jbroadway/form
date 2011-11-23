/**
 * Front-end logic for /form/edit form builder.
 */

$(function () {
	// Initialize the tabs
	$('#tabs').tabs ();

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