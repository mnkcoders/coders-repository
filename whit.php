<?php

function modify_admin_menus() {
    global $submenu;

    if (array_key_exists('members', $submenu)) {

        foreach ($submenu['members'] as $key => $value) {
            $k = array_search('view_account_notes', $value);
            if ($k) {

                $submenu['members'][$key][$k] = (current_user_can($submenu['members'][$key][1])) ?
                        admin_url('/edit.php?post_type = acct_notes') : '';
            }

            $l = array_search('new_account_note', $value);

            if ($l) {
                $submenu['members'][$key][$l] = (current_user_can($submenu['members'][$key][1])) ?
                        admin_url('/post-new.php?post_type = dojo_acct_notes') : '';
            }
        }
    }
}
