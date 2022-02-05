<?php

namespace form;

/**
 * This model stores individual form submissions in the `results`
 * table.
 */
class Results extends \Model
{
    /**
	 * Table name.
	 */
    public $table = '#prefix#form_results';

    /**
	 * Stores results data.
	 */
    private $_results = false;

    /**
	 * Create a reference to the form table.
	 */
    public $fields = array (
        'form_id' => array ('ref' => 'form\Form')
    );

    /**
	 * Dynamic getter that unserializes results.
	 */
    public function __get($key)
    {
        if ($key == 'results') {
            if ($this->_results === false) {
                $this->_results = json_decode ($this->data['results']);
            }

            return $this->_results;
        }

        return parent::__get ($key);
    }

    /**
	 * Dynamic setter that serializes results.
	 */
    public function __set($key, $val)
    {
        if ($key == 'results') {
            $this->_results = $val;
            $this->data[$key] = json_encode ($val);

            return;
        }

        return parent::__set ($key, $val);
    }
    
    /**
     * Mark the total results for a list of forms.
     */
    public static function mark_forms (&$forms) {
    	$ids = array_map (function ($f) { return $f->id; }, $forms);

    	$res = self::query ('form_id, count(*) as results')
    		->where_in ('form_id', $ids)
    		->group ('form_id')
    		->fetch_assoc ('form_id', 'results');
    	
    	foreach ($forms as $k => $f) {
    		$forms[$k]->results = isset ($res[$f->id]) ? $res[$f->id] : 0;
    	}
    }
}
