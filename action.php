<?php
/**
 * DokuWiki Plugin jquerymigrate (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Henry Pan <dokuwiki@phy25.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

class action_plugin_jquerymigrate extends DokuWiki_Action_Plugin
{

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     *
     * @return void
     */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('CONFUTIL_CDN_SELECT', 'AFTER', $this, 'handle_confutil_cdn_select');

    }

    /**
     * [Custom event handler which performs action]
     *
     * Called for event:
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     *
     * @return void
     */
    public function handle_confutil_cdn_select(Doku_Event $event, $param)
    {
        global $conf;
        $dev_string = $conf['compress'] ? '.min' : '';
        $version = $this->getConf('version');

        // remove bundled jqm so prevent conflict
        foreach ($event->data['src'] as $key => $value) {
            if (strstr($value, 'jquery-migrate') !== false) {
                unset($event->data['src'][$key]);
            }
        }

        // add our jqm
        if(!$conf['jquerycdn']) {
            $event->data['src'][] = sprintf(DOKU_BASE . 'lib/plugins/jquerymigrate/jquery-migrate-3.1.0%s.js', $dev_string);
        } elseif($conf['jquerycdn'] == 'jquery') {
            $event->data['src'][] = sprintf('https://code.jquery.com/jquery-migrate-%s%s.js', $version, $dev_string);
        } elseif($conf['jquerycdn'] == 'cdnjs') {
            $event->data['src'][] = sprintf(
                'https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/%s/jquery-migrate%s.js',
                $version, $dev_string
            );
        }
    }
}
