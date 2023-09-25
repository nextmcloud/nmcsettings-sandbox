<?php
declare(strict_types=1);

/** @var \OCP\IL10N $l */
/** @var array $_ */

?>

<div class="nmcsettings storage">
    <div class="personal-settings-account-container">
        <div class="personal-settings-container">
            <div class="personal-settings-setting-box quota-box">
                <h2><?php p($l->t('Storage utilisation')); ?></h2>
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
                <h2><?php p($l->t('Tariff information')); ?></h2>
                <div>
                    <strong><?php p($l->t('Your tariff')); ?></strong>:
                    <?php p($_['tariff']) ?>
                </div>
                <div>
                    <strong><?php p($l->t('Storage')); ?></strong>: 
                    <?php p($_['totalSpace']); ?>
                </div>
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
    </div>
</div>