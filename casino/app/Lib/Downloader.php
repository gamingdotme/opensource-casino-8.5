<?php

namespace VanguardLTE\Lib;

class Downloader {

    function download_send_headers($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    function array2csv(array &$array, $keys=false){
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        if($keys){
            fputcsv($df, $keys, ';');
        }else{
            fputcsv($df, array_keys(reset($array)), ';');
        }

        foreach ($array as $row) {
            fputcsv($df, $row, ';');
        }
        fclose($df);
        return ob_get_clean();
    }
}