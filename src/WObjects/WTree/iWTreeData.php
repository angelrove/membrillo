<?
namespace angelrove\membrillo\WObjects\WTree;


interface iWTreeData
{
  function getCategorias($nivel, $id_padre);
  function tieneSubc($nivel, $id);
}
