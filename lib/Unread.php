<?php

namespace form;

use DB;

/**
 * Manages unread form submissions.
 */
class Unread {
	/**
	 * Get the total number of unread submissions for a given user.
	 */
	public static function total ($user_id) {
		$results = DB::shift (
			'select count(*) from #prefix#form_results'
		);
		
		$read = DB::shift (
			'select count(*) from #prefix#form_read where user_id = ?',
			$user_id
		);
		
		return $results - $read;
	}
	
	/**
	 * Get the total number of unread submissions for a given form and user.
	 */
	public static function total_for_form ($form_id, $user_id) {
		$results = DB::shift (
			'select count(*) from #prefix#form_results where form_id = ?',
			$form_id
		);
		
		$read = DB::shift (
			'select count(*) from #prefix#form_read where form_id = ? and user_id = ?',
			$form_id,
			$user_id
		);
		
		return $results - $read;
	}
	
	/**
	 * Mark a list of forms with the number of unread submissions. Assumes the
	 * forms have previously been passed to `form\Results::mark_forms()` to fetch
	 * the total number of submissions.
	 */
	public static function mark_forms (&$forms, $user_id) {
		$count = count ($forms);
		if ($count === 0) return;
		
		$ids = array_map (function ($f) { return $f->id; }, $forms);
		$qmarks = array_fill (0, $count, '?');
		$params = $ids;
		$params[] = $user_id;
		
		$res = DB::pairs (
			'select form_id, count(*) from #prefix#form_read where form_id in(' . join (', ', $qmarks) . ') and user_id = ?
			 group by form_id',
			$params
		);
		
		foreach ($forms as $k => $f) {
			$read = isset ($res[$f->id]) ? $res[$f->id] : 0;
			$results = isset ($f->results) ? $f->results : 0;
			$forms[$k]->unread = $results - $read;
		}
	}
	
	/**
	 * Mark a list of form submissions as read or not. Adds a `read` property
	 * to each `form\Results` object in the list.
	 */
	public static function mark_results (&$results, $user_id) {
		$count = count ($results);
		if ($count === 0) return;
		
		$ids = array_map (function ($r) { return $r->id; }, $results);
		$qmarks = array_fill (0, $count, '?');
		$params = $ids;
		$params[] = $user_id;
		
		$read = DB::shift_array (
			'select results_id from #prefix#form_read where results_id in(' . join (', ', $qmarks) . ') and user_id = ?',
			$params
		);
		
		foreach ($results as $k => $r) {
			$results[$k]->read = in_array ($r->id, $read);
		}
	}
	
	/**
	 * Mark a form submission as read by a given user.
	 */
	public static function mark_read ($form_id, $results_id, $user_id) {
		$exists = DB::shift (
			'select count(*) from #prefix#form_read where form_id = ? and results_id = ? and user_id = ?',
			$form_id,
			$results_id,
			$user_id
		);
		
		if (intval ($exists) > 0) {
			return true;
		}
		
		return DB::execute (
			'insert into #prefix#form_read (form_id, results_id, user_id) values (?, ?, ?)',
			$form_id,
			$results_id,
			$user_id
		);
	}
}
