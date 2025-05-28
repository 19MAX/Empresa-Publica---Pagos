<?php
function display_errors($validation, $field) {
    if ($validation->hasError($field)) {
        return esc($validation->getError($field));
    } else {
        return null;
    }
}


function display_data($last_data, $field, $text=false) {
    if (array_key_exists($field, $last_data)) {
        if ($text) {
            return nl2br($last_data[$field]);
        }
        return esc($last_data[$field]);
    } else {
        return null;
    }
}


function getFlashMessages($flashMessages=null, bool $as_json=false) {
    if ($as_json) {
        $currentFlaskMessages = (isset($flashMessages)) ? json_encode($flashMessages) : json_encode([]);
    } else {
        $currentFlaskMessages = (isset($flashMessages)) ? $flashMessages : [];
    }
    return $currentFlaskMessages;
}