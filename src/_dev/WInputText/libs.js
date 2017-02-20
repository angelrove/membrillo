
//-------------------------------------------------------
function textarea_limite(zone, max) {
  if(zone.value.length >= max) {
    zone.value = zone.value.substring(0, max);
  }
}
//-------------------------------------------------------
