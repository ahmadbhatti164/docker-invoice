<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\User;


function flashMessage($messageIndex, $itemName = '', $counter = NULL){
    try{
        $messages = [
            "insert" => ($itemName==''?'Item':$itemName).' has been added successfully.',
            "update" => ($itemName==''?'Item':$itemName).' has been updated successfully.',
            "delete" => ($itemName==''?'Item':$itemName).' has been deleted successfully.',
            "notFound" => ($itemName==''?'Item':$itemName).' not found.',
            "notDeleteable" => 'You have not right to delete it.',
            "notEditable" => 'You have not right to edit it.',
            "contactUsMessage" => 'Thank you for contacting us.',
        ];
        return $messages[$messageIndex];
    } catch(Exception $e){
        return $e->getFile().' (Line No. '.$e->getLine().'): '.$e->getMessage();
    }
}

function saveFile($file, $path, $fileName, $width=null, $height=null){
    $uploadedPath = '';
    $uploadedPath = Storage::put($path.'/'.$fileName,  File::get($file));
    $uploadedPath = $path.'/'.$fileName;

    return $uploadedPath;
}

function exceptionMessage($e){
    return $e->getFile().' (Line No. '.$e->getLine().'): '.$e->getMessage();
}

function strip_tags_content($text, $tags = '', $invert = FALSE) {

    preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
    $tags = array_unique($tags[1]);

    if(is_array($tags) AND count($tags) > 0) {
        if($invert == FALSE) {
            return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        else {
            return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
        }
    }
    elseif($invert == FALSE) {
        return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
    }
    return $text;
}
?>
