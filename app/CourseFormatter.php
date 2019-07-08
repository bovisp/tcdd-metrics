<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class CourseFormatter {
    /**
    * Formats training portal courses for course catalog.
    *
    * Splits information from "summary" column into separate properties. Gets French or English course information based on $lang param
    *
    * @param string $lang Language of course information to be returned (French or English) 
    *
    * @param array $collection Course information from database
    *
    * @return array returns formatted collection of course information
    */
    public function formatMoodleCourses($lang, $collection) {

        $formattedCollection = new Collection;
        if($lang === 'fr') { //abstract into method
            $formattedCollection = $collection->each(function ($category) {
                $original = $category->name;
                $category->name = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">[\s\S]*<\/span> <span lang=\"fr\" class=\"multilang\">|<\/span>/", "", $category->name));
                if($original === $category->name) { // only run the second preg_replace if the first did nothing
                    $category->name = trim(preg_replace("/{mlang en}[\s\S]*{mlang}[\s\S]*{mlang fr}|{mlang}[\s\S]*/", "", $category->name));
                }

                $category->name = ucwords($category->name);

                $category->courses->each(function ($row) {
                    $summaryArr = preg_split("/<p>{mlang} /", $row->keywords);
                    $frenchSummary = count($summaryArr) > 1 ? $summaryArr[1] : "";
                    
                    $original = $row->longTitle;
                    $row->longTitle = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">[\s\S]*<\/span> <span lang=\"fr\" class=\"multilang\">|<\/span>/", "", $row->longTitle));
                    if($original === $row->longTitle) { //only run the second preg_replace if the first did nothing
                        $row->longTitle = trim(preg_replace("/{mlang en}[\s\S]*{mlang}[\s\S]*{mlang fr}|{mlang}[\s\S]*/", "", $row->longTitle));
                    }

                    $row->shortTitle = $this->truncate($row->longTitle, 80);

                    $row->keywords = preg_replace('~[\s\S]*Mots-clés</span>:\s?<span class="value">|</span></div>\s*<div id="estimatedtime">[\s\S]*~', "", $frenchSummary);
                    $row->estimatedtime = preg_replace('~[\s\S]*urée estimée</span>:\s?<span class="value">|</span></div>\s*<div id="objectives">[\s\S]*~', "", $frenchSummary);
                    $row->lastmodified = Carbon::createFromTimestamp($row->lastmodified)->toDateString();
                    $row->timecreated = Carbon::createFromTimestamp($row->timecreated)->toDateString();
                    $row->description = preg_replace('~[\s\S]*<div id="description-content">|</div>[\s\S]*~', "", $frenchSummary);
                    $row->description = $this->truncate($row->description);
                    $row->objectives = preg_replace('~[\s\S]*<div id="objectives-content">|</div>\s*<div id="description">[\s\S]*~', "", $frenchSummary);
                });

                $category->courses = $category->courses->sortBy('longTitle');
            });
        }
        else if($lang === 'en') {
            $formattedCollection = $collection->each(function ($category) {
                $original = $category->name;
                $category->name = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">|<\/span> <span lang=\"fr\" class=\"multilang\">(.*)<\/span>/", "", $category->name));
                if($original === $category->name) { // only run the second preg_replace if the first did nothing
                    $category->name = trim(preg_replace("/{mlang en}|{mlang}{mlang fr}(.*){mlang}|{mlang} {mlang fr}(.*){mlang}/", "", $category->name));
                }

                $category->name = ucwords($category->name);

                $category->courses->each(function ($row) {
                    $summaryArr = preg_split("/<p>{mlang} /", $row->keywords);
                    $englishSummary = $summaryArr[0];
                    
                    $original = $row->longTitle;
                    $row->longTitle = trim(preg_replace("/<span lang=\"en\" class=\"multilang\">|<\/span> <span lang=\"fr\" class=\"multilang\">(.*)<\/span>/", "", $row->longTitle));
                    if($original === $row->longTitle) { // only run the second preg_replace if the first did nothing
                        $row->longTitle = trim(preg_replace("/{mlang en}|{mlang}{mlang fr}(.*){mlang}|{mlang} {mlang fr}(.*){mlang}/", "", $row->longTitle));
                    }

                    $row->shortTitle = $this->truncate($row->longTitle, 80);

                    $row->keywords = preg_replace('~[\s\S]*eywords</span>:\s?<span class="value">|</span></div>\s*<div id="estimatedtime">[\s\S]*~', "", $englishSummary);
                    $row->estimatedtime = preg_replace('~[\s\S]*stimated time to complete</span>:\s?<span class="value">|</span></div>\s*<div id="objectives">[\s\S]*~', "", $englishSummary);
                    $row->lastmodified = Carbon::createFromTimestamp($row->lastmodified)->toDateString();
                    $row->timecreated = Carbon::createFromTimestamp($row->timecreated)->toDateString();
                    $row->description = preg_replace('~[\s\S]*<div id="description-content">|</div>[\s\S]*~', "", $englishSummary);
                    $row->description = $this->truncate($row->description);
                    $row->objectives = preg_replace('~[\s\S]*<div id="objectives-content">|</div>\s*<div id="description">[\s\S]*~', "", $englishSummary);
                });

                $category->courses = $category->courses->sortBy('longTitle');
            });
        }

        $formattedCollection = $formattedCollection->sortBy('name');
        return $formattedCollection;
    }

    /**
    * Formats COMET courses for course catalog.
    *
    * Splits information from "summary" column into separate properties. Gets French or English course information based on $lang param
    *
    * @param string $lang Language of course information to be returned (French or English) 
    *
    * @param array $collection Course information from database
    *
    * @return array returns formatted collection of course information
    */
    public function formatCometCourses($lang, $array) {
        foreach($array as $category) {
            $category->courses = $category->courses->each(function ($row) use ($lang) {
                $row->completionTime = preg_replace("~h~", "", $row->completionTime);
                $plusFlag = false;
    
                if(preg_match("~\+~", $row->completionTime)) {
                    preg_replace("~\+~", "", $row->completionTime);
                    $plusFlag = true;
                }
                $row->completionTime = rtrim($row->completionTime);
    
                $timeArr = preg_split("~ - ~", $row->completionTime);
                $minTime = $this->getCompletionTime($lang, $timeArr[0]);
                $row->completionTime = $minTime;
    
                if(count($timeArr) > 1) {
                    $maxTime = $this->getCompletionTime($lang, $timeArr[1]);
                    if($lang === 'fr') {
                        $row->completionTime = $row->completionTime . " &agrave; " . $maxTime;
                    } else if($lang === 'en') {
                        $row->completionTime = $row->completionTime . " - " . $maxTime;
                    }
                }
                $row->completionTime = rtrim($row->completionTime);
                if($plusFlag) {
                    $row->completionTime = $row->completionTime . "+";
                }
                
                $row->description = $this->truncate($row->description);
                $row->shortTitle = $this->truncate($row->shortTitle, 80);
                $row->lastUpdated = $row->lastUpdated !== "" ? Carbon::parse($row->lastUpdated)->toDateString() : "";
                $row->publishDate = Carbon::parse($row->publishDate)->toDateString();
                $row->topics = $this->truncate($row->topics, 70);
            });
        }
        return $array;
    }

    /**
    * Formats estimated time to complete COMET course into same format used for training portal courses.
    *
    * @param string $lang Language of course information to be returned (French or English)
    *
    * @param string $completionTimeStr Time string in COMET format
    *
    * @return string $completionTimeStr Returns time string in training portal format
    */
    private function getCompletionTime($lang, $completionTimeStr) {
        $hours = '';
        $minutes = '';

        $completionTimeStrArr = preg_split("~\.~", $completionTimeStr);
        if(count($completionTimeStrArr) > 1) {
            $completionTimeStrArr[1] = rtrim($completionTimeStrArr[1], "0");
        }

        if($lang === 'fr') {
            if($completionTimeStrArr[0] !== "0" && $completionTimeStrArr[0] !== "") {
                $hours = $completionTimeStrArr[0];
                $minutes = "." . (count($completionTimeStrArr) > 1 ? $completionTimeStrArr[1] : "");
                $completionTimeStr = $hours . " h ";
            } else {
                $minutes = $completionTimeStr;
                $completionTimeStr = "";
            }
            if($minutes > 0) {
                $minutes = (float) $minutes;
                $minutes = (int) floor($minutes * 60);
                if($completionTimeStrArr[0] !== "0" && $completionTimeStrArr[0] !== "") {
                    $completionTimeStr = $completionTimeStr . $minutes;
                } else {
                    $completionTimeStr = $completionTimeStr . $minutes . " minutes";
                }
            }
            if($completionTimeStr === '') {
                $completionTimeStr = "0";
            }
            return $completionTimeStr;
        } else if ($lang === 'en'){
            if($completionTimeStrArr[0] !== "0" && $completionTimeStrArr[0] !== "") {
                $hours = $completionTimeStrArr[0];
                $minutes = "." . (count($completionTimeStrArr) > 1 ? $completionTimeStrArr[1] : "");
                $completionTimeStr = $hours . "h ";
            } else {
                $minutes = $completionTimeStr;
                $completionTimeStr = "";
            }
            if($minutes > 0) {
                $minutes = (float) $minutes;
                $minutes = (int) floor($minutes * 60);
                $completionTimeStr = $completionTimeStr . $minutes . "m";
            }
            if($completionTimeStr === '') {
                $completionTimeStr = "0";
            }
            return $completionTimeStr;
        }
    }

    /**
    * Truncates string to given number of characters by wrapping to the nearest word.
    *
    * @param string $string String to be truncated
    *
    * @param integer $length Character limit for string
    *
    * @param string $append String to append to end of string after truncation
    *
    * @return string $string Returns truncated string
    */
    private function truncate($string, $length=250, $append="...") {
        $string = trim($string);
        $string = preg_replace("~\n~", " ", $string);
      
        if(strlen($string) > $length) {
          $string = wordwrap($string, $length);
          $string = explode("\n", $string, 2);
          $string = rtrim($string[0], " ,") . $append;
        }
        return $string;
    }
}
