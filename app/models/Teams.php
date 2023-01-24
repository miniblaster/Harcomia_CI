<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class Teams extends Model {

    public function get_team_by_team_id($team_id) {

        /* Get the team */
        $team = null;

        /* Try to check if the resource exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('team?team_id=' . $team_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $team = db()->where('team_id', $team_id)->getOne('teams');

            if($team) {
                \Altum\Cache::$adapter->save(
                    $cache_instance->set($team)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('user_id=' . $team->user_id)
                );
            }

        } else {

            /* Get cache */
            $team = $cache_instance->get();

        }

        return $team;

    }

}
