<?php

if (!function_exists('format_reference_number')) {
    function format_reference_number($type)
    {
        return strtoupper($type) . '-' . str_pad(Date('dmys'), 5, '0', STR_PAD_LEFT);
    }
}
