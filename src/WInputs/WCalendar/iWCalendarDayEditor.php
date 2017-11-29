<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 * 2006
 *
 */

namespace angelrove\membrillo2;


interface iWCalendarDayEditor
{
  public function getValue($numDiaSemana, $diasem, $numDias_mes, $year, $month, $day);
  public function getProperties($time, $year, $month, $day);
  /*
  public function getToolTip($time, $year, $month, $day);
  public function getClass($time, $year, $month, $day);
  public function getStyle($time, $year, $month, $day);*/
}
