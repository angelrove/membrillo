<?php
namespace angelrove\membrillo\WObjects\WList;

interface iWListRowEditor
{
    public function getBgColorAt($id, $idSelected, &$values);
    public function getClass($id, $idSelected, &$values);
}
