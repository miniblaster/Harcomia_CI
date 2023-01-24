<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class QrCode extends Model {

    public function delete($qr_code_id) {

        if(!$qr_code = db()->where('qr_code_id', $qr_code_id)->getOne('qr_codes', ['qr_code_id', 'qr_code', 'qr_code_logo'])) {
            return;
        }

        foreach(['qr_code', 'qr_code_logo'] as $image_key) {
            \Altum\Uploads::delete_uploaded_file($qr_code->{$image_key}, $image_key);
        }

        /* Delete from database */
        db()->where('qr_code_id', $qr_code_id)->delete('qr_codes');
    }
}
