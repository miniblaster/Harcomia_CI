<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class BiolinkTheme extends Model {

    public function get_biolinks_themes() {

        /* Get the user pixels */
        $biolinks_themes = [];

        /* Try to check if the user exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('biolinks_themes');

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $biolinks_themes_result = database()->query("SELECT * FROM `biolinks_themes` WHERE `is_enabled` = 1");
            while($row = $biolinks_themes_result->fetch_object()) {
                $row->settings = json_decode($row->settings);
                $biolinks_themes[$row->biolink_theme_id] = $row;
            }

            \Altum\Cache::$adapter->save(
                $cache_instance->set($biolinks_themes)->expiresAfter(CACHE_DEFAULT_SECONDS)
            );

        } else {

            /* Get cache */
            $biolinks_themes = $cache_instance->get();

        }

        return $biolinks_themes;

    }

}
