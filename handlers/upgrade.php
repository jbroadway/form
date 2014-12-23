<?php

$this->require_admin ();

$page->layout = 'admin';

$cur = $this->installed ('form', $appconf['Admin']['version']);

if ($cur === true) {
    $page->title = 'Already up-to-date';
    echo '<p><a href="/form/admin">Continue</a></p>';

    return;
}

$page->title = 'Upgrading App: Forms';

$prefix = conf ('Database', 'prefix');
if ($prefix !== '') {
    if (! DB::shift ('select count(*) from #prefix#form')) {
        $conn = conf ('Database', 'master');
        $driver = $conn['driver'];
        DB::beginTransaction ();

        $error = false;
        $sqldata = sql_split (file_get_contents ('apps/form/conf/install_' . $driver . '.sql'));
        foreach ($sqldata as $sql) {
            if (! DB::execute ($sql)) {
                $error = DB::error ();
                break;
            }
        }

        if (! $error) {
            if (! DB::execute (
                'insert into #prefix#form
					(id, title, message, ts, fields, actions, response_title, response_body)
				select * from form'
            )) {
                $error = DB::error ();
            }
        }

        if (! $error) {
            if (! DB::execute (
                'insert into #prefix#form_results
					(id, form_id, ts, ip, results)
				select * from results'
            )) {
                $error = DB::error ();
            }
        }

        if (! $error) {
            if (! DB::execute ('drop table form')) {
                $error = DB::error ();
            }
        }

        if (! $error) {
            if (! DB::execute ('drop table results')) {
                $error = DB::error ();
            }
        }

        if ($error) {
            DB::rollback ();
            echo '<p class="visible-notice">Error: ' . $error . '</p>';
            echo '<p>Upgrade failed.</p>';

            return;
        }
        DB::commit ();
    }
}

echo '<p><a href="/form/admin">Done.</a></p>';

$this->mark_installed ('form', $appconf['Admin']['version']);
