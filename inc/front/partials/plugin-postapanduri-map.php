<div id="harta-pp" class="pp-container">
    <div class="pp-panel">
        <div class="pp-panel__header">
            <div id="pp-types">
                <?php if ($count_pp): ?>
                    <div class="<?php echo $show_pr_delivery_points ? 'pp-half-size' : '' ?> pp-types-holder">
                        <label class="radio-inline">
                            <input type="radio" name="dp_type" class="dp-type"
                                   value="1" <?php echo (isset($pp_type) && $pp_type == 1) || !isset($pp_type) ? 'checked' : '' ?>><img
                                    src="<?php echo $icon ?>"> <?php echo __('PostaPanduri Smartlocker', 'postapanduri') ?>
                        </label>
                    </div>
                <?php else: ?>
                    <?php echo __('No PostaPanduri delivery points', 'postapanduri') ?>
                <?php endif; ?>
                <?php if ($show_pr_delivery_points): ?>
                    <?php if ($count_pr): ?>
                        <div class="pp-half-size pp-types-holder">
                            <label class="radio-inline">
                                <input type="radio" name="dp_type" class="dp-type"
                                       value="0" <?php echo isset($pp_type) && $pp_type == 0 ? 'checked' : '' ?>><img
                                        src="<?php echo $icon_posta ?>"> <?php echo __('Posta Romana', 'postapanduri'); ?>
                            </label>
                        </div>
                    <?php else: ?>
                        <?php echo __('No Posta Romana delivery points', 'postapanduri') ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="pp-col">
                <div class="pp-form-group">
                    <select id="judete" class="pp-select2">
                        <option value="0" disabled selected><?php echo __('Choose county', 'postapanduri'); ?></option>
                    </select>
                </div>
            </div>
            <div class="pp-col">
                <div class="pp-form-group" style="display: none;">
                    <select id="localitati" class="pp-select2">
                    </select>
                </div>
            </div>
            <div class="pp-col">
                <div class="pp-form-group" style="display: none;">
                    <select id="pachetomate" class="pp-select2">
                    </select>
                </div>
            </div>
        </div>
        <div class="pp-panel__body">
            <a class="pp-map-toggle" href="#"><i
                        class="fas fa-map-marker"></i> <?php echo __('See map', 'postapanduri'); ?>
            </a>
            <div class="pp-map-wrap">
                <div class="pp-map">
                    <div class="pp-map__item">
                        <div class="pp-map__item" id="pp-map-canvas"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="page-loading" class="hidden">
    <div class="msg">
        <div class="c">
            <div class="icon"></div>
            <span class="muted" id="pl-msg">Vă rugăm să aşteptaţi, se încarcă datele</span>
        </div>
    </div>
</div>
