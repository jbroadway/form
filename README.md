Forms is a form builder app for the [Elefant CMS](http://www.elefantcms.com/).
Admins can create forms by adding fields, assigning validation rules, and
selecting form handling "actions", then embed them into web pages through
the Dynamic Objects dialog.

> Note: Requires Elefant 1.1+

## Status: Alpha

The form builder is currently just an outline, but the rest of the app is
fairly complete, including form publishing and handling.

To do:

* Form builder
  * Link all edit fields
  * Link validation rules
  * Enable delete links
  * Server-side field structure input validation
  * Versioning on saved changes

Done:

* Server-side validation
* Client-side validation
* Default values
* Basic output
* CSRF prevention
* Field types
  * Text field rendering
  * Textarea rendering
  * Select fields
  * Radio fields
  * Checkbox fields
  * Range
  * Date
* Saving to results table
* Calling hooks
* Email action
* CC Recipient handler
* Store IP addresses
* Embed through Dynamic Objects
* Admin UI
  * Browse results
  * Export results
  * Form builder
    * Edit form properties, fields, and actions (partial)
    * Re-sort fields via drag and drop
    * Preview tab
    * Auto-saves changes transparently

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
