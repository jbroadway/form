Forms is a form builder app for the [Elefant CMS](http://www.elefantcms.com/).
Admins can create forms by adding fields, assigning validation rules, and
selecting form handling "actions", then embed them into web pages through
the Dynamic Objects dialog.

> Note: Requires Elefant 1.1+

## Status: Beta

The form builder is now feature-complete, but requires loads more testing
to be considered stable.

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
* Easily style generated forms to fit your site
* Field types: text, textarea, select, checkbox, radio, range, date
* Form edits are version-controlled

## Future possibilities

* More field types
* More validation types
* Multi-page forms
* Export forms to PHP so they can be customized further
* Conditional fields (`if (field_a == 'yes') { show field_b } else { show field_c }`)
