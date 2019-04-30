<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait FormatCollection {

    private function formatTwoColumns(Collection $collection, string $englishColumn, string $frenchColumn)
    {
        $formattedCollection = $collection->each(function ($x) use ($englishColumn, $frenchColumn){
            //english course name formatting
            $original = $x->{$englishColumn};
            $x->{$englishColumn} = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">|<\/span> <span lang=\"fr\" class=\"multilang\">(.*)<\/span>/", "", $x->{$englishColumn}));
            if($original === $x->{$englishColumn}) { //only run the second preg_replace if the first did nothing
                $x->{$englishColumn} = trim(preg_replace("/{mlang en}|{mlang}{mlang fr}(.*){mlang}|{mlang} {mlang fr}(.*){mlang}/", "", $x->{$englishColumn}));
            }
            
            //french course name formatting
            $original = $x->{$frenchColumn};
            $x->{$frenchColumn} = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">(.*)<\/span> <span lang=\"fr\" class=\"multilang\">|<\/span>/", "", $x->{$frenchColumn}));
            if($original === $x->{$frenchColumn}) { //only run the second preg_replace if the first did nothing
                $x->{$frenchColumn} = trim(preg_replace("/{mlang en}(.*){mlang}{mlang fr}|{mlang en}(.*){mlang} {mlang fr}|{mlang}/", "", $x->{$frenchColumn}));
            }
        });
        return $formattedCollection;
    }

    private function formatOneColumn(Collection $collection, string $column)
    {
        $formattedCollection = $collection->each(function ($x) use ($column) {
            $original = $x->{$column};
            //english course name formatting
            $englishname = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">|<\/span> <span lang=\"fr\" class=\"multilang\">(.*)<\/span>/", "", $x->{$column}));
            
            if($original === $englishname) { //only run the second preg_replace if the first did nothing
                $englishname = trim(preg_replace("/{mlang en}|{mlang}{mlang fr}(.*){mlang}|{mlang} {mlang fr}(.*){mlang}/", "", $x->{$column}));
            }
            
            //french course name formatting
            $frenchname = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">(.*)<\/span> <span lang=\"fr\" class=\"multilang\">|<\/span>/", "", $x->{$column}));
            
            if($original === $frenchname) { //only run the second preg_replace if the first did nothing
                $frenchname = trim(preg_replace("/{mlang en}(.*){mlang}{mlang fr}|{mlang en}(.*){mlang} {mlang fr}|{mlang}/", "", $x->{$column}));
            }

            $x->{$column} = $englishname . " / " . $frenchname;
        });
        return $formattedCollection;
    }
}
