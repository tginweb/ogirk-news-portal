<?php

namespace SM\Util;

class Xml
{
    static function xml_cdata($str)
    {
        if (seems_utf8($str) == false) $str = utf8_encode($str);
        // $str = ent2ncr(esc_html($str));
        $str = '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $str) . ']]>';
        return $str;
    }

    static function xml_url()
    {
        if (is_multisite()) return network_home_url();
        else return get_bloginfo_rss('url');
    }

    static function xml_cat_name($category)
    {
        if (empty($category->name)) return;
        echo '<wp:cat_name>' . self::xml_cdata($category->name) . '</wp:cat_name>';
    }

    static function xml_category_description($category)
    {
        if (empty($category->description)) return;
        echo '<wp:category_description>' . self::xml_cdata($category->description) . '</wp:category_description>';
    }

    static function xml_tag_name($tag)
    {
        if (empty($tag->name)) return;
        echo '<wp:tag_name>' . self::xml_cdata($tag->name) . '</wp:tag_name>';
    }

    static function xml_tag_description($tag)
    {
        if (empty($tag->description)) return;
        echo '<wp:tag_description>' . self::xml_cdata($tag->description) . '</wp:tag_description>';
    }

    static function xml_term_name($term)
    {
        if (empty($term->name)) return;
        echo '<wp:term_name>' . self::xml_cdata($term->name) . '</wp:term_name>';
    }

    static function xml_term_description($term)
    {
        if (empty($term->description)) return;
        echo '<wp:term_description>' . self::xml_cdata($term->description) . '</wp:term_description>';
    }



    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public static function array_to_xml_dom($data, $rootNodeName = 'data', $xml=null)
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1)
        {
            ini_set ('zend.ze1_compatibility_mode', 0);
        }

        if ($xml == null)
        {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
        }

        // loop through the data passed in.
        foreach($data as $key => $value)
        {
            // no numeric keys in our xml please!
            if (is_numeric($key))
            {
                // make string key...
                $key = "unknownNode_". (string) $key;
            }

            // replace anything not alpha numeric
            $key = preg_replace('/[^a-z]/i', '', $key);

            // if there is another array found recrusively call this function
            if (is_array($value))
            {
                $node = $xml->addChild($key);
                // recrusive call.
                self::to_xml_dom($value, $rootNodeName, $node);
            }
            else
            {
                // add single node.
                $value = htmlentities($value);
                $xml->addChild($key,$value);
            }

        }
        // pass back as string. or simple xml object if you want!
        return $xml;
    }
}