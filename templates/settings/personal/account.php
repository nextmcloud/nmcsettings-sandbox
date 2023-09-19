<?php
declare(strict_types=1);

/** @var \OCP\IL10N $l */
/** @var array $_ */

script('settings', ['vue-settings-personal-info']);
?>

<div id="nmcsettings">

    <div class="section" data-lookup-server-upload-enabled="<?php p($_['lookupServerUploadEnabled'] ? 'true' : 'false') ?>">
        <div class="personal-settings-account-container">
            <h3><?php p($l->t('Account details')); ?></h3>
            <div class="personal-settings-container name-box">
                <div class="personal-settings-setting-box">
                    <section>
                        <div class="headerbar-label">
                            <label for="displayName">
                                <?php p($l->t('Name')); ?>
                            </label>
                            <a href="#" class="federation-menu" aria-label="<?php p($l->t('Change privacy level of full name')); ?>">
                                <span class="icon-federation-menu icon-password">
                                    <span class="icon-triangle-s"></span>
                                </span>
                            </a>
                        </div>
                        <input type="text" id="displayName" name="displayName" read-only value="<?php p($_['displayName']['value']) ?>" autocomplete="on" autocapitalize="none" autocorrect="off" readonly/>
                        <span class="icon-checkmark hidden"></span>
                        <span class="icon-error hidden"></span>
                        <input type="hidden" id="displaynamescope" value="<?php p($_['displayName']['scope']) ?>">
                    </section>
                </div>
                <div class="personal-settings-setting-box language-box">
                    <div id="vue-language-section"></div>
                </div>
            </div>
            
            <div class="personal-settings-container">
                <div class="personal-settings-setting-box email-box">
                    <div id="vue-email-section"></div>
                </div>
            </div>

            <div class="personal-settings-container">
                <div class="personal-settings-setting-box password-box">
                    <section>
                        <p><?php p($l->t('You can add an alternative email address to receive your notifications there. It will also be used as an address for shared content. Your password can be changed in the')); ?>
                        <a href='https://account.idm.telekom.com/account-manager/index.xhtml' target='_blank'><?php p($l->t('login settings')); ?></a>
                        <?php p($l->t('for all Telekom services.')); ?></p>
                    </section>
                </div>
            </div>

            <div class="personal-settings-container">
                <div class="personal-settings-setting-box quota-box">
                    <h3><?php p($l->t('Storage utilisation')); ?></h3>
                    <div class="icon-quota">
                        <div class="quota-text">
                            <span class="usage-total">
                                <?php if ($_['quota'] === \OCP\Files\FileInfo::SPACE_UNLIMITED) : ?>
                                    <strong><?php p($_['usage']); ?></strong> <?php p($l->t('of')); ?> <?php p($_['totalSpace']); ?>
                                <?php else : ?>
                                    <strong><?php p($_['usage']); ?></strong> <?php p($l->t('of')); ?> <?php p($_['totalSpace']); ?>
                                <?php endif ?>
                            </span>
                            <span class="memory-occupied">
                                <?php p($l->t('Memory')); ?> <?php p($_['usageRelative']); ?>% <?php p($l->t('occupied')); ?>
                            </span>
                        </div>
                        <div class="settings-progress-bar">
                            <div class="progress-bar styledbar files-usage" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php isset($_['filesSizeInPer'])?p($_['filesSizeInPer']):""; ?>%;"></div>
                            <div class="progress-bar styledbar photos-usage" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php isset($_['photoVideoSizeInPer'])?p($_['photoVideoSizeInPer']):""; ?>%;"></div>
                            <div class="progress-bar styledbar bin-usage" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php isset($_['trashSizeInPer'])?p($_['trashSizeInPer']):""; ?>%;"></div>
                        </div>
                    </div>

                    <div class="usage-details">
                        <div>
                            <div id="files" class="files-usage"></div>
                            <?php p($l->t('Files')); ?>:<strong><?= $filesSize ?></strong>
                        </div>
                        <div>
                            <div id="photos" class="photos-usage"></div>
                            <?php p($l->t('Photos & videos')); ?>:<strong><?= $photoVideoSize ?></strong>
                        </div>
                        <div>
                            <div id="bin" class="bin-usage"></div>
                            <?php p($l->t('Recycle Bin')); ?>:<strong><?= $trashSize ?></strong>
                        </div>
                        <p class="recycle-paragraph">
                            <?php print_unescaped($l->t('The recycle bin is automatically tidied up.')); ?>
                        </p>
                        <p>
                            <?php print_unescaped($l->t('Files that have been in the recycle bin for longer than 30 days are automatically deleted permanently and free up storage space.')); ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="personal-settings-container">
                <div class="personal-settings-setting-box tariff-box">
                    <h3><?php p($l->t('Tariff information')); ?></h3>
                    <div>
                        <strong><?php p($l->t('Your tariff')); ?></strong>:
                        <?php p($_['tariff']) ?>
                    </div>
                    <strong><?php p($l->t('Storage')); ?></strong>: <?php p($_['totalSpace']); ?>
                </div>
            </div>

            <div class="personal-settings-container">
                <div class="personal-settings-setting-box">
                    <a href="https://cloud.telekom-dienste.de/tarife" target="_blank">
                        <button>
                            <?php print_unescaped($l->t('Expand storage')); ?>
                        </button>
                    </a>
                </div>
            </div>

            <div id="personal-settings-avatar-container" class="personal-settings-container" style="display:none;">
                <div class="personal-settings-setting-box">
                    <div id="vue-avatar-section"></div>
                </div>
            </div>
        </div>
    </div>
</div>