<?php


namespace App\Service;


class Slugify
{
    public function generate(string $input) : string
    {
        $input=preg_replace('/[!-,]/', '', $input);
        $input= str_replace(' ', '-', $input);
        $input= trim($input);
        $input =iconv('UTF-8', 'ASCII//TRANSLIT',$input);
        return$input;
    }
}