<?php defined('ALTUMCODE') || die() ?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2 d-flex justify-content-center">
    <nft-card
            tokenAddress="<?= $data->link->settings->token_address ?>"
            tokenId="<?= $data->link->settings->token_id ?>"
            network="mainnet"
    >
    </nft-card>

    <?php if(!\Altum\Event::exists_content_type_key('javascript', 'opensea')): ?>
    <?php ob_start() ?>
        <script src="https://unpkg.com/embeddable-nfts/dist/nft-card.min.js"></script>
        <?php \Altum\Event::add_content(ob_get_clean(), 'javascript', 'opensea') ?>
    <?php endif ?>
</div>
