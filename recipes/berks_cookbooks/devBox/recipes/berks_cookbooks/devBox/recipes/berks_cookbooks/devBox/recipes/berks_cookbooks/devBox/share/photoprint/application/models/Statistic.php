<?php

class Statistic extends Model
{

    public static $_tableName = 'statistic';

    function getStatisticByDate($startDate, $endDate = null)
    {
        $endDate = ($endDate === null) ? $startDate : $endDate;
        $select = $this->select()
            ->where("date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'")
            ->order(array('date DESC', 'name DESC'))
            ->limit(700);
        return $this->fetchAll($select)->toArray();
    }

}