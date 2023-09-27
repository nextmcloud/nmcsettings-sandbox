<?php
declare(strict_types=1);

/** @var \OCP\IL10N $l */
/** @var array $_ */

script('settings', ['vue-settings-personal-info']);
?>

<div id="nmcsettings">
    <div class="section" data-lookup-server-upload-enabled="<?php p($_['lookupServerUploadEnabled'] ? 'true' : 'false') ?>">
        <div class="personal-settings-account-container">
            <h2><?php p($l->t('Account details')); ?></h2>
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

            <div id="personal-settings-avatar-container" class="personal-settings-container" style="display:none;">
                <div class="personal-settings-setting-box">
                    <div id="vue-avatar-section"></div>
                </div>
            </div>
        </div>
    </div>
</div>