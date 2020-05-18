<?php

namespace GiataHotels;

use DOMDocument;

class XmlToArray

{
    public static function convert($xml, $outputRoot = false)
    {
        $array = self::xmlStringToArray($xml);
        if (!$outputRoot && array_key_exists('@root', $array)) {
            unset($array['@root']);
        }
        return $array;
    }

    protected static function xmlStringToArray($xmlstr)
    {
        $doc = new DOMDocument();
        $doc->loadXML($xmlstr);
        $root = $doc->documentElement;
        $output = self::domNodeToArray($root);
        $output['@root'] = $root->tagName;
        return $output;
    }

    protected static function domNodeToArray($node)
    {
        $output = [];
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = self::domNodeToArray($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        if (!isset($output[$t])) {
                            $output[$t] = [];
                        }
                        $output[$t][] = $v;
                    } elseif ($v || $v === '0') {
                        $output = (string)$v;
                    }
                }
                if ($node->attributes->length && !is_array($output)) { // Has attributes but isn't an array
                    $output = ['value' => $output]; // Change output into an array.
                }
                if (is_array($output)) {
                    if ($node->attributes->length) {
                        $a = [];
                        foreach ($node->attributes as $attrName => $attrNode) {
//                            $a[$attrName] = (string) $attrNode->value;
                            $output[$attrName] = (string)$attrNode->value;
                        }
                    }
                    foreach ($output as $t => $v) {
                        $n = [];
                        if (is_array($v) && count($v) == 1 && $t != '@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }
        return $output;
    }

    public static function reformatHotel($hotel)
    {
        foreach ($hotel as $key => $value) {
            if (is_array($value))
                switch ($key) {
                    case 'ghgml':
                        $hotel[$key] = $value['href'];
                        break;
                    case 'city':
                        $hotel['cityId'] = $value['cityId'];
                        $hotel[$key] = $hotel[$key]['value'];
                        break;
                    case 'destination':
                        $hotel['destinationId'] = $value['destinationId'];
                        $hotel[$key] = $hotel[$key]['value'];
                        break;
                    case 'ratings':
                        $hotel[$key] = $hotel[$key]['rating'];
                        break;
                    case 'airports':
                        $hotel[$key] = $hotel[$key]['airport'];
                        break;
                    case 'alternativeNames':
                        $hotel[$key] = $hotel[$key]['alternativeName'];
                        break;
                    case 'addresses':
                        try {
                            $address = $value['address'];
                            $newAddress = [];
                            foreach ($address as $k => $v) {
                                if ($k == 'addressLine') {
                                    $newAddress[$k] = '';
                                    foreach ($address[$k] as $addressLine)
                                        if (isset($addressLine['value']))
                                            $newAddress[$k] = $newAddress[$k] ? ($newAddress[$k] . ', ' . $addressLine['value']) : $addressLine['value'];
                                } else
                                    $newAddress[$k] = $v;
                            }
                            $hotel['address'] = $newAddress;
                            unset($hotel[$key]);
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'phones':
                        try {
                            $phones = $faxes = [];
                            $phoneArr = $value['phone'];
                            if (isset($phoneArr[0]) && is_array($phoneArr[0])) {
                                foreach ($phoneArr as $phone) {
                                    if ($phone['tech'] == 'voice')
                                        $phones[] = $phone['value'];
                                    else if ($phone['tech'] == 'fax')
                                        $faxes[] = $phone['value'];
                                }
                            } else {
                                if ($phoneArr['tech'] == 'voice')
                                    $phones[] = $phoneArr['value'];
                                else if ($phoneArr['tech'] == 'fax')
                                    $faxes[] = $phoneArr['value'];
                            }
                            $hotel[$key] = $phones;
                            $hotel['faxes'] = $faxes;
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'emails':
                        try {
                            $emails = [];
                            $emailArr = $value['email'];
                            if (is_array($emailArr))
                                foreach ($emailArr as $email)
                                    $emails[] = $email;
                            else
                                $emails[] = $emailArr;
                            $hotel[$key] = $emails;
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'urls':
                        try {
                            $urls = [];
                            $urlArr = $value['url'];
                            if (is_array($urlArr))
                                foreach ($urlArr as $url)
                                    $urls[] = $url;
                            else
                                $urls[] = $urlArr;
                            $hotel[$key] = $urls;
                        } catch (\Exception $e) {
                            $e->getMessage();
                        }
                        break;
                    case 'chains':
                        $hotel[$key] = $hotel[$key]['chain'];
                        break;
                    case 'geoCodes':
                        $hotel[$key] = $hotel[$key]['geoCode'];
                        break;
                    case 'propertyCodes':
                        try {
                            $providersArr = $value['provider'];
                            $providers = [];
                            if (is_array($providersArr) && isset($providersArr[0])) {
                                foreach ($providersArr as $providerObj) {
                                    $provider = self::propertyCodes($providerObj);
                                    $providers[] = $provider;
                                }
                            } else {
                                $provider = self::propertyCodes($providersArr);
                                $providers[] = $provider;
                            }
                            $hotel[$key] = $providers;
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    default:
                        break;
                }
            else
                switch ($key) {
                    case 'lastUpdate':
                        $time = strtotime($value);
                        $dateInLocal = date("Y-m-d H:i:s", $time);
                        $hotel[$key] = $dateInLocal;
                        break;
                }
        }
        return $hotel;
    }

    protected static function propertyCodes($providerArr)
    {
        $provider = [];
        foreach ($providerArr as $k => $v) {
            if ($k == 'code') {
                $codes = [];
                foreach ($v as $i => $code)
                    $codes[] = isset($code['value']) ? $code['value'] : $code;
                $provider['codes'] = $codes;
            } else
                $provider[$k] = $v;
        }
        return $provider;
    }

    public static function reformatHotelTexts($texts)
    {
        $newTexts = [];
        $newTexts['giataId'] = $texts['giataId'];
        $newTexts['lang'] = $texts['texts']['text']['lang'];
        $newTexts['lastUpdate'] = $texts['texts']['text']['lastUpdate'];
        $newTexts['texts'] = $texts['texts']['text']['sections']['section'];
        return $newTexts;
    }

    public static function reformatHotelImages($images)
    {
        $newImages = [];
        $newImages['giataId'] = $images['giataId'];
        $urls = [];
        foreach ($images['images']['image'] as $sizes) {
            if (isset($sizes['sizes'])) {
                foreach ($sizes['sizes']['size'] as $size) {
                    if ($size['maxwidth'] < 1920) {
                        $urls[] = $size['href'];
                    }
                }
            } else {
                foreach ($images['images']['image']['sizes']['size'] as $s) {
                    if ($s['maxwidth'] < 1920) {
                        $urls[] = $s['href'];
                    }
                }
            }
        }
        $newImages['images'] = $urls;
        return $newImages;
    }

    public static function reformatProviderIds($hotel)
    {
        foreach ($hotel as $key => $value) {
            if (is_array($value))
                switch ($key) {
                    case 'code':
                        $codes = [];
                        foreach ($value as $code) {
                            $codes[] = isset($code['value']) ? $code['value'] : $code;
                        }
                        $hotel['code'] = $codes;
                        break;
                    default:
                        break;
                }
        }
        return $hotel;
    }
}
