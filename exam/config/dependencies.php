<?php

$container = \Yii::$container;

$container->set(
    exam\components\managers\interfaces\SpeakingExamManagerInterface::class,
    exam\components\managers\SpeakingExamManager::class
);
