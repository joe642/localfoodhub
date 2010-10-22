<?php
################################################################################
#                                                                              #
#		Filename:	product_pic.php				       #
#		Description:	upload and resize product images		       #
#		Calls:		config.php				       #
#									       #
#   Copyright 2010 Trellis Ltd
#
#   Licensed under the Apache License, Version 2.0 (the "License");
#   you may not use this file except in compliance with the License.
#   You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
#   Unless required by applicable law or agreed to in writing, software
#   distributed under the License is distributed on an "AS IS" BASIS,
#   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#   See the License for the specific language governing permissions and
#   limitations under the License.
#
################################################################################


$idir = "images/original/";   // Path To Images Directory
$tdir = "images/thumbs/";   // Path To Thumbnails Directory
$mdir = "images/normal/";   // Path To Normal size Directory


function save_product_pic($product_id = "0", $url = "", $file_temp = "", $file_type = "") {
$twidth = "150";   // Maximum Width For Thumbnail Images
$theight = "150";   // Maximum Height For Thumbnail Images
$mwidth = "500";   // Maximum Width For Main Images
$mheight = "500";   // Maximum Height For Main Images
global $idir, $tdir, $mdir;

// make sure the image folders exist
 if (!is_dir($idir)) mkdir($idir, 0777, true); 
 if (!is_dir($tdir)) mkdir($tdir, 0777, true); 
 if (!is_dir($mdir)) mkdir($mdir, 0777, true); 
 
 
 if ($url <> "" && ($file_type == "image/jpg" || $file_type == "image/jpeg" || $file_type == "image/pjpeg")) {
    $copy = copy($file_temp, "$idir" . $product_id . ".jpg");   // Move Image From Temporary Location To Permanent Location
    if ($copy) {   // If The Script Was Able To Copy The Image To It's Permanent Location
	// Make normal size image
	  $simg = imagecreatefromjpeg("$idir" . $product_id . ".jpg");   // Make A New Temporary Image To Create The Thumbanil From
      $currwidth = imagesx($simg);   // Current Image Width
      $currheight = imagesy($simg);   // Current Image Height
      if ($currheight > $currwidth) {   // If Height Is Greater Than Width
         $zoom = $mheight / $currheight;   // Length Ratio For Width
         $newheight = $mheight;   // Height Is Equal To Max Height
         $newwidth = $currwidth * $zoom;   // Creates The New Width
      } else {    // Otherwise, Assume Width Is Greater Than Height (Will Produce Same Result If Width Is Equal To Height)
        $zoom = $mwidth / $currwidth;   // Length Ratio For Height
        $newwidth = $mwidth;   // Width Is Equal To Max Width
        $newheight = $currheight * $zoom;   // Creates The New Height
      }
      $dimg = imagecreatetruecolor($newwidth, $newheight);   // Make New Image For Thumbnail
      imagecopyresampled($dimg, $simg, 0, 0, 0, 0, $newwidth, $newheight, $currwidth, $currheight);   // Copy Resized Image To The New Image (So We Can Save It)
      imagejpeg($dimg, "$mdir" . $product_id . ".jpg");   // Saving The main (or normal) Image
      imagedestroy($dimg);   // Destroying The Other Temporary Image

	
	// Make the thumbnail image
      if ($currheight > $currwidth) {   // If Height Is Greater Than Width
         $zoom = $theight / $currheight;   // Length Ratio For Width
         $newheight = $theight;   // Height Is Equal To Max Height
         $newwidth = $currwidth * $zoom;   // Creates The New Width
      } else {    // Otherwise, Assume Width Is Greater Than Height (Will Produce Same Result If Width Is Equal To Height)
        $zoom = $twidth / $currwidth;   // Length Ratio For Height
        $newwidth = $twidth;   // Width Is Equal To Max Width
        $newheight = $currheight * $zoom;   // Creates The New Height
      }
      $dimg = imagecreatetruecolor($newwidth, $newheight);   // Make New Image For Thumbnail
      imagecopyresampled($dimg, $simg, 0, 0, 0, 0, $newwidth, $newheight, $currwidth, $currheight);   // Copy Resized Image To The New Image (So We Can Save It)
      imagejpeg($dimg, "$tdir" . $product_id . ".jpg");   // Saving The Image
      imagedestroy($simg);   // Destroying The Temporary Image
      imagedestroy($dimg);   // Destroying The Other Temporary Image
	  return (1);
    } 
  }
  return (0);
}

function product_pic_thumbnail($product_id = "0") {
global $idir, $tdir, $mdir;
	return $tdir . $product_id . ".jpg";
}

 
?>
