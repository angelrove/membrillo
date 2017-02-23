<?
namespace angelrove\membrillo2\WObjects\WTree;


interface iWTreeData
{
  function getCategorias($nivel, $id_padre);
  function tieneSubc($nivel, $id);
  function show_newSub($datos);
}
