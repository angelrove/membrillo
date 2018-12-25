<?php
namespace angelrove\membrillo\WObjects\WTree;

interface iWTreeData
{
    function getCategorias($nivel, $id_padre);
    function tieneSubc($nivel, $id);
    function show_newSub(array $datos);
}
