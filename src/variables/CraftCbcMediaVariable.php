<?php
/**
 * Craft CBC Media plugin for Craft CMS 3.x
 *
 * Get the CBC Media Feed
 *
 * @link      thisanimus.com
 * @copyright Copyright (c) 2018 Andrew Hale
 */

namespace countrysidebible\craftcbcmedia\variables;

use countrysidebible\craftcbcmedia\CraftCbcMedia;

use Craft;

/**
 * Craft CBC Media Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.craftCbcMedia }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Andrew Hale
 * @package   CraftCbcMedia
 * @since     1.0.0
 */
class CraftCbcMediaVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.craftCbcMedia.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.craftCbcMedia.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function cleanText($html){
            $chr_map = array(
           // Windows codepage 1252
           "\xC2\x82" => "'", // U+0082⇒U+201A single low-9 quotation mark
           "\xC2\x84" => '"', // U+0084⇒U+201E double low-9 quotation mark
           "\xC2\x8B" => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
           "\xC2\x91" => "'", // U+0091⇒U+2018 left single quotation mark
           "\xC2\x92" => "'", // U+0092⇒U+2019 right single quotation mark
           "\xC2\x93" => '"', // U+0093⇒U+201C left double quotation mark
           "\xC2\x94" => '"', // U+0094⇒U+201D right double quotation mark
           "\xC2\x9B" => "'", // U+009B⇒U+203A single right-pointing angle quotation mark

           // Regular Unicode     // U+0022 quotation mark (")
                                  // U+0027 apostrophe     (')
           "\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
           "\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
           "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
           "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
           "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
           "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
           "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
           "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
           "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
           "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
           "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
           "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
           "\xc2\xa0"     => " ", // U+00A0 is a no-break space
        );
        $chr = array_keys  ($chr_map); // but: for efficiency you should
        $rpl = array_values($chr_map); // pre-calculate these two arrays
        $clean_html = str_replace($chr, $rpl, html_entity_decode($html, ENT_QUOTES, "UTF-8"));
        return $clean_html;
    }

    public function getFeed()
    {
        $url = 'https://api.countrysidebible.org/?return=twu';
        $mediafeed = file_get_contents($url);
        $medialist = json_decode($mediafeed, false);
        $mediaobject = $medialist->twuData;
    return $mediaobject;

    }
    public function getSingle($mediacode)
    {
        $url = 'https://api.countrysidebible.org/?return=single&mediacode='.$mediacode;
        $mediafeed = file_get_contents($url);
        $medialist = json_decode($mediafeed, false);
        $mediaobject = $medialist->singleEntry[0];

    return $mediaobject;

    }
    public function transcriptExists($mediacode){
        $date = '20'.preg_replace('/[^0-9,.]/', '', $mediacode);
        $year = substr($date, 0, -4);

        $url = 'https://s3.amazonaws.com/media.countrysidebible.org/'.$year.'/'.$mediacode.'transcript.html';

        $return = false;
        if((@get_headers($url)[0] == 'HTTP/1.1 404 Not Found') || (@get_headers($url)[0] == 'HTTP/1.1 403 Forbidden')){ 
            // No transcript
        }else{
            $return = $url;
        }
        return $return;

    }
  

    public function getTranscript($mediacode){
        $return = null;
        $url =  $this->transcriptExists($mediacode);

        $return = $this->cleanText(file_get_contents($url));

        return $return;

    }
    public function getTranscriptByUrl($url){
        $return = null;

        $return = $this->cleanText(file_get_contents($url));

        return $return;

    }

}
