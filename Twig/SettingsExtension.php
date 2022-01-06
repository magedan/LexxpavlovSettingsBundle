<?php

namespace Lexxpavlov\SettingsBundle\Twig;

use Lexxpavlov\SettingsBundle\Service\Settings;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsExtension extends AbstractExtension
{
    /** @var Settings */
    private $settings;

    /**
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function getName()
    {
        return 'lexxpavlov_settings_extension';
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('settings', array($this, 'getSettings')),
            new TwigFunction('settings_group', array($this, 'getSettingsGroup')),
        );
    }

    public function getSettings($name, $subname = null, $default = null)
    {
        return $this->settings->get($name, $subname, $default);
    }

    public function getSettingsGroup($name)
    {
        return $this->settings->group($name);
    }
}
