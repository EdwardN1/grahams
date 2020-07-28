<?php
add_shortcode('wpam-import', 'wpam_import_shortcode');

function wpam_import_shortcode($params=array()) {
    $row = 1;
    if (is_array($params)) {
        if (array_key_exists('name', $params)) {
            if (($handle = fopen($params['name'], "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    echo "<p> $num fields in line $row: <br /></p>\n";
                    $row++;
                    for ($c = 0; $c < $num; $c++) {
                        echo $data[$c] . "<br />\n";
                    }
                }
                fclose($handle);
            }
        }
}