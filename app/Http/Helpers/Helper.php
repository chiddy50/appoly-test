<?php
namespace App\Http\Helpers;

class Helper {

    public static function createList($data) {
        $label = strtolower($data['label']);
        $children = $data['children'] ?? $data['meta'];
        $html = '';
        $step = 0;
        if (count($children) > 0) {
            $html .= "<li>";
            $html .= "<a href='edit-item?id='".$label."' class='feed__link' >". $label ."</a>";

            // $html .= $label;
            $html .= "<ul>";
            foreach ($children as $child) {
                $step += 1;
                $html .= self::printLI($child);
            }
            $html .= "</ul>";
            $html .= "</li>";
            echo $html;
        }else{
            $html = "<li>".$label."</li>";
            echo $html;
        }
    }

    public static function printLI($data) {

        $children = $data['children'] ?? $data['meta'];
        $label = strtolower($data['label']);

        $html = "";
        $html .= "<li>";
        $html .= "<a href='/edit-item?id='".$label."' class='feed__link' >". $label ."</a>";

        // $html .= $label;

        if (count($children) > 0) {
            $html .= "<ul>";
            foreach ($children as $child) {
                // $html .= $child['label'];

                // if (count($child['children']) > 0) {
                //     $html .= "<ul>";
                    // $html .= self::printLI($child);
                //     $html .= "</ul>";
                // }
                // return $html;
            }
            $html .= "</ul>";

            $html .= "</li>";
            return $html;
        }else{
            $html .= "</li>";

            return $html;
        }
    }

    public static function generateList($data, $id) {
        $label = strtolower($data['label']);
        $children = $data['children'] ?? $data['meta'];

        $html = '';

        if (count($children) > 0) {
            $html .= "<li>";
            $html .= $label;
            $html .= "<ul>";

            foreach ($children as $child) {
                // $html .= self::printLI($child);
            }

            $html .= "</ul>";
            $html .= "</li>";
            echo $html;
        }else{
            echo "<li>".$label."</li>";
        }
    }
}
