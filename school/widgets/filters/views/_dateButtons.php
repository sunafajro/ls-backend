<?php
/**
 * @var View  $this
 * @var array $item
 */
use school\widgets\filters\FiltersWidgetAsset;
use yii\web\View;

FiltersWidgetAsset::register($this);
?>
<div class="form-group js--change-dates-btn-block" data-start-date="<?= $item['dateStartClass'] ?>" data-end-date="<?= $item['dateEndClass'] ?>">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
            <button class="btn btn-xs js--change-dates-btn" data-type="previousWeek" type="button" style="margin-bottom:5px"><i class="fa fa-angle-double-left" aria-hidden="true"></i> -1</button>
            <button class="btn btn-xs js--change-dates-btn" data-type="currentWeek" type="button" style="margin-bottom:5px">текущая неделя</button>
            <button class="btn btn-xs js--change-dates-btn" data-type="nextWeek" type="button" style="margin-bottom:5px">+1 <i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
            <button class="btn btn-xs js--change-dates-btn" data-type="previousMonth" type="button" style="margin-bottom:5px"><i class="fa fa-angle-double-left" aria-hidden="true"></i> -1</button>
            <button class="btn btn-xs js--change-dates-btn" data-type="currentMonth" type="button" style="margin-bottom:5px">текущий месяц</button>
            <button class="btn btn-xs js--change-dates-btn" data-type="nextMonth" type="button" style="margin-bottom:5px">+1 <i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
        </div>
    </div>
</div>