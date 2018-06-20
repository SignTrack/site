<?php
echo getDistance(33.678639851675555,-111.84657096862793,33.6803004,-111.8330106);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function getDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $latDelta = $latTo - $latFrom;
  $lonDelta = $lonTo - $lonFrom;

  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
  return (round((($angle * $earthRadius)/1609.34)*10)/10).' mi';
}
?>