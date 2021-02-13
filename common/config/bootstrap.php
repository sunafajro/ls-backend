<?php

Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@school', dirname(dirname(__DIR__)) . '/school');
Yii::setAlias('@client', dirname(dirname(__DIR__)) . '/client');
Yii::setAlias('@exam', dirname(dirname(__DIR__)) . '/exam');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
// data directory
Yii::setAlias('@data', dirname(dirname(__DIR__)) . '/data');