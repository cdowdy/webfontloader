<?php

namespace Bolt\Extension\cdowdy\webfontloader;

use Bolt\Application;
use Bolt\BaseExtension;


class Extension extends BaseExtension
{


    public function getName()
    {
        return "webfontloader";
    }


    public function initialize()
    {
        if ($this->app['config']->getWhichEnd() == 'frontend') {
            // Initialize the Twig function
            $this->addTwigFunction( 'webfont', 'twigWebfont' );
        }
    }

    public function twigWebfont( $font_service = '', $font_family = array() )
    {
        // load up twig template directory
        $this->app['twig.loader.filesystem']->addPath( __DIR__ . "/assets" );

        // Determine if the script should be loaded async. If not declared in the config assume it's a regular blocking
        // script
        if ( ! empty ( $this->config['async'] )) {
            $is_async = $this->config['async'];

            if ( ! empty ( $this->config['use_cdn'] )) {
                $asyncLoader = 'https://ajax.googleapis.com/ajax/libs/webfont/1.5.10/webfont.js';
            } else {
                $asyncLoader = $this->getBaseUrl() . 'assets/js/webfontloader.js';
            }
        } else {
            $this->webfontScript();
        }


        // Get the Font Service from the Config File. If no font is there throw an error
        if (isset( $this->config['font_service'] )) {
            $font_service = strtolower( $this->config['font_service'] );
        } else {
            return new \Twig_Markup( '<b>No Font Service specified.</b>', 'UTF-8' );
        }

        // custom url's for custom fonts:
        if (isset( $this->config['custom_url'] )) {
            $custom_url = $this->app['paths']['theme'] . $this->config['custom_url'];
        }

        if (isset ( $this->config['font_family'] )) {
            $font_family = $this->config['font_family'];
            if (is_array( $this->config['font_family'] )) {
                $font_family = $this->config['font_family'];
            }
        }

        // Font Deck config settings
        if (isset ( $this->config['font_deck_id'] )) {
            $font_deck_id = $this->config['font_deck_id'];
        }

        // Fonts.com config settings
        if (isset ( $this->config['projectID'] )) {
            $project_id = $this->config['projectID'];
        }

        if (isset ( $this->config['version'] )) {
            $version = $this->config['version'];
        }

        $script = $this->app['render']->render( 'font-config.twig', array(
            'is_async'      => $is_async,
            'async_loader'  => $asyncLoader,
            'font_service'  => $font_service,
            'font_families' => $font_family,
            'custom_url'    => $custom_url,
            'font_deck_id'  => $font_deck_id,
            'project_id'    => $project_id,
            'version'       => $version
        ) );

        return new \Twig_Markup( $script, 'UTF-8' );
    }

    /**
     *
     */
    public function webfontScript()
    {
        /**
         * since there is no head function or any reliable way to insert anything in to the head in Bolt we have to
         * hackishly insert our js into the head this way.
         *
         */

        $webfontJS = $this->getBaseUrl() . 'assets/js/webfontloader.js';
        $webfont   = <<<WEBFONT
<script src="{$webfontJS}"></script>
WEBFONT;
        // insert snippet after the last CSS file in the head
        $this->addSnippet( 'aftercss', $webfont );
    }

    protected function getDefaultConfig()
    {
        return array();
    }

}






