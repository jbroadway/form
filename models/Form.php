<?php

namespace form;

/**
 * This model contains the outline of forms to be rendered.
 * Forms are built using a form builder admin app for Elefant.
 */
class Form extends \Model
{
    /**
	 * Table name.
	 */
    public $table = '#prefix#form';

    /**
	 * List of fields that failed validation.
	 */
    public $failed = array ();

    /**
	 * Stores field data.
	 */
    private $_fields = false;

    /**
	 * Stores action data.
	 */
    private $_actions = false;

    /**
	 * A `\Form` object used internally.
	 */
    private $_form;

    /**
	 * Get labels as associative array from fields.
	 */
    public function labels()
    {
        $labels = array ();
        foreach ($this->field_list as $field) {
            $labels[$field->id] = $field->label;
        }

        return $labels;
    }

    /**
	 * Get a list of validation rules for this form.
	 */
    public function rules()
    {
        $rules = array ();
        foreach ($this->field_list as $field) {
            $rules[$field->id] = (array) $field->rules;
        }

        return $rules;
    }

    /**
	 * Override the `put()` method to validate structures before
	 * saving them.
	 */
    public function put()
    {
        if (! $this->_validate_fields ($this->field_list)) {
            $this->error = 'Fields failed to validate';

            return false;
        }
        if (! $this->_validate_actions ($this->actions)) {
            $this->error = 'Actions failed to validate';

            return false;
        }

        return parent::put ();
    }

    /**
	 * Validates fields array has the correct structure and the
	 * required fields for each field type. Note: Does not validate
	 * the contents of the values except field types.
	 */
    public function _validate_fields($fields)
    {
        // verify it's an array
        if (! is_array ($fields)) {
            return false;
        }

        foreach ($fields as $field) {
            // normalize to an object if it's an array
            if (is_array ($field)) {
                $field = (object) $field;
            }

            // verify it's an array of objects
            if (! is_object ($field)) {
                return false;
            }

            // verify rules
            if (! is_object ($field->rules)) {
                return false;
            } elseif (! $this->_validate_rules ($field->rules)) {
                return false;
            }

            // attributes shared by all field types
            if (! isset ($field->type)) {
                return false;
            }
            if (! isset ($field->label)) {
                return false;
            }
            if (! isset ($field->id)) {
                return false;
            }
            if (! isset ($field->message)) {
                return false;
            }
            if (! isset ($field->default_value)) {
                return false;
            }

            // type-specific fields
            if ($field->type === 'text') {
                if (! isset ($field->size)) {
                    return false;
                }
                if (! isset ($field->placeholder)) {
                    return false;
                }
            } elseif ($field->type === 'textarea') {
                if (! isset ($field->cols)) {
                    return false;
                }
                if (! isset ($field->rows)) {
                    return false;
                }
                if (! isset ($field->placeholder)) {
                    return false;
                }
            } elseif ($field->type === 'select' || $field->type === 'checkbox' || $field->type === 'radio') {
                if (! isset ($field->values) || ! is_array ($field->values)) {
                    return false;
                }
            } elseif ($field->type === 'range') {
                if (! isset ($field->min)) {
                    return false;
                }
                if (! isset ($field->max)) {
                    return false;
                }
            } elseif ($field->type !== 'date') {
                // invalid type
                return false;
            }
        }

        return true;
    }

    /**
	 * Validates rules array. Note: Does not validate that the
	 * rules themselves are valid.
	 */
    public function _validate_rules($rules)
    {
        // verify it's an object
        if (! is_object ($rules)) {
            return false;
        }

        $rules = (array) $rules;

        foreach ($rules as $key => $value) {
            // key must be a string
            if (! is_string ($key)) {
                return false;
            }

            // value must not be an array or object
            if (is_array ($value) || is_object ($value)) {
                return false;
            }
        }

        return true;
    }

