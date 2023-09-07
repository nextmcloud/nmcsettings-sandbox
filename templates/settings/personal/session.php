<?php
declare(strict_types=1);

script('settings', 'vue-settings-personal-security');
?>

<div id="nmcsettings">

    <div class="section">

        <div class="personal-settings-account-container session">
            <div class="personal-settings-container clients-box">
                <div class="personal-settings-setting-box">
                    <h2>Mobile Applikationen</h2>
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
                    <h2>Desktop Software</h2>
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
                    <h2>WebDAV Adresse</h2>
                    <em>Mit der WebDAV Adresse können sie ihre MagentaCLOUD z.B. als Netzlaufwerk bei Windows einrichten. Weitere Information über WebDAV und wie Sie es nutzen können finden Sie <a href="https://cloud.telekom-dienste.de/hilfe#einrichten" target="_blank" rel="noreferrer noopener"><span>hier.</span></a></em>
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