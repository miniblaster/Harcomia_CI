<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class PaymentProcessor extends Model {

    public function get_payment_processors_by_user_id($user_id) {

        /* Get the user payment_processors */
        $payment_processors = [];

        /* Try to check if the user exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('payment_processors?user_id=' . $user_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $payment_processors_result = database()->query("SELECT * FROM `payment_processors` WHERE `user_id` = {$user_id}");
            while($row = $payment_processors_result->fetch_object()) {
                $row->settings = json_decode($row->settings);
                $payment_processors[$row->payment_processor_id] = $row;
            }

            /* Properly tag the cache */
            $cache_instance->set($payment_processors)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('payment_processors?user_id=' . $user_id)->addTag('user_id=' . $user_id);

            foreach($payment_processors as $payment_processor) {
                $cache_instance->addTag('payment_processor_id=' . $payment_processor->payment_processor_id);
            }

            \Altum\Cache::$adapter->save($cache_instance);

        } else {

            /* Get cache */
            $payment_processors = $cache_instance->get();

        }

        return $payment_processors;

    }

    public function get_payment_processor_by_payment_processor_id($payment_processor_id) {

        /* Get the payment_processor */
        $payment_processor = null;

        /* Try to check if the payment_processor exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('payment_processor?payment_processor_id=' . $payment_processor_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $payment_processor = db()->where('payment_processor_id', $payment_processor_id)->getOne('payment_processors');

            if($payment_processor) {
                $payment_processor->settings = json_decode($payment_processor->settings);

                \Altum\Cache::$adapter->save(
                    $cache_instance->set($payment_processor)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('payment_processors?user_id=' . $payment_processor->user_id)->addTag('payment_processor_id=' . $payment_processor->payment_processor_id)->addTag('user_id=' . $payment_processor->user_id)
                );
            }

        } else {

            /* Get cache */
            $payment_processor = $cache_instance->get();

        }

        return $payment_processor;

    }

}
