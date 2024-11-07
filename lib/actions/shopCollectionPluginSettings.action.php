<?php

class shopCollectionPluginSettingsAction extends waViewAction
{
    public function execute()
    {
        $this->view->assign('cron', '[путь до интерпретатора]php ' . wa()->getConfig()->getRootPath() . '/cli.php shop CollectionPluginProcess');
        $this->view->assign('settings', wa('shop')->getPlugin('collection')->getSettings());
    }
}