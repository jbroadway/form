; <?php

[form/index]

label = "Embed a Form"

id[label] = Form
id[type] = select
id[require] = "apps/form/lib/Functions.php"
id[callback] = "form_list_all"
id[not empty] = 1
id[message] = Please choose a form.

; */ ?>
