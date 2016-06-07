<?php

namespace Bolt\Extension\cdowdy\webfontloader;

use Bolt\Application;
use Bolt\Asset\File\JavaScript;
use Bolt\Asset\Snippet\Snippet;
use Bolt\Asset\Target;
use Bolt\Controller\Zone;
use Bolt\Extension\SimpleExtension;

/**
 * BoltResponsiveImages extension class.
 *
 * @author Cory Dowdy <cory@corydowdy.com>
 */
class WebFontLoaderExtension extends SimpleExtension
{

    private $_currentVersion = '1.6.24';
    private $_currentGoogleVersion = '1.6.16';
    private $_currentJSDeliver = '1.6.24';
    private $_currentCDNJS = '1.6.24';

    /**
     * {@inheritdoc}
     */
    protected function registerTwigPaths()
    {
        return ['templates'];
    }


    /**
     * {@inheritdoc}
     */
    protected function registerTwigFunctions()
    {
        $options = ['is_safe' => ['html']];

        return [
            'webfont' => ['webfont',  $options ]
        ];
    }


    /**
     * The callback function when {{ my_twig_function() }} is used in a template.
     *
     * @return string
     */
    public function webfont( $fontService = '', $font_family = array() )
    {


        $this->addAssets();

        $is_async = '';
        $asyncLoader = '';
        $custom_url = '';
        $font_deck_id = '';
        $project_id = '';
        $version = '';

        $app = $this->getContainer();

        $config = $this->getConfig();

        /*
		Determine if the script should be loaded async.
		If not declared in the config assume it's a regular render blocking script
		*/

        // if async is not an empty value ( true and not false or nothing entered )
        if (!empty ($config['async'])) {
            $is_async = $config['async'];

            // if use_cdn is not empty ( true and not false or nothing entered )
            if (!empty ($config['use_cdn'])) {
                $cdn = strtolower($config['cdn']);
                $asyncLoader = $this->whichCDN($cdn);

            } else { // if false or nothing is entered use the current webfont loader version
                $asyncLoader = $this->getBaseUrl() . 'assets/js/' . $this->_currentVersion . '/webfontloader.js';
            }
        } else { // all else fails fall back to regular webfont loader script insertion
            $this->webfontScript();
        }


        // Get the Font Service from the Config File. If no font is there throw an error
        if (isset($config['font_service'])) {
            $font_service = strtolower($config['font_service']);
        } else {
            return new \Twig_Markup('<b>No Font Service specified.</b>', 'UTF-8');
        }

        // custom url's for custom fonts:
        if (isset($config['custom_url'])) {
            $custom_url = $config['custom_url'];
        }

        $fonts = $this->getFontFamilies($font_family);

        // Font Deck config settings
        if (isset ($config['font_deck_id'])) {
            $font_deck_id = $config['font_deck_id'];
        }

        // Fonts.com config settings
        if (isset ($config['projectID'])) {
            $project_id = $config['projectID'];
        }

        if (isset ($config['version'])) {
            $version = $config['version'];
        }

        $context = [
            'is_async' => $is_async,
            'async_loader' => $asyncLoader,
            'font_service' => $font_service,
            'font_families' => $fonts,
            'custom_url' => $custom_url,
            'font_deck_id' => $font_deck_id,
            'project_id' => $project_id,
            'version' => $version
        ];

        $renderTemplate = $this->renderTemplate('font-config.twig', $context);

        return new \Twig_Markup($renderTemplate, 'UTF-8');
    }

    protected function whichCDN($cdn)
    {

        switch ($cdn) {
            case 'google':
                $cdnLoader = 'https://ajax.googleapis.com/ajax/libs/webfont/' . $this->_currentGoogleVersion . '/webfont.js';
                break;
            case 'jsdelivr':
                $cdnLoader = 'https://cdn.jsdelivr.net/webfontloader/' . $this->_currentJSDeliver . '/webfontloader.js';
                break;
            case 'cdnjs':
                $cdnLoader = 'https://cdnjs.cloudflare.com/ajax/libs/webfont/' . $this->_currentCDNJS . '/webfontloader.js';
                break;
            default:
                $cdnLoader = 'https://ajax.googleapis.com/ajax/libs/webfont/' . $this->_currentGoogleVersion . '/webfont.js';
        }

        return $cdnLoader;
    }


    function getFontFamilies($fontFamily)
    {
        $config = $this->getConfig();
        if (isset ($config['font_family'])) {

            $fontFamily = $config['font_family'];

            if (is_array($config['font_family'])) {

                $fontFamily = $config['font_family'];
            }
        }

        return $fontFamily;
    }

    /**
     * This may need some tinkering.
     */
    public function webfontScript()
    {
        $app = $this->getContainer();

        $config = $this->getConfig();
        $extPath = $app['resources']->getUrl('extensions');
        $vendor = 'vendor/cdowdy/';
        $extName = 'webfontloader/';


        // non cdn webfont script
        $webfontJS = $extPath . $vendor . $extName . 'js/' . $this->_currentVersion . '/webfontloader.js';
        $webfont = <<<WEBFONT
<script src="{$webfontJS}"></script>
WEBFONT;

        // cdn webfont script
        $cdn = $config['cdn'];
        $cdnLoader = $this->whichCDN($cdn);
        $cdnWebFont = <<<WEBFONT
<script src="{$cdnLoader}"></script>
WEBFONT;

        $asset = new Snippet();

        if (!empty ($config['use_cdn'])) {
            // insert snippet after the last CSS file in the head
            $asset->setCallback($cdnWebFont)
                ->setZone(ZONE::FRONTEND)
                ->setLocation(Target::AFTER_HEAD_CSS);

            $app['asset.queue.snippet']->add($asset);
        } else {
            // insert snippet after the last CSS file in the head
            $asset->setCallback($webfont)
                ->setZone(ZONE::FRONTEND)
                ->setLocation(Target::AFTER_HEAD_CSS);
            $app['asset.queue.snippet']->add($asset);
        }

    }

    
    public function isSafe()
    {
        return true;
    }


}
