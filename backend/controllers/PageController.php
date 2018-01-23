<?php

namespace restotech\full\backend\controllers;

use Yii;


/**
 * Page controller
 */
class PageController extends \restotech\standard\backend\controllers\PageController {

    public function beforeAction($action) {

        if (parent::beforeAction($action)) {

            $this->setViewPath('@restotech/standard/backend/views/page');

            return true;
        } else {
            return false;
        }
    }
}
