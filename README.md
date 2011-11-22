Forms is a form builder app for the [Elefant CMS](http://www.elefantcms.com/).
Admins can create forms by adding fields, assigning validation rules, and
selecting form handling "actions", then embed them into web pages through
the Dynamic Objects dialog.

## Status: Pre-alpha

This app is still in the process of being outlined. No admin UI has been
created, and only the underlying data structures/handling logic have been
started.

To do:

* Select fields
* Checkbox fields
* Radio fields
* Default values
* Admin UI
  * Form builder
  * Export results

Done:

* Server-side validation
* Client-side validation
* Basic output
* CSRF prevention
* Text field rendering
* Textarea rendering
* Saving to results table
* Calling hooks
* Email action
* CC Recipient handler
* Store IP addresses
* Embed through Dynamic Objects
* Admin UI
  * Browse results

## Feature overview

* Easy to use web-based form builder
* Define email and cc-recipient handlers
* Set response text or redirect on submit
* Saves all submissions to browsable results table
* Export results as a CSV file
* Form validation occurs both client-side and server-side
* Hooks let you define custom PHP form handling
* Embed forms into any page through the WYSIWYG editor
* CSRF and other abuse prevention built-in
* Tracks submission time and IP address

## Notes on storage

Forms are defined in a 'form' table and have the following structure:

```
id (int)
title (char)
message (text)
ts (timestamp)
fields (json)
actions (json)
response_title (char)
response_body (text)
```

Results are stored in the following structure:

```
id (int)
form_id (int)
ts (timestamp)
results (json)
```

### Fields in JSON

Fields is a JSON array with the following structure for each element:

```
{
	"id": "website",
	"label": "Website",
	"type": "text",
	"size": 40,
	"rules": {
		"not empty": 1,
		"regex": "|^https?://|"
	},
	"message": "Please enter a valid website address.",
	"default": "http://",
	"placeholder": "http://"
}
```

Types can include:

* text
* textarea
* radio
* checkbox
* select

Additional properties for textarea:

* `"cols": 60`
* `"rows": 4`

Additional properties for select, checkbox, and radio:

* `"values": ["Option 1", "Option 2"]`

This is not exhaustive, but it is sufficient for most ordinary web forms, which
is the target use for this app. Forms is not meant to handle every type of form,
and the Elefant form API makes it easy to create custom forms.

### Actions in JSON

Actions are stored as a JSON array. Here are examples of each type of handling action:

```
[
	{
		"type": "email",
		"to": "joe@example.com"
	},
	{
		"type": "cc_recipient",
		"name_field": "name",
		"email_field": "email",
		"reply_from": "you@example.com",
		"subject": "Thank you for contacting us",
		"body_intro": "Your message has been received and will be answered shortly.",
		"body_sig": "Thank you,\nExample Co.",
		"include_data": "yes"
	},
	{
		"type": "redirect",
		"url": "/thank/you"
	}
]
```

Additionally, all data is saved to the `results` table, where it can be viewed through
Elefant and exported as a CSV file, and a call will be made through Elefant's hooks
system so you can register a handler in the global config as follows:

```
form/submitted[] = myapp/handler
```

In `apps/myapp/handlers/handler.php` you can add custom handling code to the form
like this:

```php
<?php

if (! $this->internal) {
	// Security keep-out
	die ('Must be called by another handler');
}

if ($data['form'] != 123) {
	// Listen for a specific form
	return;
}

// Retrieve the form data via the
// $data['values'] associative array
// and add your custom handling here.

?>
```

### Results in JSON

Result data is saved as a JSON array of key/value pairs.
