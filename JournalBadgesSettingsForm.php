<?php

/**
 * @file plugins/generic/journalBadges/JournalBadgesSettingsForm.php
 *
 * Author  : Edi Suherlan
 * Company : audhighasu.com
 * Contact : audhighasu@gmail.com
 *
 * @class JournalBadgesSettingsForm
 * @brief Settings form for Journal Badges plugin.
 */

namespace APP\plugins\generic\journalBadges;

use APP\template\TemplateManager;
use PKP\form\Form;

class JournalBadgesSettingsForm extends Form
{
    /** @var int */
    public $_contextId;

    /** @var JournalBadgesPlugin */
    public $_plugin;

    public function __construct(JournalBadgesPlugin $plugin, int $contextId)
    {
        $this->_contextId = $contextId;
        $this->_plugin = $plugin;

        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));

        $this->addCheck(new \PKP\form\validation\FormValidatorPost($this));
        $this->addCheck(new \PKP\form\validation\FormValidatorCSRF($this));
    }

    public function initData()
    {
        $this->_plugin->ensureSeeded($this->_contextId);
        $raw = $this->_plugin->getSetting($this->_contextId, 'badgesJson');
        if (!is_string($raw) || trim($raw) === '') {
            $raw = json_encode(
                $this->_plugin->getDummyBadges(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
        }

        $this->_data = [
            'showFrontend' => (bool) $this->_plugin->getSetting($this->_contextId, 'showFrontend'),
            'stripTitle' => (string) ($this->_plugin->getSetting($this->_contextId, 'stripTitle')
                ?: __('plugins.generic.journalBadges.settings.stripTitle.default')),
            'badgesJson' => $raw,
        ];
    }

    public function readInputData()
    {
        $this->readUserVars(['showFrontend', 'stripTitle', 'badgesJson', 'restoreDummy']);
    }

    public function fetch($request, $template = null, $display = false)
    {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->_plugin->getName());
        return parent::fetch($request, $template, $display);
    }

    public function execute(...$functionArgs)
    {
        if ($this->getData('restoreDummy')) {
            $json = json_encode(
                $this->_plugin->getDummyBadges(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
            $title = (string) __('plugins.generic.journalBadges.settings.stripTitle.default');
            $show = true;
        } else {
            $title = trim((string) $this->getData('stripTitle'));
            $show = (bool) $this->getData('showFrontend');
            $json = trim((string) $this->getData('badgesJson'));
            if ($json === '') {
                $json = json_encode(
                    $this->_plugin->getDummyBadges(),
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                );
            } else {
                $decoded = json_decode($json, true);
                if (!is_array($decoded)) {
                    $json = json_encode(
                        $this->_plugin->getDummyBadges(),
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                    );
                } else {
                    $json = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
            }
        }

        $this->_plugin->updateSetting($this->_contextId, 'showFrontend', $show, 'bool');
        $this->_plugin->updateSetting($this->_contextId, 'stripTitle', $title, 'string');
        $this->_plugin->updateSetting($this->_contextId, 'badgesJson', $json, 'string');

        parent::execute(...$functionArgs);
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\journalBadges\JournalBadgesSettingsForm', '\JournalBadgesSettingsForm');
}
