<?php

/**
 * @file plugins/generic/journalBadges/JournalBadgesPlugin.php
 *
 * Author  : Edi Suherlan
 * Company : audhighasu.com
 * Contact : audhighasu@gmail.com
 *
 * @class JournalBadgesPlugin
 * @brief Cross-theme accreditation / partner badge strip.
 */

namespace APP\plugins\generic\journalBadges;

use APP\core\Application;
use APP\template\TemplateManager;
use PKP\core\JSONMessage;
use PKP\core\PKPPageRouter;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

class JournalBadgesPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);
        if (!$success || Application::isUnderMaintenance()) {
            return $success;
        }

        if ($this->getEnabled($mainContextId)) {
            $this->addLocaleData();
            Hook::add('TemplateManager::display', $this->registerAssets(...));
            Hook::add('Templates::Common::Footer::PageFooter', $this->renderBadges(...));
        }

        return $success;
    }

    public function getDisplayName()
    {
        return __('plugins.generic.journalBadges.displayName');
    }

    public function getDescription()
    {
        return __('plugins.generic.journalBadges.description');
    }

    public function getActions($request, $verb)
    {
        $router = $request->getRouter();
        return array_merge(
            $this->getEnabled() ? [
                new LinkAction(
                    'settings',
                    new AjaxModal(
                        $router->url($request, null, null, 'manage', null, [
                            'verb' => 'settings',
                            'plugin' => $this->getName(),
                            'category' => 'generic',
                        ]),
                        $this->getDisplayName()
                    ),
                    __('manager.plugins.settings'),
                    null
                ),
            ] : [],
            parent::getActions($request, $verb)
        );
    }

    public function manage($args, $request)
    {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $context = $request->getContext();
                $templateMgr = TemplateManager::getManager($request);
                $templateMgr->registerPlugin('function', 'plugin_url', $this->smartyPluginUrl(...));

                $form = new JournalBadgesSettingsForm($this, $context ? $context->getId() : Application::CONTEXT_SITE);

                if ($request->getUserVar('save')) {
                    $form->readInputData();
                    if ($form->validate()) {
                        $form->execute();
                        return new JSONMessage(true);
                    }
                } else {
                    $form->initData();
                }
                return new JSONMessage(true, $form->fetch($request));
        }
        return parent::manage($args, $request);
    }

    /**
     * Default dummy badges for demos / first install.
     *
     * @return list<array{id:string,label:string,url:string,kind:string,note:string}>
     */
    public function getDummyBadges(): array
    {
        return [
            [
                'id' => 'sinta2',
                'label' => 'SINTA 2',
                'url' => 'https://sinta.kemdikbud.go.id/',
                'kind' => 'sinta',
                'note' => 'Akreditasi Sinta Peringkat 2 (contoh)',
            ],
            [
                'id' => 'doaj',
                'label' => 'DOAJ',
                'url' => 'https://doaj.org/',
                'kind' => 'doaj',
                'note' => 'Directory of Open Access Journals (contoh)',
            ],
            [
                'id' => 'crossref',
                'label' => 'Crossref',
                'url' => 'https://www.crossref.org/',
                'kind' => 'crossref',
                'note' => 'Anggota Crossref DOI (contoh)',
            ],
            [
                'id' => 'garuda',
                'label' => 'Garuda',
                'url' => 'https://garuda.kemdikbud.go.id/',
                'kind' => 'garuda',
                'note' => 'Garuda Kemdikbud (contoh)',
            ],
            [
                'id' => 'partner',
                'label' => 'Audhighasu Partner',
                'url' => 'https://audhighasu.com/',
                'kind' => 'partner',
                'note' => 'Mitra penerbitan (contoh)',
            ],
        ];
    }

    /**
     * @return list<array{id:string,label:string,url:string,kind:string,note:string}>
     */
    public function getBadges(int $contextId): array
    {
        $raw = $this->getSetting($contextId, 'badgesJson');
        if (!is_string($raw) || trim($raw) === '') {
            return $this->getDummyBadges();
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || $decoded === []) {
            return $this->getDummyBadges();
        }

        $badges = [];
        foreach ($decoded as $row) {
            if (!is_array($row)) {
                continue;
            }
            $label = trim((string) ($row['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $badges[] = [
                'id' => preg_replace('/[^a-z0-9_-]/i', '', (string) ($row['id'] ?? $label)) ?: 'badge',
                'label' => $label,
                'url' => trim((string) ($row['url'] ?? '')),
                'kind' => preg_replace('/[^a-z0-9_-]/i', '', strtolower((string) ($row['kind'] ?? 'custom'))) ?: 'custom',
                'note' => trim((string) ($row['note'] ?? '')),
            ];
        }

        return $badges !== [] ? $badges : $this->getDummyBadges();
    }

    public function ensureSeeded(int $contextId): void
    {
        $raw = $this->getSetting($contextId, 'badgesJson');
        if (is_string($raw) && trim($raw) !== '') {
            return;
        }
        $this->updateSetting(
            $contextId,
            'badgesJson',
            json_encode($this->getDummyBadges(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'string'
        );
        if ($this->getSetting($contextId, 'stripTitle') === null || $this->getSetting($contextId, 'stripTitle') === '') {
            $this->updateSetting(
                $contextId,
                'stripTitle',
                __('plugins.generic.journalBadges.settings.stripTitle.default'),
                'string'
            );
        }
        if ($this->getSetting($contextId, 'showFrontend') === null) {
            $this->updateSetting($contextId, 'showFrontend', true, 'bool');
        }
    }

    public function registerAssets(string $hookName, array $params): bool
    {
        $request = Application::get()->getRequest();
        $context = $request->getContext();
        if (!$context) {
            return false;
        }

        $router = $request->getRouter();
        if (!$router instanceof PKPPageRouter) {
            return false;
        }

        $contextId = (int) $context->getId();
        $this->ensureSeeded($contextId);

        if (!$this->getSetting($contextId, 'showFrontend')) {
            return false;
        }

        $badges = $this->getBadges($contextId);
        if ($badges === []) {
            return false;
        }

        $templateMgr = TemplateManager::getManager($request);
        $cssUrl = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/styles/badges.css?v=1.0.0';
        $templateMgr->addStyleSheet('journalBadges', $cssUrl, [
            'contexts' => ['frontend'],
        ]);

        return false;
    }

    public function renderBadges(string $hookName, array $params): bool
    {
        $output = &$params[2];

        $request = Application::get()->getRequest();
        $context = $request->getContext();
        if (!$context) {
            return false;
        }

        $contextId = (int) $context->getId();
        $this->ensureSeeded($contextId);

        if (!$this->getSetting($contextId, 'showFrontend')) {
            return false;
        }

        $badges = $this->getBadges($contextId);
        if ($badges === []) {
            return false;
        }

        $title = trim((string) $this->getSetting($contextId, 'stripTitle'));
        if ($title === '') {
            $title = (string) __('plugins.generic.journalBadges.settings.stripTitle.default');
        }

        $aria = htmlspecialchars((string) __('plugins.generic.journalBadges.aria'), ENT_QUOTES, 'UTF-8');
        $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        $items = '';
        foreach ($badges as $badge) {
            $kind = htmlspecialchars($badge['kind'], ENT_QUOTES, 'UTF-8');
            $label = htmlspecialchars($badge['label'], ENT_QUOTES, 'UTF-8');
            $note = htmlspecialchars($badge['note'], ENT_QUOTES, 'UTF-8');
            $mark = '<span class="jb-badge__mark" aria-hidden="true"></span><span class="jb-badge__label">' . $label . '</span>';
            if ($badge['url'] !== '') {
                $href = htmlspecialchars($badge['url'], ENT_QUOTES, 'UTF-8');
                $inner = '<a class="jb-badge jb-badge--' . $kind . '" href="' . $href . '" target="_blank" rel="noopener noreferrer" title="' . $note . '">' . $mark . '</a>';
            } else {
                $inner = '<span class="jb-badge jb-badge--' . $kind . '" title="' . $note . '">' . $mark . '</span>';
            }
            $items .= '<li class="jb-badges__item">' . $inner . '</li>';
        }

        $output .=
            '<section class="jb-badges" id="jbBadges" aria-label="' . $aria . '">' .
            '<div class="jb-badges__inner">' .
            ($titleEsc !== '' ? '<p class="jb-badges__title">' . $titleEsc . '</p>' : '') .
            '<ul class="jb-badges__list">' . $items . '</ul>' .
            '</div></section>' .
            '<script>(function(){var el=document.getElementById("jbBadges");if(!el)return;var f=document.querySelector(".pkp_structure_footer_wrapper");if(f&&f.parentNode){f.parentNode.insertBefore(el,f);}})();</script>';

        return false;
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\journalBadges\JournalBadgesPlugin', '\JournalBadgesPlugin');
}
