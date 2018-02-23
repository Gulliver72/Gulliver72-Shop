<?php
/** Clear unnecessary chars
 * 
 * @param string  $input
 * @return string
 */
function clean($input)
{
    $input = str_replace("\'", "'", $input);
    $input = str_replace('\\\\', '\\', $input);
    $input = str_replace('<br />', "\n", $input);
    $input = str_replace('&amp;', '&', $input);
    $input = str_replace('&quot;', '"', $input);
    $input = str_replace('<', '&lt;', $input);
    $input = str_replace('>', '&gt;', $input);
    return $input;
}
?>