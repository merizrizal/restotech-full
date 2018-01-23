<?php
namespace restotech\full;

use Yii;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

	$modules = [];
        $modules['backend']['class'] = 'restotech\full\backend\BackendModule';
        $modules['frontend']['class'] = 'restotech\full\frontend\FrontendModule';
        $this->setModules($modules);
    }
}
