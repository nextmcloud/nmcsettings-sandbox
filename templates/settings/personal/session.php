<?php
declare(strict_types=1);

script('settings', 'vue-settings-personal-security');
?>

<div id="nmcsettings">

    <div class="section">

        <div class="personal-settings-account-container session">
            <div class="personal-settings-container clients-box">
                <div class="personal-settings-setting-box">
                    <h2><?php p($l->t('Mobile applications')); ?></h2>
                    <a href="https://apps.apple.com/us/app/magentacloud-cloud-speicher/id312838242" rel="noreferrer noopener" target="_blank">
                        <img src="<?php p($appWebPath); ?>/img/iOS.svg" alt="iOS-App">
                    </a>
                    <a href="https://app.adjust.com/r4e1yl" rel="noreferrer noopener" target="_blank">
                        <img src="<?php p($appWebPath); ?>/img/Google-Play-Store.svg" alt="Android-App">
                    </a>
                </div>
            </div>
            
            <div class="personal-settings-container clients-box desktop">
                <div class="personal-settings-setting-box">
                    <h2><?php p($l->t('Desktop software')); ?></h2>
                    <a href="https://static.magentacloud.de/software/MagentaCLOUD.dmg" rel="noreferrer noopener" target="_blank">
                        <img src="<?php p($appWebPath); ?>/img/MacOS.svg" alt="Mac-Client">
                    </a>
                    <a href="https://static.magentacloud.de/software/MagentaCLOUD.exe" rel="noreferrer noopener" target="_blank">
                        <img src="<?php p($appWebPath); ?>/img/WinOS.svg" alt="Windows-Client">
                    </a>
                </div>
            </div>

            <div class="personal-settings-container webdav-box">
                <div class="personal-settings-setting-box">
                    <h2><?php p($l->t('WebDAV Address')); ?></h2>
                    <em><?php p($l->t('With the WebDAV address, you can set up your MagentaCLOUD as a network drive on Windows, for example. You can find more information about WebDAV and how to use it')); ?><a href="https://cloud.telekom-dienste.de/hilfe#einrichten" target="_blank" rel="noreferrer noopener"><span><?php p($l->t('here.')); ?></span></a></em>
                    <div id="webdav-url">
                        <input id="endpoint-url" type="text" value="https://magentacloud.de/remote.php/webdav" readonly>
                        <a class="button clipboardButton icon-clippy" data-clipboard-target="#endpoint-url" data-original-title="" title=""></a>
                    </div>
                </div>
            </div>

            <div class="personal-settings-container authtokens-box">
                <div class="personal-settings-setting-box">
                    <div id="security-authtokens" class="section"></div>
                </div>
            </div>
        </div>
    </div>
</div>