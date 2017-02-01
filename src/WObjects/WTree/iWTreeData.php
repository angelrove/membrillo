<?
namespace angelrove\membrillo2\WObjects\WTree;


interface iWTreeData
{
  function getCategorias($nivel, $id_padre);
  function tieneSubc($nivel, $id);
}