    /**
	 * Validates actions array has the correct structure and the
	 * required fields for each action type. Note: Does not validate
	 * the contents of the values except action types.
	 */
    public function _validate_actions($actions)
    {
        // verify it's an array
        if (! is_array ($actions)) {
            return false;
        }

        foreach ($actions as $action) {
            // normalize to an object if it's an array
            if (is_array ($action)) {
                $action = (object) $action;
            }

            // verify it's an array of objects
            if (! is_object ($action)) {
                return false;
            }

            // verify it has a type
            if (! isset ($action->type)) {
                return false;
            }

            // type-specific fields
            if ($action->type === 'email') {
                if (! isset ($action->to)) {
                    return false;
                }
            } elseif ($action->type === 'redirect') {
                if (! isset ($action->url)) {
                    return false;
                }
            } elseif ($action->type === 'cc_recipient') {
                if (! isset ($action->name_field)) {
                    return false;
                }
                if (! isset ($action->email_field)) {
                    return false;
                }
                if (! isset ($action->reply_from)) {
                    return false;
                }
                if (! isset ($action->subject)) {
                    return false;
                }
                if (! isset ($action->body_intro)) {
                    return false;
                }
                if (! isset ($action->body_sig)) {
                    return false;
                }
                if (! isset ($action->include_data)) {
                    return false;
                }
            } else {
                // invalid type
                return false;
            }
        }

        return true;
    }

    /**
	 * Checks whether the form can be submitted.
	 */
    public function submit()
    {
        if (! is_object ($this->_form)) {
            $this->_form = new \Form ('POST');
            $this->_form->rules = $this->rules ();
        }
        $res = $this->_form->submit ();
        $this->failed = $this->_form->failed;

        return $res;
    }

    /**
	 * Calls the internal `\Form::merge_values()`.
	 */
    public function merge_values($obj)
    {
        return $this->_form->merge_values ($obj);
    }

    /**
	 * Sends email messages, either through `Mailer::send()` or `mail()`
	 * depending on which is available.
	 */
    public function send_email($to, $subject, $body, $from = false, $reply_to = false)
    {
        if (file_exists ('lib/Mailer.php')) {
            $msg = array (
                'to' => $to,
                'subject' => $subject,
                'text' => $body
            );
            if ($from !== false) {
                $msg['from'] = $from;
            }
            if ($reply_to) {
                $msg['reply_to'] = $reply_to;
            }
            try {
                return \Mailer::send ($msg);
            } catch (\Exception $e) {
                $this->error = $e->getMessage ();

                return false;
            }
        } else {
            $to = is_array ($to) ? '"' . $to[1] . '" <' . $to[0] . '>' : $to;
            if ($from === false) {
                $from = array ();
                $from[0] = (self::$config['email_from'] !== 'default') ? self::$config['email_from'] : conf ('General', 'email_from');
                $from[1] = (self::$config['email_name'] !== 'default') ? self::$config['email_name'] : conf ('General', 'site_name');
            }
            $from = is_array ($from) ? '"' . $from[1] . '" <' . $from[0] . '>' : $from;

            return mail (
                $to,
                $subject,
                $body,
                'From: ' . $from
            );
        }
    }

    /**
	 * Dynamic getter that unserializes fields and actions.
	 */
    public function __get($key)
    {
        if ($key == 'field_list') {
            if ($this->_fields === false) {
                $this->_fields = json_decode ($this->data['fields']);
            }

            return $this->_fields;
        } elseif ($key == 'actions') {
            if ($this->_actions === false) {
                $this->_actions = json_decode ($this->data['actions']);
            }

            return $this->_actions;
        }

        return parent::__get ($key);
    }

    /**
	 * Dynamic setter that serializes fields and actions.
	 */
    public function __set($key, $val)
    {
        if ($key == 'field_list') {
            $this->_fields = $val;
            $this->data['fields'] = json_encode ($val);

            return;
        } elseif ($key == 'actions') {
            $this->_actions = $val;
            $this->data[$key] = json_encode ($val);

            return;
        }

        return parent::__set ($key, $val);
    }
}
