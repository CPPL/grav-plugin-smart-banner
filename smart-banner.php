<?php
namespace Grav\Plugin;

use Grav\Common\Page\Page;
use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class SmartBannerPlugin
 * @package Grav\Plugin
 */
class SmartBannerPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main event we are interested in
        $this->enable([
            'onPageInitialized' => ['onPageInitialized', 0]
        ]);
    }

    /**
     * Do some work for this event, full details of events can be found
     * on the learn site: http://learn.getgrav.org/plugins/event-hooks
     *
     * @param Event $e
     */
    public function onPageInitialized(Event $e)
    {
        /** Check if 'apple-itunes-app' tag is already set */
        $mtName = "apple-itunes-app";

        /** @var Page $page */
        $page = $e['page'];
        $meta = $page->metadata();

        /** Early return the tag is already set */
        if (isset($meta[$mtName])) {
            return;
        }

        /** Get the appID */
        $appID = trim($this->grav['config']->get('plugins.smart-banner.appID'));


        /** Early return if we don't at least have an appID */
        if (empty($appID)) {
            return;
        }

        /** Get the 'optional' affliate token */
        $token = trim($this->grav['config']->get('plugins.smart-banner.affiliateToken'));
        $affiliateDataString = empty($token) ? '' : ", affiliate-date=$token";

        /**Get the 'optional' app arguments */
        $deeplink = urlencode(trim($this->grav['config']->get('plugins.smart-banner.deeplink')));
        $argsString = empty($deeplink) ? '' : ", app-argument=$deeplink";

        /** Assemble the tag content attribute */
        $mtContent = "app-id=$appID$affiliateDataString$argsString";

        $meta[$mtName] = ['name' => $mtName, 'content' => $mtContent];

        $page->metadata($meta);
    }
}
